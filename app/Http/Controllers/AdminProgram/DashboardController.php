<?php

namespace App\Http\Controllers\AdminProgram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard untuk Admin Program
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil program-program yang dikelola oleh user ini
        // Menggunakan relasi managedPrograms yang sudah kita buat di Model User
        $programs = $user->managedPrograms()
            ->withCount(['subPrograms']) // Contoh: Menghitung jumlah konten di tiap program
            ->get();

        return view('pages.admin-program.dashboard', compact('user', 'programs'));
    }
}
