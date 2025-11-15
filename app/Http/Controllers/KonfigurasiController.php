<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KonfigurasiController extends Controller
{
    public function lokasikantor()
    {
        $lokasi_kantor = DB::table('konfigurasi_lokasi')->where('id', 1)->first();
        return view('konfigurasi.lokasikantor', compact('lokasi_kantor'));
    }

    public function updateLokasiKantor(Request $request)
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
            'latitude' => $latitude,
            'lokasi_kantor' => $koordinat_kantor,
            'radius' => $radius,
            'nama_lokasi' => $nama_lokasi,
        ];

        try {
            DB::table('konfigurasi_lokasi')->where('id', 1)->update($data);
            return back()->with('success', 'Konfigurasi lokasi kantor berhasil diupdate!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate konfigurasi lokasi. Error: ' . $e->getMessage());
        }
    }
}
