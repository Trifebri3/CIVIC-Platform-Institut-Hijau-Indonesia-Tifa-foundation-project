<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;




class Absensi extends Model
{
    protected $fillable = [
        'sub_program_id', 'title', 'type', 'auth_code',
        'is_protected', 'open_at', 'duration_minutes', 'is_active'
    ];

    protected $casts = [
        'open_at' => 'datetime',
        'is_active' => 'boolean',
        'is_protected' => 'boolean',
    ];

    // Relasi ke SubProgram
    public function subProgram() {
        return $this->belongsTo(SubProgram::class);
    }

    // Relasi ke User yang sudah absen
    public function kehadiran() {
        return $this->hasMany(KehadiranUser::class);
    }

    /**
     * Helper: Cek apakah absen saat ini SEDANG DIBUKA
     */
    public function getIsOpenAttribute()
    {
        if (!$this->is_active || !$this->open_at) return false;

        $now = Carbon::now();
        $closingTime = $this->open_at->copy()->addMinutes($this->duration_minutes);

        return $now->between($this->open_at, $closingTime);
    }

    /**
     * Helper: Cek apakah absen sudah LEWAT WAKTU
     */
    public function getIsExpiredAttribute()
    {
        if (!$this->open_at) return false;
        return Carbon::now()->gt($this->open_at->copy()->addMinutes($this->duration_minutes));
    }

    public function kehadirans(): HasMany
    {
        // Pastikan nama modelnya sesuai, misal: KehadiranUser atau Attendance
        return $this->hasMany(KehadiranUser::class, 'absensi_id');
    }









}
