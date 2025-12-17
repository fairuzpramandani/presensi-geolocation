<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak terautentikasi.',
            ], 401);
        }

        $data = $user->only([
            'email',
            'nama_lengkap',
            'jabatan',
            'no_hp',
            'kode_dept',
            'foto'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data profile berhasil diambil.',
            'user' => $data
        ], 200);
    }
    public function update(Request $request)
    {
        $karyawan = Auth::guard('karyawan')->user();

        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'no_hp' => 'required|string|max:15',
            'password' => 'nullable|string|min:5|confirmed',
            'foto' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Input tidak valid.',
                'errors'  => $validator->errors()
            ], 422);
        }

        $karyawan->nama_lengkap = $request->nama_lengkap;
        $karyawan->no_hp = $request->no_hp;

        if ($request->filled('password')) {
            $karyawan->password = Hash::make($request->password);
        }

        if ($request->foto) {
            $folder = "public/uploads/karyawan/";
            $base64String = $request->foto;
            $oldFile = $karyawan->foto;
            $base64String = preg_replace('/^data:image\/\w+;base64,/', '', $base64String);
            $base64String = str_replace(' ', '+', $base64String);
            if ($oldFile && Storage::exists($folder . $oldFile)) {
                Storage::delete($folder . $oldFile);
            }
            $fileName = "foto_" . time() . ".webp";
            $fileData = base64_decode($base64String);

            if ($fileData === false) {
                 return response()->json([
                     'status' => 'error',
                     'message' => 'Gagal menguraikan data foto Base64. String mungkin korup.'
                 ], 422);
            }

            Storage::put($folder . $fileName, $fileData);
            $karyawan->foto = $fileName;
        }
        $karyawan->save();
        $updatedData = $karyawan->only([
            'email', 'nama_lengkap', 'jabatan', 'no_hp', 'kode_dept', 'foto'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui.',
            'data' => $updatedData
        ], 200);
    }
}
