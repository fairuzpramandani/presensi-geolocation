<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index()
    {
        $hariini = date("Y-m-d");
        $bulanini = date("m") * 1;
        $tahunini = date("Y");
        $email = Auth::guard('karyawan')->user()->email;

        $jam_kerja_user = DB::table('karyawan')
            ->join('jam_kerja', 'karyawan.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('email', $email)
            ->first();
        $jam_masuk_standar = $jam_kerja_user->jam_masuk ?? "07:00:00";

        $presensihariini = DB::table('presensi')->where('email',$email)->where('tgl_presensi', $hariini)->first();

        $historibulanini = DB::table('presensi')
            ->where('email', $email)
            ->whereRaw('MONTH(tgl_presensi)="'.$bulanini.'"')
            ->whereRaw('YEAR(tgl_presensi)="' .$tahunini. '"')
            ->orderBy('tgl_presensi')
            ->get();
        $rekappresensi = DB::table('presensi')
            ->selectRaw('COUNT(email) as jmlhadir, SUM(IF(jam_in > "' . $jam_masuk_standar . '",1,0)) as jmlterlambat')
            ->where('email',$email)
            ->whereRaw('MONTH(tgl_presensi)="'.$bulanini.'"')
            ->whereRaw('YEAR(tgl_presensi)="' .$tahunini. '"')
            ->first();

        $leaderboard = DB::table('presensi')
            ->join('karyawan', 'presensi.email', '=', 'karyawan.email')
            ->where('tgl_presensi', $hariini)
            ->orderBy('jam_in')
            ->get();

        $namabulan = ["","Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];

        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="izin",1,0)) as jmlizin, SUM(IF(status="sakit",1,0)) as jmlsakit')
            ->where('email',$email)
            ->whereRaw('MONTH(tgl_izin)="'.$bulanini.'"')
            ->whereRaw('YEAR(tgl_izin)="' .$tahunini. '"')
            ->where('status_approved', 1)
            ->first();

        return view('dashboard.dashboard', compact('presensihariini', 'historibulanini','namabulan','bulanini','tahunini','rekappresensi','leaderboard', 'rekapizin','jam_kerja_user'));
    }

    public function dashboardadmin()
    {
        $hariini = date("Y-m-d");

        $rekappresensi = DB::table('presensi')
            ->selectRaw('COUNT(email) as jmlhadir, SUM(IF(jam_in > "08:00:00",1,0)) as jmlterlambat')
            ->where('tgl_presensi', $hariini)
            ->first();

        $rekapizin = DB::table('pengajuan_izin')
            ->selectRaw('SUM(IF(status="izin",1,0)) as jmlizin, SUM(IF(status="sakit",1,0)) as jmlsakit')
            ->where('tgl_izin', $hariini)
            ->where('status_approved', 1)
            ->first();

        $izin_pending = DB::table('pengajuan_izin')
            ->join('karyawan', 'pengajuan_izin.email', '=', 'karyawan.email')
            ->where('status_approved', 0)
            ->get();

        $riwayat_hari_ini = DB::table('presensi')
            ->join('karyawan', 'presensi.email', '=', 'karyawan.email')
            ->where('tgl_presensi', $hariini)
            ->orderBy('jam_in', 'desc')
            ->get();

        // ==============================================================
        // LOGIKA BARU: MENCARI NAMA LOKASI BERDASARKAN KOORDINAT ABSEN
        // ==============================================================
        $konfigurasi_lokasi = DB::table('konfigurasi_lokasi')->get();

        foreach ($riwayat_hari_ini as $riwayat) {
            $riwayat->nama_lokasi_absen = "Lokasi Tidak Dikenali";

            if ($riwayat->location_in) {
                $koordinat_absen = explode(',', $riwayat->location_in);

                if (count($koordinat_absen) == 2) {
                    $lat_absen = (float) trim($koordinat_absen[0]);
                    $lon_absen = (float) trim($koordinat_absen[1]);

                    $jarak_terdekat = 999999;

                    foreach ($konfigurasi_lokasi as $lokasi) {
                        $koordinat_kantor = explode(',', $lokasi->lokasi_kantor);

                        if (count($koordinat_kantor) == 2) {
                            $lat_kantor = (float) trim($koordinat_kantor[0]);
                            $lon_kantor = (float) trim($koordinat_kantor[1]);

                            $earthRadius = 6371000;
                            $latDelta = deg2rad($lat_kantor - $lat_absen);
                            $lonDelta = deg2rad($lon_kantor - $lon_absen);
                            $a = sin($latDelta / 2) * sin($latDelta / 2) +
                                 cos(deg2rad($lat_absen)) * cos(deg2rad($lat_kantor)) *
                                 sin($lonDelta / 2) * sin($lonDelta / 2);
                            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                            $distance = $earthRadius * $c;

                            if ($distance < $jarak_terdekat) {
                                $jarak_terdekat = $distance;
                                // MENGGUNAKAN $lokasi->radius, BUKAN radius_meter
                                if ($distance <= ($lokasi->radius + 20)) {
                                    $riwayat->nama_lokasi_absen = $lokasi->nama_lokasi;
                                } else {
                                    $riwayat->nama_lokasi_absen = $lokasi->nama_lokasi . " (Di luar radius)";
                                }
                            }
                        }
                    }
                }
            }
        }

        return view ('dashboard.dashboardadmin', compact('rekappresensi','rekapizin', 'izin_pending', 'riwayat_hari_ini'));
    }

}
