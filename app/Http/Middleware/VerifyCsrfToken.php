<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // 1. Auth
        'proseslogin',
        'prosesregister',
        'proseslogout',
        'ubah-password-cepat',

        // 2. Presensi
        'presensi/store',
        'presensi/cekpengajuanizin',

        // 3. History & Izin
        'gethistori',
        'presensi/storeizin',

        // 4. Update Profile
        'presensi/*/updateprofile',

        // 5. Api
        'api/*',
    ];
}
