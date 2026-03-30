<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramReport;
use Barryvdh\DomPDF\Facade\Pdf;

class ProgramReportController extends Controller
{
    // SESUAIKAN NAMA FUNGSI JADI generatePdf (Sesuai route:list)
    public function generatePdf($id)
    {
        $report = ProgramReport::with(['user.programs', 'period'])->findOrFail($id);

        $program = $report->user?->programs?->first();

        $data = [
            'report'  => $report,
            'content' => $report->content,
            'program' => $program,
        ];

        $pdf = Pdf::loadView('pdf.program-report', $data)
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("Laporan_{$report->user->name}.pdf");
    }
}
