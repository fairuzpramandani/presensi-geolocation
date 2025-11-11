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
            ['lat' => -7.263797, 'long' => 112.737429, 'nama' => 'Test 1'],
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
}
