<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanUjian extends Model
{
    protected $fillable = [
        'modul_ujian_id',
        'user_id',
        'konten_jawaban',
        'nilai',
        'feedback_admin',
        'graded_at'
    ];

    protected $casts = [
        'konten_jawaban' => 'array', // Simpan jawaban per ID soal di sini
        'graded_at' => 'datetime',
        'nilai' => 'integer',
    ];

    /**
     * Relasi balik ke Soal
     */
    public function modulUjian(): BelongsTo
    {
        return $this->belongsTo(ModulUjian::class);
    }

    /**
     * Relasi ke User (Mahasiswa)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper: Cek apakah sudah dinilai atau belum
     */
    public function isGraded(): bool
    {
        return !is_null($this->nilai);
    }

    /**
 * Relasi balik ke model Ujian
 */
/**
 * Relasi ke Modul Ujian
 */
public function ujian()
{
    // Sesuaikan parameter kedua dengan nama kolom foreign key di tabel jawaban_ujians
    return $this->belongsTo(ModulUjian::class, 'modul_ujian_id');
}
}
