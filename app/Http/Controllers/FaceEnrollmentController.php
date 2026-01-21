<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        $request->validate([
            'image' => 'required',
        ]);

        $user = Auth::guard('karyawan')->user();
        $image_parts = explode(";base64,", $request->image);

        if (!isset($image_parts[1])) {
            return back()->with('error', 'Format foto tidak valid.');
        }

        $image_base64 = base64_decode($image_parts[1]);
        $tempFileName = 'face_check_' . time() . '.jpg';

        try {
            $response = Http::attach(
                'foto', $image_base64, $tempFileName
            )->post(env('PYTHON_API_URL', 'http://127.0.0.1:5000') . '/validasi-wajah');

            if ($response->failed()) {
                return back()->with('error', 'Gagal terhubung ke server Python.');
            }

            $hasil = $response->json();
            if (isset($hasil['status']) && $hasil['status'] == 'gagal') {
                return back()->with('error', $hasil['pesan']);
            }
            if (isset($hasil['face_encoding'])) {
                $safeEmail = str_replace(['@', '.'], '_', $user->email);
                $namaFile = $safeEmail . '_validasi.jpg';
                Storage::disk('public')->put('uploads/karyawan/' . $namaFile, $image_base64);
                $user->face_embedding = json_encode($hasil['face_encoding']);
                $user->foto_wajah = $namaFile;
                $user->save();
                return redirect('/dashboard')->with('success', 'Wajah berhasil didaftarkan! Selamat Datang.');
            } else {
                return back()->with('error', 'Respon tidak valid dari sistem.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Error Sistem: ' . $e->getMessage());
        }
    }
}
