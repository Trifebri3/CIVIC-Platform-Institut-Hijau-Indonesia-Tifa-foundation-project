<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RabSubmission extends Model
{
    protected $fillable = [
        'user_id',
        'tor_submission_id',
        'rab_period_id',
        'items', // JSON: Isian RAB dari User
        'total_requested',
        'total_approved',
        'status', // pending, revision, approved, rejected
        'admin_feedback', // JSON: Ceklis per item & catatan revisi
        'general_note'
    ];

    protected $casts = [
        'items' => 'array',
        'admin_feedback' => 'array',
        'total_requested' => 'decimal:2',
        'total_approved' => 'decimal:2'
    ];

    /**
     * Relasi ke User yang mengajukan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi MENGINDUK ke TOR Submission
     */
    public function torSubmission(): BelongsTo
    {
        return $this->belongsTo(TorSubmission::class);
    }

    /**
     * Relasi ke Periode RAB
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(RabPeriod::class, 'rab_period_id');
    }

    /**
     * Helper: Hitung otomatis total_approved berdasarkan item yang di-ACC admin
     * Digunakan saat Admin melakukan Update/Review
     */
    public function calculateApprovedTotal()
    {
        $total = 0;
        $feedback = $this->admin_feedback ?? [];

        foreach ($this->items as $index => $item) {
            // Jika admin menceklis (status == 'acc') pada index item tersebut
            if (isset($feedback[$index]['status']) && $feedback[$index]['status'] === 'acc') {
                // Asumsi: 'subtotal' dihitung di sisi frontend/logic sebelum simpan
                $total += $item['subtotal'] ?? 0;
            }
        }

        $this->total_approved = $total;
        $this->save();
    }
}
