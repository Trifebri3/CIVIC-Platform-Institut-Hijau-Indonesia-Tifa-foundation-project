<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PgJawaban extends Model
{
    protected $fillable = [
        'user_id',
        'modul_ujian_id',
        'list_jawaban',
        'total_benar',
        'total_salah',
        'skor_akhir',
        'submitted_at'
    ];

    protected $casts = [
        'list_jawaban' => 'array',
        'submitted_at' => 'datetime',
    ];

    /**
     * Relasi ke User (Mahasiswa)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Modul Ujian
     */
    public function modulUjian(): BelongsTo
    {
        return $this->belongsTo(ModulUjian::class);
    }

    /**
     * FUNGSI SAKTI: Hitung Skor Otomatis
     * Digunakan saat user klik Submit di Livewire
     */
    public static function hitungDanSimpan($userId, $modulId, $userAnswers)
    {
        $allSoal = PgModul::where('modul_ujian_id', $modulId)->get();

        $benar = 0;
        $salah = 0;
        $skorDidapat = 0;
        $maxSkor = 0;

        foreach ($allSoal as $soal) {
            $jawabanUser = $userAnswers[$soal->id] ?? null;

            if ($jawabanUser === $soal->kunci_jawaban) {
                $benar++;
                $skorDidapat += $soal->poin;
            } else {
                $salah++;
            }
            $maxSkor += $soal->poin;
        }

        // Hitung skor akhir skala 0-100
        $skorAkhir = ($maxSkor > 0) ? ($skorDidapat / $maxSkor) * 100 : 0;

        return self::updateOrCreate(
            ['user_id' => $userId, 'modul_ujian_id' => $modulId],
            [
                'list_jawaban' => $userAnswers,
                'total_benar' => $benar,
                'total_salah' => $salah,
                'skor_akhir' => round($skorAkhir, 2),
                'submitted_at' => now(),
            ]
        );
    }
}
