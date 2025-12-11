<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KonfigurasiLokasi;

class LokasiApiController extends Controller
{
    public function index()
    {
        $lokasi = KonfigurasiLokasi::select('id', 'nama_lokasi', 'latitude', 'longitude', 'radius')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Data lokasi presensi.',
            'data' => $lokasi
        ]);
    }
}
