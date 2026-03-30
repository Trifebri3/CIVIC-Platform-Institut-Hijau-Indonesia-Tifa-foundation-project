<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramReport;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan sudah install dompdf

class ProgramReportController extends Controller
{
    public function exportPdf($id)
    {
        $report = ProgramReport::with(['user', 'profile', 'template', 'period'])->findOrFail($id);

        $data = [
            'report' => $report,
            'content' => $report->content, // Data dinamis dari user
            'profile' => $report->profile
        ];

        $pdf = Pdf::loadView('pdf.program-report', $data)
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("Laporan_{$report->user->name}_{$report->period->name}.pdf");
    }
}
