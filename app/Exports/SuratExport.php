<?php

namespace App\Exports;

use App\Models\SuratSubmission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SuratExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return SuratSubmission::with('user')->latest()->get();
    }

    public function headings(): array
    {
        return ['Tanggal Pengajuan', 'Nomor Surat', 'Pengaju', 'Wilayah FGD', 'Penerima', 'Status'];
    }

    public function map($surat): array
    {
        return [
            $surat->created_at->format('d/m/Y'),
            $surat->nomor_surat ?? '-',
            $surat->user->name,
            $surat->wilayah_kegiatan,
            $surat->penerima_surat,
            strtoupper($surat->status),
        ];
    }
}
