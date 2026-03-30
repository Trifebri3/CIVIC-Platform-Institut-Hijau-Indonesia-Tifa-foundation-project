<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PgModul extends Model
{
    use HasFactory;

    protected $fillable = [
        'modul_ujian_id',
        'pertanyaan',
        'opsi',
        'kunci_jawaban',
        'poin'
    ];

    // Casting JSON 'opsi' otomatis jadi Array PHP
    protected $casts = [
        'opsi' => 'array',
    ];

    /**
     * Relasi ke Header Modul Ujian Utama
     */
    public function modulUjian(): BelongsTo
    {
        return $this->belongsTo(ModulUjian::class);
    }
}
