<?php

namespace App\Exports\Superadmin;

use App\Models\User;
use App\Models\ActivationQuestion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActivationAnswersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $questions;

    public function __construct()
    {
        // Ambil semua pertanyaan untuk jadi header
        $this->questions = ActivationQuestion::orderBy('order', 'asc')->get();
    }

    public function collection()
    {
        // Ambil user yang sudah aktivasi beserta jawabannya
        return User::where('is_activated', true)
                   ->with(['activationAnswers'])
                   ->get();
    }

    public function headings(): array
    {
        $headers = ['ID', 'Nama Lengkap', 'Email', 'Tanggal Aktivasi'];

        foreach ($this->questions as $q) {
            $headers[] = $q->title;
        }

        return $headers;
    }

    public function map($user): array
    {
        $row = [
            $user->id,
            $user->name,
            $user->email,
            $user->updated_at->format('d/m/Y H:i'),
        ];

        // Map jawaban user sesuai dengan urutan pertanyaan di header
        foreach ($this->questions as $q) {
            $answer = $user->activationAnswers->where('activation_question_id', $q->id)->first();

            if ($answer) {
                $content = $answer->content;
                if (is_array($content)) {
                    // Gabungkan array jadi string agar rapi di satu cell Excel
                    $formatted = [];
                    foreach ($content as $key => $val) {
                        $valStr = is_array($val) ? implode(', ', $val) : $val;
                        $formatted[] = ucfirst($key) . ": " . $valStr;
                    }
                    $row[] = implode(" | ", $formatted);
                } else {
                    $row[] = $content;
                }
            } else {
                $row[] = '-';
            }
        }

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style Header (Baris 1) jadi Tebal & Background Teal
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0D9488']
                ]
            ],
        ];
    }
}
