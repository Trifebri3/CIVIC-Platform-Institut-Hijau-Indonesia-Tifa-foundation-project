<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivationAnswer;
use App\Models\ActivationQuestion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\Superadmin\ActivationAnswersExport;
use Maatwebsite\Excel\Facades\Excel;


class SuperadminActivationController extends Controller
{
    /**
     * Halaman Rekap: Menampilkan daftar semua user yang sudah aktivasi.
     */
    public function rekap()
    {
        $users = User::where('is_activated', true)
                     ->withCount('activationAnswers')
                     ->latest()
                     ->paginate(15);

        // Sesuaikan dengan path folder Anda: pages/super-admin/activation/answer/index
        return view('pages.super-admin.activation.answer.index', compact('users'));
    }

    /**
     * Cetak SEMUA jawaban (Rekapitulasi PDF).
     */
    public function printAll()
    {
        $users = User::where('is_activated', true)
                     ->with(['activationAnswers.question'])
                     ->get();

        $data = [
            'title' => 'Laporan Rekapitulasi Aktivasi',
            'date'  => date('d/m/Y'),
            'users' => $users
        ];

        return Pdf::loadView('pages.super-admin.activation.answer.print-all', $data)
                  ->setPaper('a4', 'landscape')
                  ->stream('rekap-aktivasi.pdf');
    }

    /**
     * Cetak jawaban per orang (Individu PDF).
     */
    public function printUser($id)
    {
        $user = User::findOrFail($id);
        $answers = ActivationAnswer::where('user_id', $id)
                    ->with('question')
                    ->get();

        $data = [
            'title'   => 'Hasil Aktivasi Individu',
            'user'    => $user,
            'answers' => $answers,
            'date'    => date('d/m/Y')
        ];

        return Pdf::loadView('pages.super-admin.activation.answer.print-single', $data)
                  ->stream("aktivasi-{$user->name}.pdf");
    }

    public function exportExcel()
{
    return Excel::download(new ActivationAnswersExport, 'rekap-aktivasi-' . date('Y-m-d') . '.xlsx');
}
}
