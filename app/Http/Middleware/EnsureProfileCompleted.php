<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
public function handle(Request $request, Closure $next)
{
    if (auth()->check()) {
        $user = auth()->user();

        // JIKA BELUM AKTIVASI: Lempar ke rute Livewire baru
        if (!$user->is_activated) {
            // Pastikan tidak sedang mengakses rute verifikasi itu sendiri (cegah loop)
            if (!$request->routeIs('user.verifikasipertanyaan')) {
                return redirect()->route('user.verifikasipertanyaan');
            }
        }
    }

    return $next($request);
}
}
