<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PresensiApiController extends Controller
{
    // Menghitung jarak (meter)
    public function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles) * 60 * 1.1515;
        $meters = ($miles * 1.609344) * 1000;

        return $meters;
    }

    // Cek apakah sudah absen + ambil lokasi kantor
    public function create()
    {
        $hariini = date("Y-m-d");
        $email = Auth::user()->email;

        $cekAbsen = DB::table('presensi')
            ->where('tgl_presensi', $hariini)
            ->where('email', $email)
            ->count();

        $lokasi_kantor = DB::table('konfigurasi_lokasi')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'sudah_absen' => $cekAbsen > 0,
                'lokasi_kantor_list' => $lokasi_kantor
            ]
        ]);
    }

    // Absen Masuk & Pulang
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lokasi' => 'required',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lokasi dan Foto wajib diisi.',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $today = now()->format('Y-m-d');
        $currentTime = now()->format('H:i:s');

        // Cek lokasi user
        $locationCheck = $this->checkUserLocation($request->lokasi);
        if (!$locationCheck['is_valid']) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda berada di luar radius lokasi kantor yang diizinkan.'
            ], 403);
        }
        $nama_lokasi = $locationCheck['location_name'];

        // Cek presensi hari ini
        $presensiHariIni = DB::table('presensi')
            ->where('tgl_presensi', $today)
            ->where('email', $user->email)
            ->first();

        // Simpan file foto
        $fileName = $this->saveImage($request->image, $user->email, $today, $presensiHariIni ? 'out' : 'in');
        if (!$fileName) {
            return response()->json(['status' => 'error', 'message' => 'Format gambar tidak valid.'], 422);
        }

        // Logika Absen Pulang
        if ($presensiHariIni) {
            // Asumsi jam pulang dari konfigurasi, default 17:00
            $jamPulang = DB::table('konfigurasi_jam')->value('jam_pulang') ?? '17:00:00';
            if ($currentTime < $jamPulang) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Absen pulang hanya bisa dilakukan setelah jam " . substr($jamPulang, 0, 5)
                ], 403);
            }

            DB::table('presensi')
                ->where('id', $presensiHariIni->id)
                ->update([
                    'jam_out' => $currentTime,
                    'foto_out' => $fileName,
                    'location_out' => $request->lokasi
                ]);

            return response()->json([
                'status' => 'success',
                'message' => "Absen pulang berhasil dari $nama_lokasi"
            ]);
        }

        // Logika Absen Masuk
        $jamMasuk = DB::table('konfigurasi_jam')->value('jam_masuk') ?? '07:40:00';
        $batasTelat = DB::table('konfigurasi_jam')->value('batas_telat') ?? '08:00:00';

        if ($currentTime < $jamMasuk) {
            return response()->json([
                'status' => 'error',
                'message' => "Absen masuk dibuka mulai jam " . substr($jamMasuk, 0, 5)
            ], 403);
        }

        $msg = ($currentTime > $batasTelat)
            ? "Anda terlambat, tetapi absen masuk berhasil di $nama_lokasi"
            : "Absen masuk berhasil di $nama_lokasi";

        DB::table('presensi')->insert([
            'email' => $user->email,
            'tgl_presensi' => $today,
            'jam_in' => $currentTime,
            'foto_in' => $fileName,
            'location_in' => $request->lokasi
        ]);

        return response()->json([
            'status' => 'success',
            'message' => $msg
        ]);
    }

    private function checkUserLocation($userLocation)
    {
        [$latUser, $longUser] = explode(",", $userLocation);
        $officeLocations = DB::table('konfigurasi_lokasi')->get();

        if ($officeLocations->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lokasi kantor belum diatur.'
            ], 500);
        }

        foreach ($officeLocations as $location) {
            [$latKantor, $longKantor] = explode(",", $location->lokasi_kantor);
            $radius = $location->radius ?? 100; // Default radius 100m
            $distance = $this->distance($latKantor, $longKantor, $latUser, $longUser);

            if ($distance <= $radius) {
                return ['is_valid' => true, 'location_name' => $location->nama_lokasi];
            }
        }

        return ['is_valid' => false, 'location_name' => null];
    }

    private function saveImage($imageBase64, $email, $date, $type)
    {
        $imageData = explode(";base64,", $imageBase64)[1] ?? null;
        if (!$imageData) return null;

        $safeEmail = str_replace(['@', '.'], '_', $email);
        $fileName = "$safeEmail-$date-$type.png";
        $filePath = "public/uploads/absen/$fileName";

        Storage::put($filePath, base64_decode($imageData));
        return $fileName;
    }

    public function getLokasiKantor()
    {
        $lokasiKantor = DB::table('lokasi_kantor')->get();

        return response()->json([
            'status' => 'success',
            'data' => $lokasiKantor
        ]);
    }

    // Histori presensi
    public function histori(Request $request)
    {
        $email = Auth::user()->email;
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');

        $histori = DB::table('presensi')
            ->whereMonth('tgl_presensi', $bulan)
            ->whereYear('tgl_presensi', $tahun)
            ->where('email', $email)
            ->orderBy('tgl_presensi')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $histori
        ]);
    }
    private function checkMultiLokasi($lat, $long)
    {
        $lokasi = \App\Models\KonfigurasiLokasi::all();

        foreach ($lokasi as $l) {
            $jarak = $this->haversine($lat, $long, $l->latitude, $l->longitude);

            if ($jarak <= $l->radius) {
                return [
                    "valid" => true,
                    "lokasi" => $l
                ];
            }
        }

        return ["valid" => false];
    }

    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c * 1000;
    }

    public function settings()
    {
        return response()->json(['status' => 'success']);
    }
}
