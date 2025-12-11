<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class KonfigurasiController extends Controller
{

    public function lokasikantor(Request $request)
    {
        $nama_lokasi = $request->nama_lokasi;
        $query = DB::table('konfigurasi_lokasi');
        if (!empty($nama_lokasi)) {
            $query->where('nama_lokasi', 'like', '%' . $nama_lokasi . '%');
        }
        $lokasi_kantor_list = $query->get();
        return view('konfigurasi.lokasikantor', compact('lokasi_kantor_list'));
    }
    public function storeLokasiKantor(Request $request)
    {
        $koordinat_kantor = $request->lokasi_kantor;
        $radius = $request->radius;
        $nama_lokasi = $request->nama_lokasi;
        $koordinat_array = explode(',', $koordinat_kantor);
        $latitude = trim($koordinat_array[0] ?? null);

        $request->validate([
            'lokasi_kantor' => 'required',
            'radius' => 'required|numeric',
            'nama_lokasi' => 'required|string|max:100',
        ]);

        $data = [
            'lokasi_kantor' => $koordinat_kantor,
            'radius' => $radius,
            'nama_lokasi' => $nama_lokasi,
        ];

        try {
            DB::table('konfigurasi_lokasi')->insert($data);
            return back()->with('success', 'Lokasi baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('warning', 'Gagal menambahkan lokasi. Error: ' . $e->getMessage());
        }
    }
    public function editLokasi($id)
    {
        $lokasi = DB::table('konfigurasi_lokasi')->where('id', $id)->first();
        return view('konfigurasi.editlokasikantor', compact('lokasi'));
    }
    public function updateLokasiKantor(Request $request, $id)
    {
        $koordinat_kantor = $request->lokasi_kantor;
        $radius = $request->radius;
        $nama_lokasi = $request->nama_lokasi;

        $koordinat_array = explode(',', $koordinat_kantor);
        $latitude = trim($koordinat_array[0] ?? null);

        $request->validate([
            'lokasi_kantor' => 'required',
            'radius' => 'required|numeric',
            'nama_lokasi' => 'required|string|max:100',
        ]);

        $data = [
            'lokasi_kantor' => $koordinat_kantor,
            'radius' => $radius,
            'nama_lokasi' => $nama_lokasi,
        ];

        try {
            DB::table('konfigurasi_lokasi')->where('id', $id)->update($data);
            return back()->with('success', 'Konfigurasi lokasi berhasil diupdate!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate konfigurasi lokasi. Error: ' . $e->getMessage());
        }
    }
    public function deleteLokasi($id)
    {
        try {
            DB::table('konfigurasi_lokasi')->where('id', $id)->delete();
            return back()->with('success', 'Lokasi berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('warning', 'Gagal menghapus lokasi. Error: ' . $e->getMessage());
        }
    }

    public function jamkerja()
    {
        $jam_kerja = DB::table('jam_kerja')->orderBy('kode_jam_kerja')->get();
        return view('konfigurasi.jamkerja',compact('jam_kerja'));
    }

    public function storejamkerja(Request $request)
    {
        $kode_jam_kerja = $request->kode_jam_kerja;
        $nama_jam_kerja = $request->nama_jam_kerja;
        $awal_jam_masuk = $request->awal_jam_masuk;
        $jam_masuk = $request->jam_masuk;
        $akhir_jam_masuk = $request->akhir_jam_masuk;
        $jam_pulang = $request->jam_pulang;

        $data = [
            'kode_jam_kerja' => $kode_jam_kerja,
            'nama_jam_kerja' => $nama_jam_kerja,
            'awal_jam_masuk' => $awal_jam_masuk,
            'jam_masuk' => $jam_masuk,
            'akhir_jam_masuk' => $akhir_jam_masuk,
            'jam_pulang' => $jam_pulang
        ];
        try {
            DB::table('jam_kerja')->insert($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        }
    }
    public function editjamkerja(Request $request)
    {
        $kode_jam_kerja = $request->kode_jam_kerja;
        $jamkerja = DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->first();
        return view('konfigurasi.editjamkerja', compact('jamkerja'));
    }
    public function deletejamkerja($kode_jam_kerja)
    {
        $hapus = DB::table('jam_kerja')->where('kode_jam_kerja', $kode_jam_kerja)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Berhasil Di Hapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Di Hapus']);
        }
    }
    public function updatejamkerja(Request $request)
    {
        $kode_jam_kerja = $request->kode_jam_kerja;
        $nama_jam_kerja = $request->nama_jam_kerja;
        $awal_jam_masuk = $request->awal_jam_masuk;
        $jam_masuk = $request->jam_masuk;
        $akhir_jam_masuk = $request->akhir_jam_masuk;
        $jam_pulang = $request->jam_pulang;
        $data = [
            'nama_jam_kerja' => $nama_jam_kerja,
            'awal_jam_masuk' => $awal_jam_masuk,
            'jam_masuk' => $jam_masuk,
            'akhir_jam_masuk' => $akhir_jam_masuk,
            'jam_pulang' => $jam_pulang
        ];
        try {
            DB::table('jam_kerja')
                ->where('kode_jam_kerja', $kode_jam_kerja)
                ->update($data);
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

}
