<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;


class ProgramKhusus extends Model
{
    use HasFactory;

    protected $table = 'program_khusus'; // Pastikan nama tabel pas

    protected $fillable = [
        'nama_program',
        'slug',
        'deskripsi_singkat',
        'konten_eksklusif',
        'banner_url',
        'warna_tema',
        'start_at',
        'end_at',
        'is_active'
    ];

    // Otomatis bikin Slug pas bikin Nama Program
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($program) {
            $program->slug = Str::slug($program->nama_program);
        });
    }

    /**
     * Relasi: Satu program punya banyak peserta VIP
     */
    public function participants()
    {
        return $this->hasMany(ProgramKhususParticipant::class, 'program_khusus_id');
    }

    /**
     * Relasi ke Tabel Konten (Timeline, Resource, Information)
     */
    public function contents(): HasMany
    {
        // Pastikan nama model-nya "ProgramContentKhusus" sesuai yang kita buat tadi
        return $this->hasMany(ProgramContentKhusus::class, 'program_khusus_id');
    }



}
