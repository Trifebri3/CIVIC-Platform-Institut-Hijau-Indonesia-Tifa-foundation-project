<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TorSubmission extends Model
{
    protected $guarded = [];

    // Agar data form yang kompleks (tabel, link, foto) mudah diakses sebagai array
    protected $casts = [
        'submission_data' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'answers' => 'array',
    ];


    /**
     * Relasi balik ke User (Pengaju)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi balik ke Periode
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(TorPeriod::class, 'tor_period_id');
    }

    /**
     * Boot Method: Otomatis buat kode unik saat TOR dibuat
     * Contoh: TOR-2026-ABCDE
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->submission_code)) {
                $model->submission_code = 'TOR-' . date('Y') . '-' . strtoupper(Str::random(5));
            }
        });
    }

    /**
     * Helper untuk cek warna label status di UI
     */
    public function getStatusColorAttribute()
    {
        return [
            'draft'    => 'gray',
            'pending'  => 'blue',
            'revision' => 'orange',
            'approved' => 'green',
            'rejected' => 'red',
        ][$this->status] ?? 'gray';
    }
}
