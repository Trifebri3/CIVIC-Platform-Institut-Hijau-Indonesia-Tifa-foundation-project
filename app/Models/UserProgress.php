<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    /**
     * Nama tabel (Opsional jika sudah sesuai standar Laravel)
     */
    protected $table = 'user_progress';

    /**
     * Guarded untuk keamanan mass-assignment
     */
    protected $guarded = [];

    /**
     * CASTING: Memastikan completed_at dibaca sebagai objek Carbon/Datetime
     */
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * RELASI: Milik siapa progress ini?
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELASI: Materi mana yang diselesaikan?
     */
    public function content()
    {
        return $this->belongsTo(SubProgramContent::class, 'sub_program_content_id');
    }

    /**
     * SCOPE: Memudahkan query materi yang sudah selesai
     * Cara pakai: UserProgress::completed()->get();
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }
}
