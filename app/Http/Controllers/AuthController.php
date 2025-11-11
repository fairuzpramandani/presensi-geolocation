<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function proseslogin(Request $request)
    {
        if (Auth::guard('karyawan')->attempt(['email'=> $request-> email, 'password' => $request->password]))
        {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }else{
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
