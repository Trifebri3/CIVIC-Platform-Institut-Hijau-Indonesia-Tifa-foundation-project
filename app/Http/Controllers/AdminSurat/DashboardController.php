<?php

namespace App\Http\Controllers\AdminSurat;

use App\Http\Controllers\Controller;
use App\Models\SuratSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Utama (Cards)
        $stats = [
            'total'     => SuratSubmission::count(),
            'pending'   => SuratSubmission::where('status', 'pending')->count(),
            'approved'  => SuratSubmission::where('status', 'approved')->count(),
            'rejected'  => SuratSubmission::where('status', 'rejected')->count(),
        ];

        // 2. Aktivitas Terbaru (5 Surat Terakhir)
        $recentSubmissions = SuratSubmission::with('user')
            ->latest()
            ->take(5)
            ->get();

        // 3. Statistik Wilayah (Untuk Chart/List Wilayah Teraktif)
        $topRegions = SuratSubmission::select('wilayah_kegiatan', DB::raw('count(*) as total'))
            ->groupBy('wilayah_kegiatan')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // 4. Data Bulanan (Untuk Grafik Tren Pengajuan)
        $monthlyTrend = SuratSubmission::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as count')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('pages.admin-surat.dashboard', compact(
            'stats',
            'recentSubmissions',
            'topRegions',
            'monthlyTrend'
        ));
    }
}
