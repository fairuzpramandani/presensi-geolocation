<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckFaceEnrollment
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (is_null($user->face_embedding)) {
                if ($request->routeIs('face.enroll') || $request->routeIs('face.store') || $request->routeIs('logout')) {
                    return $next($request);
                }
                return redirect()->route('face.enroll')->with('warning', 'Mohon lakukan validasi wajah terlebih dahulu sebelum mengakses sistem.');
            }
        }
        return $next($request);
    }
}
