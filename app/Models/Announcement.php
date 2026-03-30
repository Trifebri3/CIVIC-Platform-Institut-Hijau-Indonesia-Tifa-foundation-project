<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Announcement extends Model
{
    use HasFactory;

    /**
     * Kolom yang bisa diisi secara massal.
     * banner: menyimpan path gambar/foto pengumuman.
     * target_type: 'global' (semua user) atau 'program' (hanya peserta program tertentu).
     */
    protected $fillable = [
        'title',
        'banner',
        'message',
        'link_label',
        'link_url',
        'type', // info, warning, danger, success (untuk styling UI)
        'target_type',
        'send_email',
        'published_at',
    ];

    /**
     * Casting data agar otomatis menjadi tipe data yang tepat saat dipanggil.
     */
    protected $casts = [
        'send_email' => 'boolean',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Relasi ke Program.
     * Jika target_type adalah 'program', maka pengumuman ini
     * akan terhubung ke satu atau lebih program di tabel pivot announcement_program.
     */
    public function targetPrograms(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'announcement_program', 'announcement_id', 'program_id');
    }

    /**
     * Scope untuk mengambil pengumuman yang relevan bagi user tertentu.
     * Digunakan di Dashboard User: Announcement::forUser(auth()->user())->get();
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where('target_type', 'global')
            ->orWhereHas('targetPrograms', function ($q) use ($user) {
                // Mencari program yang diikuti oleh user ini
                $q->whereIn('programs.id', $user->programs->pluck('id'));
            });
    }
}
