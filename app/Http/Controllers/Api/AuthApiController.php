<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthApiController extends Controller
{
    // LOGIN
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Input tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::guard('karyawan')->attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau Password salah.'
            ], 401);
        }

        $karyawan = Auth::guard('karyawan')->user();
        $token = $karyawan->createToken('authToken')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil!',
            'user' => $karyawan,
            'token' => $token
        ], 200);
    }

    // REGISTER
    public function register(Request $request)
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:karyawan,email',
            'jabatan' => 'required|string|max:50',
            'no_hp' => 'required|string|max:15',
            'kode_dept' => 'required|string|exists:departemen,kode_dept',
            'password' => 'required|string|min:5|confirmed',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pendaftaran tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::table('karyawan')->insert([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'jabatan' => $request->jabatan,
            'no_hp' => $request->no_hp,
            'kode_dept' => $request->kode_dept
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Akun berhasil dibuat!'
        ], 201);
    }

    // RESET PASSWORD
    public function directResetPassword(Request $request)
    {
        $rules = [
            'email' => 'required|email|exists:karyawan,email',
            'password' => 'required|min:5|confirmed',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid.',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::table('karyawan')
            ->where('email', $request->email)
            ->update([
                'password' => Hash::make($request->password),
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diubah!'
        ], 200);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil.'
        ], 200);
    }
}
