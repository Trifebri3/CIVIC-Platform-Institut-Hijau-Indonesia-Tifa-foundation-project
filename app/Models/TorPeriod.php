<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class TorPeriod extends Model
{
    protected $guarded = [];

    // Penting: Agar JSON form_template otomatis jadi Array PHP
    protected $casts = [
        'form_template' => 'array',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke semua pengajuan (Submissions) dalam periode ini
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(TorSubmission::class);
    }

    /**
     * Scope untuk mengambil periode yang sedang aktif dan dalam rentang waktu
     */
    public function scopeOpen($query)
    {
        return $query->where('is_active', true)
                     ->where('start_at', '<=', Carbon::now())
                     ->where('end_at', '>=', Carbon::now());
    }

    /**
     * Cek apakah user tertentu masih punya kuota kirim TOR
     */
    public function canUserSubmit($userId): bool
    {
        $count = $this->submissions()
                      ->where('user_id', $userId)
                      ->whereIn('status', ['pending', 'approved', 'revision'])
                      ->count();

        return $count < $this->max_submissions_per_user;
    }
}
