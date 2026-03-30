<?php

namespace App\Http\Controllers\AdminProgram\ModulUjian;

use App\Http\Controllers\Controller;
use App\Models\ModulUjian;
use App\Models\JawabanUjian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ADMProgModulujiancontroller extends Controller
{
    /**
     * Cetak PDF Rekap Semua Mahasiswa dalam satu Modul
     */
    public function rekapSemua($id)
    {
        $modul = ModulUjian::with(['jawaban.user', 'subProgram'])->findOrFail($id);

        // Data untuk View PDF
        $data = [
            'title' => 'REKAPITULASI NILAI MAHASISWA',
            'modul' => $modul,
            'date' => now()->format('d F Y'),
        ];

        $pdf = Pdf::loadView('pdf.modul-ujian.rekap-nilai', $data)
                  ->setPaper('a4', 'landscape'); // Rekap biasanya enak landscape

        return $pdf->stream("Rekap-Nilai-{$modul->judul}.pdf");
    }

    /**
     * Cetak PDF Jawaban Satuan per User
     */
    public function cetakSatuan($jawaban_id)
    {
        $jawaban = JawabanUjian::with(['user', 'modulUjian'])->findOrFail($jawaban_id);

        $data = [
            'title' => 'LEMBAR JAWABAN MAHASISWA',
            'jawaban' => $jawaban,
            'modul' => $jawaban->modulUjian,
            'user' => $jawaban->user
        ];

        $pdf = Pdf::loadView('pdf.modul-ujian.jawaban-satuan', $data);

        return $pdf->stream("Jawaban-{$jawaban->user->name}-{$jawaban->modulUjian->judul}.pdf");
    }
}
