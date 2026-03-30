<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProfileTemplate;

class ActivationController extends Controller
{
public function index()
    {
        // Kita ambil semua template field yang dibuat Admin
        $templates = ProfileTemplate::orderBy('order', 'asc')->get();

        // Kita lempar ke view menggunakan compact
        return view('pages.user.activation', compact('templates'));
    }
}
