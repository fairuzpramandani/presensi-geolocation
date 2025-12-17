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
        ->selectRaw('COUNT(email) as jmlhadir, SUM(IF(jam_in > "08.00",1,0)) as jmlterlambat')
        ->where('tgl_presensi', $hariini)
        ->first();

        $rekapizin = DB::table('pengajuan_izin')
        ->selectRaw('SUM(IF(status="izin",1,0)) as jmlizin, SUM(IF(status="sakit",1,0)) as jmlsakit')
        ->where('tgl_izin', $hariini)
        ->where('status_approved', 1)
        ->first();
        return view ('dashboard.dashboardadmin', compact('rekappresensi','rekapizin'));
    }


}
