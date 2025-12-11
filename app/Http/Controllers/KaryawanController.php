<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::query();
        $query->select('karyawan.*', 'nama_dept', 'nama_jam_kerja');
        $query->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $query->leftJoin('jam_kerja', 'karyawan.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja');

        $query->orderBy('nama_lengkap');
        if (!empty($request->nama_karyawan)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_karyawan . '%');
        }

        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }

        $karyawan = $query->paginate(10);

        $departemen = DB::table('departemen')->get();
        $jam_kerja = DB::table('jam_kerja')->orderBy('nama_jam_kerja')->get();

        return view('karyawan.index', compact('karyawan', 'departemen', 'jam_kerja'));
    }

    public function store(Request $request)
    {
        $email = $request->email;
        $nama_lengkap = $request->nama_lengkap;
        $jabatan = $request->jabatan;
        $no_hp = $request->no_hp;
        $kode_dept = $request->kode_dept;
        $kode_jam_kerja = $request->kode_jam_kerja;

        $nama_parts = explode(' ', $nama_lengkap);
        $nama_pertama = strtolower($nama_parts[0]);
        $no_hp_terakhir = substr($no_hp, -4);
        $password_plain_text = $nama_pertama . $no_hp_terakhir;
        $password = Hash::make($password_plain_text);

        $cek_karyawan = DB::table('karyawan')->where('email', $email)->first();
        if ($cek_karyawan) {
            return redirect()->back()->with(['warning' => 'Email ' . $email . ' sudah terdaftar.']);
        }

        if ($request->hasFile('foto')) {
            $safeEmail = str_replace(['@', '.'], '_', $email);
            $foto = $safeEmail . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = null;
        }

        try {
            $data = [
                'email' => $email,
                'nama_lengkap' => $nama_lengkap,
                'jabatan' => $jabatan,
                'no_hp' => $no_hp,
                'kode_dept' => $kode_dept,
                'kode_jam_kerja' => $kode_jam_kerja,
                'foto' => $foto,
                'password' => $password
            ];
            $simpan = DB::table('karyawan')->insert($data);
            if ($simpan) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/karyawan/";
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return redirect()->back()->with(['success' => 'Data Berhasil DiSimpan']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with(['warning' => 'Data Tidak Berhasil DiSimpan']);
        }
    }

    public function edit(Request $request)
    {
        $email = $request->email;
        $departemen = DB::table('departemen')->get();
        $jam_kerja = DB::table('jam_kerja')->orderBy('nama_jam_kerja')->get();
        $karyawan = DB::table('karyawan')->where('email', $email)->first();

        return view('karyawan.edit', compact('departemen', 'karyawan', 'jam_kerja'));
    }

    public function update($email, Request $request)
    {
        $nama_lengkap = $request->nama_lengkap;
        $jabatan = $request->jabatan;
        $no_hp = $request->no_hp;
        $kode_dept = $request->kode_dept;
        $kode_jam_kerja = $request->kode_jam_kerja;
        $password_baru = $request->password;

        $karyawan = DB::table('karyawan')->where('email', $email)->first();

        $data = [
            'nama_lengkap' => $nama_lengkap,
            'jabatan' => $jabatan,
            'no_hp' => $no_hp,
            'kode_dept' => $kode_dept,
            'kode_jam_kerja' => $kode_jam_kerja,
        ];

        if ($request->hasFile('foto')) {
            $safeEmail = str_replace(['@', '.'], '_', $email);
            $foto = $safeEmail . "." . $request->file('foto')->getClientOriginalExtension();

            $path_foto_lama = "public/uploads/karyawan/" . $karyawan->foto;
            if ($karyawan->foto != null) {
                Storage::delete($path_foto_lama);
            }

            $folderPath = "public/uploads/karyawan/";
            $request->file('foto')->storeAs($folderPath, $foto);

            $data['foto'] = $foto;
        }

        if (!empty($password_baru)) {
            $data['password'] = Hash::make($password_baru);
        }

        try {
            $update = DB::table('karyawan')->where('email', $email)->update($data);

            if ($update) {
                return redirect('/karyawan')->with(['success' => 'Data Berhasil DiUpdate']);
            } else {
                return redirect()->back()->with(['warning' => 'Tidak Ada Perubahan Data']);
            }

        } catch (\Exception $e) {
            return redirect()->back()->with(['warning' => 'Data Tidak Berhasil DiUpdate, terjadi error.']);
        }
    }

    public function delete($email)
    {
        $delete = DB::table('karyawan')->where('email', $email)->delete();
        if ($delete) {
            return redirect()->back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return redirect()->back()->with(['warning' => 'Data Tidak Berhasil Dihapus']);
        }
    }
}
