<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KehadiranUser extends Model
{
    protected $table = 'kehadiran_users';
    protected $fillable = ['absensi_id', 'user_id', 'submitted_at', 'score', 'status'];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function absensi() {
        return $this->belongsTo(Absensi::class);
    }

    
}
