<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Validasinilai extends Model
{
    protected $fillable = [
        'template_name',
        'description',
        'schema'
    ];

    // Otomatis ubah JSON di DB jadi Array PHP
    protected $casts = [
        'schema' => 'array',
    ];

    /**
     * Relasi ke semua hasil penilaian yang menggunakan template ini.
     * Berguna untuk fitur "REKAP SEMUA USER".
     */
    public function penilaianUsers(): HasMany
    {
        return $this->hasMany(PenilaianUser::class, 'validasinilai_id');
    }
}
