<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfileTemplate;
// Import model partisipan
use App\Models\ProgramKhususParticipant;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Ambil Undangan yang PENDING (is_active = 0)
        // Kita gunakan eager load 'program' agar tidak error null
        $pendingInvites = ProgramKhususParticipant::with('program')
            ->where('user_id', $user->id)
            ->where('is_active', false)
            ->whereNotNull('program_khusus_id')
            ->get();

        // 2. Ambil Program yang sudah ACTIVE (is_active = 1)
        $activePrograms = ProgramKhususParticipant::with('program')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->whereNotNull('program_khusus_id')
            ->get();

        // --- LOGIC PROFILE BAWAAN BOS (Tetap Dipertahankan) ---
        $templates = ProfileTemplate::orderBy('order')->get();
        $profile = $user->profile;
        $totalFields = $templates->count();
        $completedFields = 0;

        if ($profile && $profile->custom_fields_values) {
            foreach ($templates as $template) {
                if (!empty($profile->custom_fields_values[$template->field_name])) {
                    $completedFields++;
                }
            }
        }
        $completionPercentage = $totalFields > 0 ? round(($completedFields / $totalFields) * 100) : 100;

        // Kirim semua data ke view
        return view('pages.user.dashboard', compact(
            'user',
            'profile',
            'templates',
            'completionPercentage',
            'pendingInvites', // Data Baru
            'activePrograms'  // Data Baru
        ));
    }
}
