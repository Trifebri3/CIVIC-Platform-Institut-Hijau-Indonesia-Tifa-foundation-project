<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuratSubmission extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi (Mass Assignment)
     */
    protected $fillable = [
        'user_id',
        'nomor_surat',
        'tanggal_surat',
        'lampiran',
        'wilayah_kegiatan',
        'penerima_surat',
        'hari_tanggal',
        'waktu_pelaksanaan',
        'tempat_pelaksanaan',
        'kontak_person',
        'status',
        'processed_by',
        'admin_note',
        'file_pdf_path',
    ];

    /**
     * Casting tipe data agar otomatis jadi Carbon (Tanggal)
     */
    protected $casts = [
        'tanggal_surat' => 'date',
        'submitted_at' => 'datetime',
    ];

    /**
     * RELASI: Surat ini milik siapa (User/Mahasiswa)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * RELASI: Siapa admin yang memproses surat ini
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * HELPER: Format Tanggal Indonesia untuk Template (dd mm yyyy)
     * Contoh: 30 Maret 2026
     */
    public function getFormattedTanggalAttribute()
    {
        return $this->tanggal_surat
            ? $this->tanggal_surat->translatedFormat('d F Y')
            : '-';
    }

    /**
     * HELPER: Badge Status untuk UI
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending'  => 'bg-amber-100 text-amber-700 border-amber-200',
            'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'rejected' => 'bg-red-100 text-red-700 border-red-200',
            default    => 'bg-gray-100 text-gray-700',
        };
    }
}
