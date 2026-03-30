<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RabPeriod extends Model
{
    protected $fillable = [
        'name',
        'description',
        'form_template', // JSON: Kolom custom & batasan per item
        'max_total_budget', // Plafon/Vakum Anggaran
        'start_at',
        'end_at',
        'is_active'
    ];

    protected $casts = [
        'form_template' => 'array',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
        'max_total_budget' => 'decimal:2'
    ];

    /**
     * Cek apakah periode masih buka (Timeline check)
     */
    public function isOpen(): bool
    {
        return $this->is_active && now()->between($this->start_at, $this->end_at);
    }

    /**
     * Relasi ke semua pengajuan RAB di periode ini
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(RabSubmission::class);
    }

    public function programProfiles()
{
    return $this->hasMany(ProgramProfile::class);
}
}
