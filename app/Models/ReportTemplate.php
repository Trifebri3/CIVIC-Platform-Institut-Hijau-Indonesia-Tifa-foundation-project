<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
// Hapus baris di bawah jika sudah ada di namespace yang sama,
// tapi tambahkan jika kamu ingin memastikan class-nya terdeteksi.
// use App\Models\ProgramReport;

class ReportTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'rab_period_id',
        'title',
        'fields',
        'is_active'
    ];

    protected $casts = [
        'fields' => 'json', // Ganti 'array' ke 'json' biar lebih aman dengan tipe longtext_bin
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Periode RAB
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(RabPeriod::class, 'rab_period_id');
    }

    /**
     * Relasi ke Laporan-laporan yang masuk menggunakan template ini
     */
    public function submissions(): HasMany
    {
        // CARA PALING AMAN: Gunakan Full Namespace jika class tidak ter-import otomatis
        return $this->hasMany(\App\Models\ProgramReport::class, 'report_template_id');
    }
}
