<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FaceEnrollmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->face_embedding) {
            return redirect('/dashboard')->with('success', 'Wajah Anda sudah terdaftar.');
        }
        return view('auth.face_enrollment');
    }

    public function store(Request $request)
    {
        // 1. Cek apakah ada data gambar dari Webcam
        $request->validate([
            'image' => 'required',
        ]);

        $user = Auth::user();

        // 2. PROSES DECODE (Ubah Teks Base64 Menjadi File Gambar)
        // Format asli: "data:image/jpeg;base64,/9j/4AAQSk..."
        // Kita buang bagian depannya ("data:image/jpeg;base64,")
        $image_parts = explode(";base64,", $request->image);

        if (!isset($image_parts[1])) {
            return back()->with('error', 'Format foto tidak valid.');
        }

        $image_base64 = base64_decode($image_parts[1]);
        $fileName = 'face_' . time() . '.jpg'; // Nama file bohongan

        try {
            // 3. KIRIM KE PYTHON SEBAGAI FILE (Multipart)
            // 'foto' harus sama dengan request.files['foto'] di Python
            $response = Http::attach(
                'foto', $image_base64, $fileName
            )->post(env('PYTHON_API_URL') . '/validasi-wajah');

            // Cek apakah Python mati
            if ($response->failed()) {
                return back()->with('error', 'Gagal terhubung ke server Python. Pastikan app.py berjalan.');
            }

            $hasil = $response->json();

            // 4. CEK RESPON PYTHON
            if (isset($hasil['status']) && $hasil['status'] == 'gagal') {
                return back()->with('error', $hasil['pesan']); // Misal: Blur atau Wajah ganda
            }

            // 5. SUKSES -> SIMPAN KE DATABASE
            if (isset($hasil['face_encoding'])) {
                $user->face_embedding = json_encode($hasil['face_encoding']);
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
