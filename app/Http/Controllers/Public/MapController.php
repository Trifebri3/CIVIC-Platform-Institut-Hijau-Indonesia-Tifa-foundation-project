<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ProgramProfile; // Pastikan nama model sesuai tabel program_profiles

class MapController extends Controller
{
    public function index()
    {
        // Ambil data dari tabel program_profiles
        $programs = ProgramProfile::whereNotNull('latitude')
                                  ->whereNotNull('longitude')
                                  ->get();

        return view('pages.public.map-index', compact('programs'));
    }
}
