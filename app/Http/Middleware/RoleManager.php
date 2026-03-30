<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleManager
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
public function handle(Request $request, Closure $next, $role): Response
{
    // 1. Cek apakah user sudah login
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    // 2. Izinkan akses jika role sesuai ATAU jika dia adalah superadmin
    // (Superadmin biasanya boleh akses semua pintu)
    if ($user->role === $role || $user->role === 'superadmin') {
        return $next($request);
    }

    // 3. JIKA AKSES DITOLAK: Lempar ke dashboard masing-masing role
    $redirectRoute = match($user->role) {
        'superadmin'   => 'superadmin.dashboard',
        'adminprogram' => 'adminprogram.dashboard',
        'adminsurat'   => 'adminsurat.dashboard',
        'user'         => 'user.dashboard',
        default        => 'login',
    };

    return redirect()->route($redirectRoute)->with('error', 'Akses ditolak! Anda diarahkan ke dashboard Anda.');
}
}
