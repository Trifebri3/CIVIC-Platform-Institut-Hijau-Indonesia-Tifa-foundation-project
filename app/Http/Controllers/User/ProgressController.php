<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    /**
     * Menampilkan halaman progres user (View Wrapper untuk Livewire)
     */
    public function index()
    {
        return view('pages.user.progress-index');
    }

    /**
     * Export E-Raport ke PDF
     */
    public function exportPdf()
    {
        $user = Auth::user();

        // Eager Load: Pastikan 'jawaban_ujians' terload agar tidak query berulang di Blade
        $user->load([
            'progress',
            'jawaban_ujians.ujian', // Relasi ini merujuk ke fungsi ujian() di Model JawabanUjian
            'programs.subPrograms.contents',
            'programs.subPrograms.absensis.kehadirans' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }
        ]);

        $programs = $user->programs;

        // Data yang dilempar ke View PDF
        $data = [
            'user'      => $user,
            'programs'  => $programs,
            'date'      => now()->format('d F Y'),
            'timestamp' => now()->format('H:i:s'),
        ];

        // Load View khusus PDF
        $pdf = Pdf::loadView('pdf.raport-user', $data);

        // Setting kertas A4 Portrait
        $pdf->setPaper('a4', 'portrait');

        // Stream langsung buka di browser
        return $pdf->stream('E-Raport_' . strtoupper(str_replace(' ', '_', $user->name)) . '.pdf');
    }
}
