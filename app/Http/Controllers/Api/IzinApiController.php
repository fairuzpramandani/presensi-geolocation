<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class IzinApiController extends Controller
{
    // Ambil daftar izin user (opsional)
    public function index()
    {
        $izin = DB::table('presensi_izin')
            ->where('id_karyawan', Auth::user()->id_karyawan)
            ->orderBy('tanggal_izin', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $izin
        ], 200);
    }

    // Simpan pengajuan izin
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_izin' => 'required|date',
            'keterangan' => 'required|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        $filePath = null;
        if ($request->hasFile('foto')) {
            $filePath = $request->file('foto')->store('izin', 'public');
        }

        DB::table('presensi_izin')->insert([
            'id_karyawan' => Auth::user()->id_karyawan,
            'tanggal_izin' => $request->tanggal_izin,
            'keterangan' => $request->keterangan,
            'foto' => $filePath,
            'status' => 'pending',
            'created_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan izin berhasil dikirim.'
        ], 201);
    }
}
