<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProfileTemplate;
// Gunakan Model Program jika sudah buat, jika belum sementara count user saja
use Illuminate\Http\Request;

class SuperDashboardController extends Controller
{
    public function index()
    {
        $data = [
            'total_users'     => User::where('role', 'user')->count(),
            'total_admin_prg' => User::where('role', 'adminprogram')->count(),
            'total_fields'    => ProfileTemplate::count(),
            // Contoh data terbaru
            'recent_users'    => User::where('role', 'user')->latest()->take(5)->get(),
        ];

        return view('pages.super-admin.dashboard', $data);
    }
}
