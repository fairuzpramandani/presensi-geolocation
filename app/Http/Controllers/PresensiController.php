<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;


class PresensiController extends Controller
{
    public function create()
    {
        $hariini = date("Y-m-d");
        $email = Auth::guard('karyawan')->user()->email;
        $cek = DB::table('presensi')
            ->where('tgl_presensi', $hariini)
            ->where('email', $email)
            ->count();
        $lokasi_kantor_list = DB::table('konfigurasi_lokasi')->get();
        $karyawan = DB::table('karyawan')
            ->join('jam_kerja', 'karyawan.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('email', $email)
            ->first();

        if ($karyawan == null) {
            return redirect('/dashboard')->with(['warning' => 'Jadwal Kerja Anda belum diatur. Hubungi Admin.']);
        }
        return view('presensi.create', compact('cek', 'lokasi_kantor_list', 'karyawan'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $email = $user->email;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");
        
        // 1. Ambil Data Karyawan
        $karyawan = DB::table('karyawan')
                    ->join('jam_kerja', 'karyawan.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
                    ->where('email', $email)
                    ->first();
                    
        if ($karyawan == null) {
            return "error|Maaf, Jadwal jam kerja Anda belum diatur.|";
        }

        // --- VALIDASI WAJAH KETAT ---
        if (is_null($user->face_embedding)) {
            return "error|Anda belum melakukan Registrasi Wajah.|";
        }

        $image = $request->image;
        $image_parts = explode(";base64,", $image);
        $image_base64 = base64_decode($image_parts[1]);

        try {
            // Timeout 10 detik agar tidak loading selamanya
            $response = Http::timeout(10)->attach(
                'foto', $image_base64, 'check_absen.jpg'
            )->post('http://127.0.0.1:5000/validasi-wajah', [
                'action' => 'verify_face', // Perintah Verifikasi
                'target_embedding' => $user->face_embedding,
                'user_id' => $email
            ]);

            $hasil = $response->json();

            // Jika Python menolak (Wajah beda / Miring / Buram)
            if (isset($hasil['status']) && $hasil['status'] == 'gagal') {
                return "error|" . $hasil['pesan']; 
            }
        } catch (\Exception $e) {
            return "error|Gagal terhubung ke server validasi wajah.";
        }
        // --- SELESAI VALIDASI WAJAH ---

        // 2. Validasi Radius Lokasi
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];
        $lokasi_kantor_db = DB::table('konfigurasi_lokasi')->get();

        if ($lokasi_kantor_db->isEmpty()) return "error|Lokasi kantor belum diatur.|";

        $lokasi_valid = false;
        foreach ($lokasi_kantor_db as $kantor) {
            $koordinat_kantor_array = explode(',', $kantor->lokasi_kantor);
            $radius_db = $kantor->radius ?? 100;
            $jarak = $this->distance($koordinat_kantor_array[0], $koordinat_kantor_array[1], $latitudeuser, $longitudeuser);
            
            if (round($jarak["meters"]) <= $radius_db) {
                $lokasi_valid = true;
                break;
            }
        }

        if (!$lokasi_valid) return "error|Maaf Anda Berada di Luar Radius Lokasi Kantor|";

        // 3. Simpan Presensi
        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('email', $email)->count();
        $ket = request()->get('ket') ?? "in";
        $folderPath = "public/uploads/absen/";
        $fileName = $email . "-" . $tgl_presensi . "-" . $ket . ".png";
        $file = $folderPath . $fileName;

        if ($ket == "out") {
            if ($jam < $karyawan->jam_pulang) {
                return "error|Belum waktunya pulang. Jam pulang: " . $karyawan->jam_pulang . ".|out";
            }
            if ($cek > 0) {
                $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('email', $email)->update([
                    'jam_out' => $jam, 'foto_out' => $fileName, 'location_out' => $lokasi,
                ]);
                if($update) Storage::put($file, $image_base64);
                return $update ? "success|Berhasil Absen Pulang.|out" : "error|Gagal Absen Pulang.|out";
            } else {
                $simpan = DB::table('presensi')->insert([
                    'email' => $email, 'tgl_presensi' => $tgl_presensi, 'jam_in' => $jam, 'jam_out' => $jam,
                    'foto_out' => $fileName, 'location_out' => $lokasi, 'kode_jam_kerja' => $karyawan->kode_jam_kerja
                ]);
                if($simpan) Storage::put($file, $image_base64);
                return $simpan ? "success|Absen Pulang Berhasil.|out" : "error|Gagal Absen Pulang.|out";
            }
        } else {
            // UNTUK TESTING: Comment baris di bawah ini jika ingin absen jam berapa saja
            if ($jam < $karyawan->awal_jam_masuk) {
               return "error|Absen Masuk Belum Dibuka.|in";
            }
            
            $simpan = DB::table('presensi')->insert([
                'email' => $email, 'tgl_presensi' => $tgl_presensi, 'jam_in' => $jam,
                'foto_in' => $fileName, 'location_in' => $lokasi, 'kode_jam_kerja' => $karyawan->kode_jam_kerja
            ]);
            
            if($simpan) Storage::put($file, $image_base64);
            return $simpan ? "success|Selamat Bekerja! Absen Masuk Berhasil.|in" : "error|Gagal Absen Masuk.|in";
        }
    }

    public function getLokasiKantor()
    {
        $lokasi = DB::table('konfigurasi_lokasi')->get();
        return response()->json([
            'status' => 'success',
            'data' => $lokasi
        ]);
    }

    // Menghitung Jarak
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    public function getprofile($email)
    {
        $user = DB::table('karyawan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->where('karyawan.email', $email)
            ->select('karyawan.*', 'departemen.nama_dept')
            ->first();

        if ($user) {
            return response()->json([
                'status' => true,
                'data' => $user
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'User tidak ditemukan.'
        ], 404);
    }

    public function editprofile()
    {
        $email = Auth::guard('karyawan')->user()->email;
        $karyawan = DB::table('karyawan')->where('email',$email)->first();
        return view('presensi.editprofile', compact('karyawan'));
    }

    public function updateprofile(Request $request, $email = null)
    {
        $userEmail = $email ?? Auth::guard('karyawan')->user()->email;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        $password = $request->password;
        $karyawan = DB::table('karyawan')->where('email', $userEmail)->first();

        $data = [
            'nama_lengkap' => $nama_lengkap,
            'no_hp' => $no_hp,
        ];

        if (!empty($password)) {
            $data['password'] = Hash::make($password);
        }
        if ($request->hasFile('foto')) {
            $safeEmail = str_replace(['@', '.'], '_', $userEmail);
            $fotoName = $safeEmail . "_" . time() . "." . $request->file('foto')->getClientOriginalExtension();
            $folderPath = "uploads/karyawan/";
            $oldFile = "uploads/karyawan/" . $karyawan->foto;
            if($karyawan->foto && Storage::disk('public')->exists($oldFile)){
                Storage::disk('public')->delete($oldFile);
            }

            $request->file('foto')->storeAs($folderPath, $fotoName, 'public');
            $data['foto'] = $fotoName;
        }
        elseif (!empty($request->foto) && is_string($request->foto)) {
            $foto_base64 = $request->foto;
            if (str_contains($foto_base64, ',')) {
                $foto_base64 = explode(',', $foto_base64)[1];
            }

            $image = base64_decode($foto_base64);
            $safeEmail = str_replace(['@', '.'], '_', $userEmail);
            $fotoName = $safeEmail . "_" . time() . ".png";
            $folderPath = "uploads/karyawan/";
            $oldFile = "uploads/karyawan/" . $karyawan->foto;
            if ($karyawan->foto && Storage::disk('public')->exists($oldFile)) {
                Storage::disk('public')->delete($oldFile);
            }

            Storage::disk('public')->put($folderPath . $fotoName, $image);
            $data['foto'] = $fotoName;
        }

        $update = DB::table('karyawan')->where('email', $userEmail)->update($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Profil berhasil diperbarui'
            ], 200);
        }

        if ($update || isset($data['foto'])) {
            return redirect('/settings')->with(['success' => 'Profil berhasil Di Update']);
        } else {
            return redirect()->back()->with(['error' => 'Gagal Update Profil atau Tidak Ada Perubahan']);
        }
    }

    public function settings()
    {
        return view('presensi.settings');
    }

    public function histori()
    {
        $namabulan = ["","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember",];
        return view('presensi.histori' ,compact('namabulan'));
    }

    public function gethistori(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $email = Auth::guard('karyawan')->user()->email;

        $histori = DB::table('presensi')
            ->whereRaw('MONTH(tgl_presensi)="'.$bulan.'"')
            ->whereRaw('YEAR(tgl_presensi)="'.$tahun.'"')
            ->where('email', $email)
            ->orderBy('tgl_presensi')
            ->get();

        return view('presensi.gethistori', compact('histori'));
    }

    public function getHistoriApi(Request $request)
    {
        $token = $request->bearerToken();
        $user = DB::table('karyawan')->where('remember_token', $token)->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $histori = DB::table('presensi')
            ->whereRaw('MONTH(tgl_presensi) = ?', [$bulan])
            ->whereRaw('YEAR(tgl_presensi) = ?', [$tahun])
            ->where('email', $user->email)
            ->orderBy('tgl_presensi')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $histori
        ]);
    }

    public function izin()
    {
        $email = Auth::guard('karyawan')->user()->email;
        $dataizin = DB::table('pengajuan_izin')->where('email', $email)->get();
        return view('presensi.izin', compact('dataizin'));
    }

    public function buatizin()
    {
        return view('presensi.buatizin');
    }

    public function storeizin(Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        if (!$user) {
            $token = $request->bearerToken();
            $user = DB::table('karyawan')->where('remember_token', $token)->first();
        }
        if (!$user) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }
            return redirect('/')->with(['warning' => 'Anda harus login terlebih dahulu']);
        }

