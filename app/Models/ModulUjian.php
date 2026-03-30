<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Carbon\Carbon;

class ModulUjian extends Model
{
    protected $fillable = [
        'sub_program_id',
        'judul',
        'instruksi',
        'lampiran_instruksi',
        'konfigurasi_soal',
        'deadline',
        'tipe_ujian',
        'is_active',
        'max_attempts'
    ];

    // Otomatis convert JSON ke Array (dan sebaliknya)
    protected $casts = [
        'lampiran_instruksi' => 'array',
        'konfigurasi_soal' => 'array',
        'deadline' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Sub Program (Parent)
     */
    public function subProgram(): BelongsTo
    {
        return $this->belongsTo(SubProgram::class);
    }

    /**
     * Relasi ke semua jawaban mahasiswa
     */
    public function jawaban(): HasMany
    {
        return $this->hasMany(JawabanUjian::class);
    }

    /**
     * Helper: Cek apakah sudah lewat deadline
     */
    public function isExpired(): bool
    {
        return $this->deadline && Carbon::now()->greaterThan($this->deadline);
    }

    /**
     * Helper: Hitung jumlah soal yang dibuat
     */
    public function getJumlahSoalAttribute(): int
    {
        return count($this->konfigurasi_soal ?? []);
    }
public function pg_soals(): HasMany
    {
        // Pastikan nama modelnya benar (PgModul)
        // dan foreign key-id nya sesuai di database (modul_ujian_id)
        return $this->hasMany(PgModul::class, 'modul_ujian_id');
    }
public function pg_moduls()
{
    return $this->hasMany(PgModul::class, 'modul_ujian_id');
}


}
