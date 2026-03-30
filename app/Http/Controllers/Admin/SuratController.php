<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuratSubmission;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
// TAMBAHKAN DUA BARIS INI:
use App\Exports\SuratExport;
use Maatwebsite\Excel\Facades\Excel;

class SuratController extends Controller
{
    /**
     * Generate PDF Surat Satuan (Berdasarkan Template Gambar)
     */
    public function generatePdf($id)
    {
        $surat = SuratSubmission::with('user')->findOrFail($id);

        // Security check
        if (!auth()->user()->isAdminSurat() && $surat->user_id !== auth()->id()) {
            abort(403, 'Akses Ditolak.');
        }

        $data = [
            'surat' => $surat,
            'tanggal_indo' => $surat->tanggal_surat
                ? $surat->tanggal_surat->translatedFormat('d F Y')
                : now()->translatedFormat('d F Y'),
        ];

        $pdf = Pdf::loadView('pdf.template-surat-ihi', $data)
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("Surat_Undangan_{$surat->wilayah_kegiatan}.pdf");
    }

    /**
     * Proses Approval oleh AdminSurat
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'nomor_surat' => 'required|string',
            'tanggal_surat' => 'required|date',
        ]);

        $surat = SuratSubmission::findOrFail($id);

        $surat->update([
            'nomor_surat'   => $request->nomor_surat,
            'tanggal_surat' => $request->tanggal_surat,
            'status'        => 'approved',
            'processed_by'  => auth()->id(),
        ]);

        return back()->with('success', 'Surat berhasil disetujui!');
    }

    /**
     * Rekap Semua Data ke Excel
     */
    public function exportExcel()
    {
        // Pastikan file App\Exports\SuratExport sudah dibuat sebelumnya
        return Excel::download(new SuratExport, 'rekap-surat-ihi.xlsx');
    }

    /**
     * Rekap Semua Data ke PDF (Tabel Landscape)
     */
    public function exportPdfRekap()
    {
        $surat = SuratSubmission::with('user')->latest()->get();

        $pdf = Pdf::loadView('pdf.rekap-surat', compact('surat'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('rekap-surat-ihi.pdf');
    }
}
