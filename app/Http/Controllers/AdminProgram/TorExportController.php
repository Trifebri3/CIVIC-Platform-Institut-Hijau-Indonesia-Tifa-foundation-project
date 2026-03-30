<?php

namespace App\Http\Controllers\AdminProgram;

use App\Http\Controllers\Controller;
use App\Models\TorSubmission;
use Barryvdh\DomPDF\Facade\Pdf;

class TorExportController extends Controller
{
    public function download($id)
    {
        $submission = TorSubmission::with(['user', 'period'])->findOrFail($id);

        // Ambil template form dari period & jawaban dari submission
        // Pastikan keduanya sudah ter-cast sebagai 'array' di model
        $template = $submission->period->form_template;
        $answers = $submission->answers; // JSON isian user

        $pdf = Pdf::loadView('pdf.tor-template', [
            'submission' => $submission,
            'template' => $template,
            'answers' => $answers
        ]);

        return $pdf->stream("TOR-{$submission->user->name}.pdf");
    }
}
