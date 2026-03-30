<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventGuestInteraction
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
public function handle(Request $request, Closure $next)
{
    if (auth()->check() && auth()->user()->email === 'tamu@ihi.id') {
        // Jika mencoba melakukan POST, PUT, atau DELETE
        if (!$request->isMethod('GET')) {
            return back()->with('error', 'MAAF: Mode Kunjungan hanya diizinkan untuk melihat data.');
        }
    }

    return $next($request);
}
}
