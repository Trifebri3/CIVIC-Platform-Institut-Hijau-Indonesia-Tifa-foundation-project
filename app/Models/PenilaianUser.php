<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PenilaianUser extends Model
{
    protected $fillable = [
        'validasinilai_id',
        'user_id',
        'isi_nilai',
        'status',
        'qr_code_secret'
    ];

    protected $casts = [
        'isi_nilai' => 'array',
    ];

    /**
     * Otomatis generate QR Secret pas data dibuat pertama kali.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->qr_code_secret = (string) Str::uuid();
        });
    }

    /**
     * Relasi balik ke Master Template.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Validasinilai::class, 'validasinilai_id');
    }

    /**
     * Relasi ke User (Mahasiswa/Peserta).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