        $email = $user->email;
        $tgl_izin = $request->tgl_izin;
        $status = $request->status;
        $keterangan = $request->keterangan;
        $cek = DB::table('pengajuan_izin')
            ->where('email', $email)
            ->where('tgl_izin', $tgl_izin)
            ->count();

        if ($cek > 0) {
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah mengajukan pada tanggal ini!']);
            }
            return redirect('/presensi/izin')->with(['error' => 'Anda Sudah Melakukan Input Pengajuan Izin Pada Tanggal Tersebut !']);
        }
        $data = [
            'email' => $email,
            'tgl_izin' => $tgl_izin,
            'status' => $status,
            'keterangan' => $keterangan,
            'status_approved' => 0
        ];

        $simpan = DB::table('pengajuan_izin')->insert($data);
        if($simpan){
            if ($request->wantsJson()) {
                return response()->json(['status' => 'success', 'message' => 'Data Berhasil Disimpan']);
            }
            return redirect('/presensi/izin')->with(['success'=>'Data Berhasil Disimpan']);
        }else{
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Data Gagal Disimpan']);
            }
            return redirect('/presensi/izin')->with(['Error'=>'Data Gagal Disimpan']);
        }
    }

    public function getizin(Request $request)
    {
        $token = $request->bearerToken();
        $user = DB::table('karyawan')->where('remember_token', $token)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $dataizin = DB::table('pengajuan_izin')
            ->where('email', $user->email)
            ->orderBy('tgl_izin', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $dataizin
        ]);
    }

    public function monitoring()
    {
        return view('presensi.monitoring');
    }

    public function getpresensi(Request $request)
    {
        $tanggal = $request->tanggal;
        $presensi = DB::table ('presensi')
        ->select('presensi.*','nama_lengkap', 'nama_dept')
        ->join('karyawan', 'presensi.email', '=', 'karyawan.email')
        ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
        ->where('tgl_presensi', $tanggal)
        ->get();

        return view('presensi.getpresensi', compact('presensi'));
    }

    public function tampilkanpeta(Request $request)
    {
        $id = $request->id;
        $presensi = DB::table('presensi')
            ->join('karyawan', 'presensi.email', '=', 'karyawan.email')
            ->where('id', $id)
            ->first();
        return view('presensi.showmap', compact('presensi'));
    }

    public function laporan()
    {
        $namabulan = ["","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember",];
        $karyawan = DB::table('karyawan')->orderBy('nama_lengkap')->get();
        return view('presensi.laporan', compact('namabulan', 'karyawan'));
    }

    public function cetaklaporan(Request $request)
    {
        $email = $request->email;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember",];
        $namabulan_terpilih = $namabulan[$bulan] ?? '';

        if (empty($email)) {
            return redirect()->back()->with(['warning' => 'Silakan Pilih Karyawan Terlebih Dahulu']);
        }
        $karyawan = DB::table('karyawan')
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->where('email', $email)
            ->first();

        $presensi = DB::table('presensi')
            ->where('email', $email)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->orderBy('tgl_presensi')
            ->get();

        $lokasi_kantor_list = DB::table('konfigurasi_lokasi')->get();
        foreach ($presensi as $item) {
            $nama_lokasi_ditemukan = "Luar Radius";

            if (!empty($item->location_in)) {
                $lokasi_karyawan = explode(",", $item->location_in);
                $lat_karyawan = $lokasi_karyawan[0];
                $long_karyawan = $lokasi_karyawan[1];
                foreach ($lokasi_kantor_list as $kantor) {
                    $koordinat_kantor_array = explode(',', $kantor->lokasi_kantor);
                    $lat_kantor = $koordinat_kantor_array[0];
                    $long_kantor = $koordinat_kantor_array[1];
                    $radius_db = $kantor->radius;
                    $jarak = $this->distance($lat_kantor, $long_kantor, $lat_karyawan, $long_karyawan);
                    $radius = round($jarak["meters"]);

                    if ($radius <= $radius_db) {
                        $nama_lokasi_ditemukan = $kantor->nama_lokasi;
                        break;
                    }
                }
            }
            $item->nama_lokasi_in = $nama_lokasi_ditemukan;
        }

        if (isset($_POST['exportexcel'])) {
            $time = date("d-M-Y H:i:s");
            $filename = "Laporan Presensi Karyawan $time.xls";
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=\"$filename\"");
            return view('presensi.cetaklaporanexcel', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'presensi'));

        } else {
            return view('presensi.cetaklaporan', compact('bulan', 'tahun', 'namabulan', 'karyawan', 'presensi'));
        }
    }

    public function rekap()
    {
        $namabulan = ["","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember",];
        return view('presensi.rekap', compact('namabulan'));
    }

   public function cetakrekap(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $namabulan_terpilih = $namabulan[$bulan] ?? '';
        $batas_telat_sql = "08:00:00";
        $semua_karyawan = DB::table('karyawan')
            ->select('email', 'nama_lengkap')
            ->orderBy('nama_lengkap')
            ->get();
        $rekap_presensi = DB::table('presensi')
            ->selectRaw("presensi.email, karyawan.nama_lengkap,
                MAX(IF(DAY(tgl_presensi) = 1, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_1,
                MAX(IF(DAY(tgl_presensi) = 2, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_2,
                MAX(IF(DAY(tgl_presensi) = 3, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_3,
                MAX(IF(DAY(tgl_presensi) = 4, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_4,
                MAX(IF(DAY(tgl_presensi) = 5, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_5,
                MAX(IF(DAY(tgl_presensi) = 6, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_6,
                MAX(IF(DAY(tgl_presensi) = 7, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_7,
                MAX(IF(DAY(tgl_presensi) = 8, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_8,
                MAX(IF(DAY(tgl_presensi) = 9, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_9,
                MAX(IF(DAY(tgl_presensi) = 10, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_10,
                MAX(IF(DAY(tgl_presensi) = 11, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_11,
                MAX(IF(DAY(tgl_presensi) = 12, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_12,
                MAX(IF(DAY(tgl_presensi) = 13, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_13,
                MAX(IF(DAY(tgl_presensi) = 14, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_14,
                MAX(IF(DAY(tgl_presensi) = 15, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_15,
                MAX(IF(DAY(tgl_presensi) = 16, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_16,
                MAX(IF(DAY(tgl_presensi) = 17, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_17,
                MAX(IF(DAY(tgl_presensi) = 18, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_18,
                MAX(IF(DAY(tgl_presensi) = 19, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_19,
                MAX(IF(DAY(tgl_presensi) = 20, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_20,
                MAX(IF(DAY(tgl_presensi) = 21, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_21,
                MAX(IF(DAY(tgl_presensi) = 22, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_22,
                MAX(IF(DAY(tgl_presensi) = 23, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_23,
                MAX(IF(DAY(tgl_presensi) = 24, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_24,
                MAX(IF(DAY(tgl_presensi) = 25, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_25,
                MAX(IF(DAY(tgl_presensi) = 26, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_26,
                MAX(IF(DAY(tgl_presensi) = 27, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_27,
                MAX(IF(DAY(tgl_presensi) = 28, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_28,
                MAX(IF(DAY(tgl_presensi) = 29, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_29,
                MAX(IF(DAY(tgl_presensi) = 30, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_30,
                MAX(IF(DAY(tgl_presensi) = 31, CONCAT(TIME_FORMAT(jam_in, '%H:%i'), '-', IFNULL(TIME_FORMAT(jam_out, '%H:%i'), '00:00')), '')) as tgl_31,

                /* === INI TAMBAHAN BARU === */
                COUNT(presensi.jam_in) as total_hadir,
                SUM(IF(presensi.jam_in > '$batas_telat_sql', 1, 0)) as total_terlambat
                /* ======================== */
            ")
            ->join('karyawan', 'presensi.email', '=', 'karyawan.email')
            ->whereRaw('MONTH(tgl_presensi) = ?', [$bulan])
            ->whereRaw('YEAR(tgl_presensi) = ?', [$tahun])
            ->groupByRaw('presensi.email, karyawan.nama_lengkap')
            ->get()
            ->keyBy('email');


        $rekap_final = [];

        foreach ($semua_karyawan as $karyawan) {
            if (isset($rekap_presensi[$karyawan->email])) {
                $rekap_final[] = $rekap_presensi[$karyawan->email];
            } else {
                $data_kosong = new \stdClass();
                $data_kosong->email = $karyawan->email;
                $data_kosong->nama_lengkap = $karyawan->nama_lengkap;

                for ($i = 1; $i <= 31; $i++) {
                    $tgl = 'tgl_' . $i;
                    $data_kosong->$tgl = '';
                }

                $data_kosong->total_hadir = 0;
                $data_kosong->total_terlambat = 0;
                $rekap_final[] = $data_kosong;
            }
                if (isset($_POST['exportexcel'])) {
                $time = date("d-M-Y H:i:s");
                header("Content-type: application/vnd-ms-excel");
                header("Content-Disposition: attachment; filename=Presensi Karyawan $time.xls");
            }
        }
        return view('presensi.cetakrekap', [
            'rekap' => $rekap_final,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namabulan' => $namabulan_terpilih
        ]);
    }

    public function izinsakit(Request $request)
    {
        $dari = $request->dari;
        $sampai = $request->sampai;
        $email_karyawan = $request->email;
        $nama_karyawan = $request->nama_karyawan;
        $status_approved = $request->status_approved;

       $query = DB::table('pengajuan_izin')
        ->select('pengajuan_izin.*', 'karyawan.nama_lengkap', 'karyawan.jabatan')
        ->join('karyawan', 'pengajuan_izin.email', '=', 'karyawan.email');

        if (!empty($dari) && !empty($sampai)) {
            $query->whereBetween('tgl_izin', [$dari, $sampai]);
        } elseif (!empty($dari)) {
            $query->where('tgl_izin', '>=', $dari);
        } elseif (!empty($sampai)) {
            $query->where('tgl_izin', '<=', $sampai);
        }
        if (!empty($email_karyawan)) {
            $query->where('pengajuan_izin.email', $email_karyawan);
        }
        if (!empty($nama_karyawan)) {
            $query->where('karyawan.nama_lengkap', 'like', '%' . $nama_karyawan . '%');
        }
        if ($status_approved !== null && $status_approved !== '') {
            $query->where('status_approved', $status_approved);
        }
        $query->orderBy('tgl_izin', 'asc');
        $izinsakit = $query->paginate(10);
        return view('presensi.izinsakit', compact(
            'izinsakit',
            'dari',
            'sampai',
            'email_karyawan',
            'nama_karyawan',
            'status_approved'
        ));
    }

    public function approvedizinsakit(Request $request)
    {
        $status_approved = $request->status_approved;
        $id_izinsakit_form = $request->id_izinsakit_form;

        if (empty($id_izinsakit_form)) {
            return redirect()->back()->with(['warning' => 'ID Pengajuan tidak ditemukan.']);
        }

        if (!in_array($status_approved, ['1', '2'])) {
            return redirect()->back()->with(['warning' => 'Status persetujuan tidak valid.']);
        }
        try {
            $update = DB::table('pengajuan_izin')
                ->where('id', $id_izinsakit_form)
                ->update([
                    'status_approved' => $status_approved
                ]);
            if ($update) {
                return redirect()->back()->with(['success' => 'Status berhasil diubah!']);
            } else {
                return redirect()->back()->with(['warning' => 'Status tidak diubah karena nilainya sama.']);
            }

        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => 'Gagal mengupdate status. Terjadi kesalahan database.']);
        }
    }

    public function batalkanizinsakit($id)
    {
        $update = DB::table('pengajuan_izin')->where('id', $id)->update([
            'status_approved' => 0
        ]);
        if($update){
            return Redirect::back()->with(['success'=>'Data Berhasil Di Update']);
        }else {
            return Redirect::back()->with(['Error'=>'Data Gagal Di Update']);
        }
    }
    public function cekPengajuanIzin(Request $request)
    {
        $tgl_izin = $request->tgl_izin;
        $email_karyawan = Auth::guard('karyawan')->user()->email;
        $cek = DB::table('pengajuan_izin')
            ->where('email', $email_karyawan)
            ->where('tgl_izin', $tgl_izin)
            ->count();
        return $cek;
    }

}
