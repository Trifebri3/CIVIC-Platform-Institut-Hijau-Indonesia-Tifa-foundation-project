<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifikasiPertanyaan
{
    /**
     * Handle an incoming request.
     */
 public function handle(Request $request, Closure $next)
{
    if (Auth::check()) {
        $user = Auth::user();

        // Cek Gerbang 1
        if (!$user->is_activated) {
            return $request->routeIs('user.verifikasipertanyaan') ? $next($request) : redirect()->route('user.verifikasipertanyaan');
        }

        // Cek Gerbang 2
        if (!$user->is_profile_completed) {
            return $request->routeIs('user.profile.edit') ? $next($request) : redirect()->route('user.profile.edit');
        }

        // Jika sudah lengkap, cegah balik ke halaman verifikasi/edit (Hanya untuk akses GET)
        if ($request->isMethod('get') && ($request->routeIs('user.verifikasipertanyaan') || $request->routeIs('user.profile.edit'))) {
            return redirect()->route('user.dashboard');
        }
    }

    return $next($request);
}

}
