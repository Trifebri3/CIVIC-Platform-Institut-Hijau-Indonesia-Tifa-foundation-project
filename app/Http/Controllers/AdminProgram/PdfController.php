<?php

namespace App\Http\Controllers\AdminProgram;

use App\Http\Controllers\Controller;
use App\Models\PenilaianUser;
use App\Models\Validasinilai;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PdfController extends Controller
{
    /**
     * Cetak PDF Satuan (Per Orang) + QR Code
     */
public function generateSingle($secret)
{
    $data = PenilaianUser::with(['template', 'user'])->where('qr_code_secret', $secret)->firstOrFail();
    $qrUrl = route('nilai.verify', $data->qr_code_secret);

    // 1. Tentukan Nama File & Path
    $fileName = 'qr-' . $secret . '.svg';
    $path = public_path('qrcodes/' . $fileName);

    // 2. Buat folder kalau belum ada
    if (!file_exists(public_path('qrcodes'))) {
        mkdir(public_path('qrcodes'), 0777, true);
    }

    // 3. Simpan QR ke File Fisik
    QrCode::format('svg')->size(150)->margin(1)->generate($qrUrl, $path);

    // 4. Kirim Path File ke Blade
    $qrcode_path = $path;

    $pdf = Pdf::loadView('pdf.nilai-single', compact('data', 'qrcode_path'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'chroot' => public_path(), // Penting agar DomPDF boleh baca file di public
            ]);

    return $pdf->stream('CIVIC_VAL_'.$data->user->name.'.pdf');
}

    /**
     * Cetak Rekap Semua User berdasarkan Template tertentu
     */
    public function generateRekap($template_id)
    {
        $template = Validasinilai::with(['penilaianUsers.user'])->findOrFail($template_id);
        $allData = $template->penilaianUsers;

        $pdf = Pdf::loadView('pdf.rekap-nilai', [
            'template' => $template,
            'allData' => $allData
        ])->setPaper('a4', 'landscape'); // Landscape biar tabelnya lega

        return $pdf->stream('Rekap_' . $template->template_name . '.pdf');
    }
}
