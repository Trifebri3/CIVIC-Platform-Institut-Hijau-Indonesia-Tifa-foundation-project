<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramContentKhusus extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database
     */
    protected $table = 'program_contents';

    /**
     * Kolom yang boleh diisi (Mass Assignment)
     */
    protected $fillable = [
        'program_khusus_id',
        'type',        // 'timeline', 'asset', 'announcement'
        'title',       // Judul konten
        'data',        // Kolom JSON
        'order',       // Urutan tampil
        'is_visible',  // Status tampil/sembunyi
    ];

    /**
     * Casting data JSON otomatis menjadi Array
     */
    protected $casts = [
        'data'       => 'array',
        'is_visible' => 'boolean',
        'order'      => 'integer',
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /**
     * Relasi ke Program Khusus Utama
     */
    public function program()
    {
        return $this->belongsTo(ProgramKhusus::class, 'program_khusus_id');
    }

    // =========================================================================
    // SCOPES (Untuk Filter Cepat)
    // =========================================================================

    public function scopeTimeline($query)
    {
        return $query->where('type', 'timeline')->orderBy('order', 'asc');
    }

    public function scopeAssets($query)
    {
        return $query->where('type', 'asset')->orderBy('order', 'asc');
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    // =========================================================================
    // ACCESSORS (Helper untuk ambil data di dalam JSON)
    // =========================================================================

    /**
     * Contoh penggunaan di Blade: $content->date_info
     */
    public function getDateInfoAttribute()
    {
        return $this->data['date'] ?? null;
    }

    /**
     * Ambil Link File atau Link Eksternal
     */
    public function getFileUrlAttribute()
    {
        if ($this->type !== 'asset') return null;

        // Cek apakah ada file yang diupload atau link eksternal
        return $this->data['file_path'] ?? $this->data['link'] ?? '#';
    }

    /**
     * Cek apakah ini aset bertipe File Download
     */
    public function getIsDownloadableAttribute()
    {
        return isset($this->data['file_path']);
    }
}
