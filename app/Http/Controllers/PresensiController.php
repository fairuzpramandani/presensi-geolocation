<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class PresensiController extends Controller
{
    public function create()
    {
        $hariini = date("Y-m-d");
        $email = Auth::guard('karyawan')->user()->email;
        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('email', $email)->count();
        return view('presensi.create', compact('cek'));
    }

    public function store(Request $request)
    {
        $email = Auth::guard('karyawan')->user()->email;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");
        $lokasi = $request->lokasi;

        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = $lokasiuser[0];
        $longitudeuser = $lokasiuser[1];

        $lokasi_kantor = [
            ['lat' => -7.34388593350558, 'long' => 112.73523239636584, 'nama' => 'Kantor Pusat CMS'],
            ['lat' => -7.344679449869948, 'long' => 112.73472526289694, 'nama' => 'Gerbang Tol Menanggal'],
            ['lat' => -7.342730715106749, 'long' => 112.75809102472155, 'nama' => 'Gerbang Tol Berbek 1'],
            ['lat' => -7.343185332266911, 'long' => 112.75237532014978, 'nama' => 'Gerbang Tol Berbek 2'],
            ['lat' => -7.3470567753921845, 'long' => 112.78926810447702, 'nama' => 'Gerbang Tol TambakSumur 1'],
            ['lat' => -7.346229427986888, 'long' => 112.78391129687654, 'nama' => 'Gerbang Tol TambakSumur 2'],
            ['lat' => -7.357726813064598, 'long' => 112.80496911781243, 'nama' => 'Gerbang Tol Juanda'],
            ['lat' => -7.497382601382557, 'long' => 112.72027988527945, 'nama' => 'Test'],
            ['lat' => -7.32031997825219, 'long' => 112.73802915918043, 'nama' => 'Test 1'],
        ];

        $lokasi_valid = false;
        $nama_lokasi = '';
        foreach ($lokasi_kantor as $kantor) {
            $jarak = $this->distance($kantor['lat'], $kantor['long'], $latitudeuser, $longitudeuser);
            $radius = round($jarak["meters"]);
            if ($radius <= 100) {
                $lokasi_valid = true;
                $nama_lokasi = $kantor['nama'];
                break;
            }
        }

        if (!$lokasi_valid) {
            echo "error|Maaf Anda Berada di Luar Radius Lokasi Kantor|";
            return;
        }

        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('email', $email)->count();
        if($cek > 0){
            $ket = "out";
        }else{
            $ket = "in";
        }
        $image = $request->image;
        $folderPath = "public/uploads/absen/";
        $formatName = $email . "-" . $tgl_presensi ."-". $ket;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;

        if ($cek > 0) {

            $jam_pulang_minimal = "17:00:00";
            if ($jam < $jam_pulang_minimal) {
                echo "error|Anda belum bisa absen pulang. Absen pulang dibuka mulai jam 17:00.|out";
                return;
            }
            $data_pulang = [
                'jam_out' => $jam,
                'foto_out' => $fileName,
                'location_out' => $lokasi,
            ];
            $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('email', $email)->update($data_pulang);

            if ($update) {
                echo "success|Anda Berhasil Absen Pulang dari $nama_lokasi|out";
                Storage::put($file, $image_base64);
            } else {
                echo "error|Maaf Anda Tidak Berhasil Absen|out";
            }

        } else {
            $jam_masuk_mulai = "07:40:00";
            if ($jam < $jam_masuk_mulai) {
                echo "error|Anda belum bisa absen masuk. Absen dibuka mulai jam 07:40.|in";
                return;
            }

            $jam_masuk_maksimal = "17:00:00";
            if ($jam > $jam_masuk_maksimal) {
                echo "error|Waktu absen masuk sudah habis. Anda tidak bisa absen masuk setelah jam 17:00.|in";
                return;
            }

            $data = [
                'email' => $email,
                'tgl_presensi' => $tgl_presensi,
                'jam_in' => $jam,
                'foto_in' => $fileName,
                'location_in' => $lokasi,
            ];
            $simpan = DB::table('presensi')->insert($data);
            if ($simpan) {
                echo "success|Anda Berhasil Absen Masuk di $nama_lokasi|in";
                Storage::put($file, $image_base64);
            } else {
                echo "error|Maaf Anda Tidak Berhasil Absen|in";
            }
        }
    }

    // Menghitung Jarak
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    public function editprofile()
    {
        $email = Auth::guard('karyawan')->user()->email;
        $karyawan = DB::table('karyawan')->where('email',$email)->first();
        return view('presensi.editprofile', compact('karyawan'));
    }

    public function updateprofile(Request $request){
        $email = Auth::guard('karyawan')->user()->email;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        $password = Hash::make($request->password);
        $karyawan = DB::table('karyawan')->where('email', $email)->first();
        if ($request->hasFile('foto')) {
            $safeEmail = str_replace(['@', '.'], '_', $email);
            $foto = $safeEmail . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $karyawan->foto;
        }

        if(empty($password)){
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'foto' => $foto
            ];
        }else{
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'password' => $password,
                'foto' => $foto
            ];
        }

        $update = DB::table('karyawan')->where('email', $email)->update($data);
        if($update){
            if($request->hasFile('foto')){
                $folderPath = "public/uploads/karyawan/";
                $request->file('foto')->storeAs($folderPath, $foto,);
            }
            return Redirect()->back()->with(['success' =>'Profil berhasil Di Update']);
        } else {
            return Redirect()->back()->with(['error' => 'Gagal Update Profil']);
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
        $email = Auth::guard('karyawan')->user()->email;
        $tgl_izin = $request->tgl_izin;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $data = [
            'email' => $email,
            'tgl_izin' => $tgl_izin,
            'status' => $status,
            'keterangan' => $keterangan
        ];

        $simpan = DB::table('pengajuan_izin')->insert($data);

        if($simpan){
            return redirect('/presensi/izin')->with(['success'=>'Data Berhasil Disimpan']);
        }else{
            return redirect('/presensi/izin')->with(['Error'=>'Data Gagal Disimpan']);
        }
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

    $lokasi_kantor = [
        ['lat' => -7.34388593350558, 'long' => 112.73523239636584, 'nama' => 'Kantor Pusat CMS', 'radius' => 100],
        ['lat' => -7.344679449869948, 'long' => 112.73472526289694, 'nama' => 'Gerbang Tol Menanggal', 'radius' => 100],
        ['lat' => -7.342730715106749, 'long' => 112.75809102472155, 'nama' => 'Gerbang Tol Berbek 1', 'radius' => 100],
        ['lat' => -7.343185332266911, 'long' => 112.75237532014978, 'nama' => 'Gerbang Tol Berbek 2', 'radius' => 100],
        ['lat' => -7.3470567753921845, 'long' => 112.78926810447702, 'nama' => 'Gerbang Tol TambakSumur 1', 'radius' => 100],
        ['lat' => -7.346229427986888, 'long' => 112.78391129687654, 'nama' => 'Gerbang Tol TambakSumur 2', 'radius' => 100],
        ['lat' => -7.357726813064598, 'long' => 112.80496911781243, 'nama' => 'Gerbang Tol Juanda', 'radius' => 100],
        ['lat' => -7.497382601382557, 'long' => 112.72027988527945, 'nama' => 'Test', 'radius' => 100],
        ['lat' => -7.270950525574215, 'long' => 112.74462413727203, 'nama' => 'Test 1', 'radius' => 100],
    ];
    foreach ($presensi as $item) {
        $nama_lokasi_ditemukan = "Luar Radius";

        if (!empty($item->location_in)) {
            $lokasi_karyawan = explode(",", $item->location_in);
            $lat_karyawan = $lokasi_karyawan[0];
            $long_karyawan = $lokasi_karyawan[1];

            foreach ($lokasi_kantor as $kantor) {
                $jarak = $this->distance($kantor['lat'], $kantor['long'], $lat_karyawan, $long_karyawan);
                $radius = round($jarak["meters"]);

                if ($radius <= $kantor['radius']) {
                    $nama_lokasi_ditemukan = $kantor['nama'];
                    break;
                }
            }
        }
        $item->nama_lokasi_in = $nama_lokasi_ditemukan;
    }
    return view('presensi.cetaklaporan', compact('bulan', 'tahun','namabulan','karyawan', 'presensi'));
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
        }

        return view('presensi.cetakrekap', [
            'rekap' => $rekap_final,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namabulan' => $namabulan_terpilih
        ]);
    }

}
