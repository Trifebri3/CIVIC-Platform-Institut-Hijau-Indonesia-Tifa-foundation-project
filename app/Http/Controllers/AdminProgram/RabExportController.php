<?php

namespace App\Http\Controllers\AdminProgram;
use App\Http\Controllers\Controller;
use App\Models\RabSubmission;
use App\Models\RabPeriod;
use Barryvdh\DomPDF\Facade\Pdf;

class RabExportController extends Controller
{
    public function single(RabSubmission $submission)
    {
        $submission->load(['user', 'period']);

        $pdf = Pdf::loadView('pdf.rab-single', [
            'sub' => $submission,
            'template' => $submission->period->form_template
        ])->setPaper('a4', 'portrait');

        return $pdf->download("RAB-{$submission->user->name}.pdf");
    }

    public function batch(RabPeriod $period)
    {
        $submissions = RabSubmission::where('rab_period_id', $period->id)->with('user')->get();

        // Gabungkan semua ke satu PDF atau buat loop
        $pdf = Pdf::loadView('pdf.rab-batch', [
            'period' => $period,
            'submissions' => $submissions
        ])->setPaper('a4', 'landscape');

        return $pdf->download("REKAP-RAB-{$period->name}.pdf");
    }
}
