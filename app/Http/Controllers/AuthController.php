<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    public function showRegisterPage()
    {
        $departemen = DB::table('departemen')->get();
        $jam_kerja = DB::table('jam_kerja')->orderBy('nama_jam_kerja')->get();
        return view('auth.register', compact('departemen', 'jam_kerja'));
    }
    public function showLoginKaryawan()
    {
        return view('auth.login');
    }

    public function prosesRegisterKaryawan(Request $request)
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:karyawan,email',
            'jabatan'      => 'required|string|max:50',
            'no_hp'        => 'required|string|max:15',
            'kode_dept'    => 'required|string|exists:departemen,kode_dept',
            'kode_jam_kerja' => 'required|string|exists:jam_kerja,kode_jam_kerja',
            'password'     => 'required|string|min:5|confirmed',
        ];

        $messages = [
            'nama_lengkap.required'   => 'Nama lengkap wajib diisi.',
            'email.required'          => 'Email wajib diisi.',
            'email.email'             => 'Format email tidak valid.',
            'email.unique'            => 'Email ini sudah terdaftar.',
            'jabatan.required'        => 'Jabatan wajib diisi.',
            'no_hp.required'          => 'No. HP wajib diisi.',
            'kode_dept.required'      => 'Departemen wajib dipilih.',
            'kode_dept.exists'        => 'Departemen tidak valid.',
            'kode_jam_kerja.required' => 'Jam Kerja wajib dipilih.',
            'kode_jam_kerja.exists'   => 'Data Jam Kerja tidak ditemukan.',
            'password.required'       => 'Password wajib diisi.',
            'password.min'            => 'Password minimal 5 karakter.',
            'password.confirmed'      => 'Konfirmasi password tidak cocok.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            return redirect('/register')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::table('karyawan')->insert([
                'nama_lengkap'   => $request->nama_lengkap,
                'email'          => $request->email,
                'password'       => Hash::make($request->password),
                'jabatan'        => $request->jabatan,
                'no_hp'          => $request->no_hp,
                'kode_dept'      => $request->kode_dept,
                'kode_jam_kerja' => $request->kode_jam_kerja
            ]);
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Akun berhasil dibuat! Silakan login.'
                ], 200);
            }
            return redirect('/')->with('success', 'Akun berhasil dibuat! Silakan login.');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            return redirect('/register')
                ->with('warning', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function showDirectResetForm()
    {
        return view('auth.passwords.reset');
    }

   public function directResetPassword(Request $request)
    {
        $rules = [
            'email' => 'required|email|exists:karyawan,email',
            'password' => 'required|string|min:5|confirmed',
        ];

        $messages = [
            'email.exists' => 'Email ini tidak terdaftar sebagai karyawan.',
            'password.min' => 'Password minimal 5 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::table('karyawan')
                ->where('email', $request->email)
                ->update([
                    'password' => Hash::make($request->password),
                ]);
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Password berhasil diubah! Silakan login dengan password baru Anda.'
                ], 200);
            }
            return redirect('/')->with('success', 'Password berhasil diubah! Silakan login dengan password baru Anda.');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Terjadi kesalahan saat mengubah password.'
                ], 500);
            }
            return back()->with('warning', 'Terjadi kesalahan saat mengubah password. Coba lagi.');
        }
    }


    public function proseslogin(Request $request)
    {
        if (Auth::guard('karyawan')->attempt(['email'=> $request->email, 'password' => $request->password])) {
            $user = Auth::guard('karyawan')->user();
            if ($request->wantsJson()) {
                $token = bin2hex(random_bytes(40));
                \Illuminate\Support\Facades\DB::table('karyawan')
                    ->where('email', $user->email)
                    ->update(['remember_token' => $token]);

                return response()->json([
                    'status' => true,
                    'message' => 'Login Berhasil',
                    'access_token' => $token,
                    'user' => $user
                ], 200);
            }
            $request->session()->regenerate();
            return redirect('/dashboard');
        }
        else {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email atau Password Salah'
                ], 401);
            }
            return redirect('/')->with(['warning'=>'Email / Password Salah']);
        }
    }

    public function proseslogout(Request $request)
    {
        if (Auth::guard('karyawan')->check())
        {
            Auth::guard('karyawan')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/');
        }
    }

    public function registerAdmin(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:5|confirmed',
        ];
        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 5 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {

            return redirect('/panel#tab-register')
                        ->withErrors($validator)
                        ->withInput()
                        ->with('form_type', 'register');
        }
        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            return redirect('/panel')->with('success', 'Akun berhasil dibuat! Silakan login.');

        } catch (\Exception $e) {
            return redirect('/panel#tab-register')
                        ->with('warning', 'Terjadi kesalahan pada server.')
                        ->withInput()
                        ->with('form_type', 'register');
        }
    }
    public function showLogin()
    {
        return view('auth.login');
    }
    public function proseslogoutadmin(Request $request)
    {
        if (Auth::guard('user')->check())
        {
            Auth::guard('user')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/panel');
        }
        return redirect('/dashboardadmin');
    }

    public function prosesloginadmin(Request $request)
    {
        if (Auth::guard('user')->attempt(['email'=> $request-> email, 'password' => $request->password]))
        {
            $request->session()->regenerate();
            return redirect('/panel/dashboardadmin');
        }else{
            return redirect('/panel')->with(['warning'=>'Email / Password Salah']);
        }
    }

}
