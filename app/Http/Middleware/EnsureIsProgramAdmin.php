<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsProgramAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
public function handle(Request $request, Closure $next)
{
    $user = auth()->user();

    // Jika dia Super Admin, biarkan lewat (Super Admin bisa lihat semua)
    if ($user->role === 'superadmin') return $next($request);

    // Jika dia Admin Program, cek apakah dia punya akses ke program ini
    $programId = $request->route('program'); // Mengambil ID program dari URL

    if ($user->role === 'adminprogram' && $user->managedPrograms()->where('program_id', $programId)->exists()) {
        return $next($request);
    }

    abort(403, 'Anda tidak memiliki otoritas atas program ini.');
}
}
