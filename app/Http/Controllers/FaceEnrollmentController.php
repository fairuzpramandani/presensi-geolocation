<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class FaceEnrollmentController extends Controller
{
    public function index()
    {
        $user = Auth::guard('karyawan')->user();
        if ($user->face_embedding) {
            return redirect('/dashboard')->with('success', 'Wajah Anda sudah terdaftar.');
        }
        return view('auth.face_enrollment');
    }

    public function store(Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $isApi = $request->wantsJson() || $request->bearerToken();

        if (!$user) {
            $token = $request->bearerToken();
            $user = DB::table('karyawan')->where('remember_token', $token)->first();
        }

        if (!$user) {
            return $isApi ? response()->json(['status' => 'error', 'message' => 'Unauthenticated.'], 401) : back();
        }

        $image_base64 = "";

        if ($request->hasFile('image')) {
            $image_base64 = file_get_contents($request->file('image')->getPathname());
        } else {
            if (!$request->image) {
                return $isApi ? response()->json(['status' => 'error', 'message' => 'Foto tidak ditemukan.']) : back();
            }
            $image_parts = explode(";base64,", $request->image);
            if (!isset($image_parts[1])) {
                return $isApi ? response()->json(['status' => 'error', 'message' => 'Format tidak valid.']) : back();
            }
            $image_base64 = base64_decode($image_parts[1]);
        }

        $tempFileName = 'face_check_' . time() . '.jpg';

        try {
            $response = Http::timeout(15)->attach('foto', $image_base64, $tempFileName)
                ->post('http://127.0.0.1:5000/validasi-wajah', [
                    'action' => 'register_center',
                    'email' => $user->email,
                    'nama' => $user->nama_lengkap
                ]);

            if ($response->failed()) return $isApi ? response()->json(['status' => 'error', 'message' => 'Gagal koneksi ke Python.']) : back();

            $hasil = $response->json();
            if (isset($hasil['status']) && $hasil['status'] == 'gagal') {
                return $isApi ? response()->json(['status' => 'error', 'message' => $hasil['pesan']]) : back();
            }

            if (isset($hasil['face_encoding'])) {
                $safeEmail = str_replace(['@', '.'], '_', $user->email);
                $namaFile = $safeEmail . '_validasi.jpg';
                Storage::disk('public')->put('uploads/karyawan/' . $namaFile, $image_base64);

                DB::table('karyawan')->where('email', $user->email)->update([
                    'face_embedding' => json_encode($hasil['face_encoding']),
                    'foto_wajah' => $namaFile
                ]);

                return $isApi ? response()->json(['status' => 'success', 'message' => 'Wajah berhasil didaftarkan!']) : redirect('/dashboard');
            }

            return $isApi ? response()->json(['status' => 'error', 'message' => 'Respon tidak valid dari server wajah.']) : back();

        } catch (\Exception $e) {
            return $isApi ? response()->json(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]) : back();
        }
    }
}
