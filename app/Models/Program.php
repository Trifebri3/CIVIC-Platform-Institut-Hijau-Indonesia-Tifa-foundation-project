<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Program extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'banner',
        'registration_start', 'registration_end', 'quota',
        'is_open', 'redeem_code', 'id_number_format',
        'use_global_id', 'status'
    ];

    protected $casts = [
        'registration_start' => 'datetime',
        'registration_end' => 'datetime',
        'is_open' => 'boolean',
        'use_global_id' => 'boolean',
    ];

    // Otomatis buat SLUG saat simpan nama program
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($program) {
            $program->slug = Str::slug($program->name);
        });
    }

    /**
     * Relasi ke Peserta (User) melalui tabel pivot ProgramParticipant
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'program_participants')
                    ->withPivot(['registration_number', 'enrolment_method', 'status', 'enrolled_at'])
                    ->withTimestamps();
    }

    /**
     * Cek apakah pendaftaran masih dibuka berdasarkan waktu & kuota
     */
public function isRegistrationOpen()
    {
        $now = now();

        // Cek Range Waktu
        $isInTimeRange = $now->between($this->registration_start, $this->registration_end);

        // Cek Kuota (Jika quota 0 dianggap unlimited)
        $isQuotaAvailable = $this->quota === 0 || ($this->participants()->count() < $this->quota);

        // Cek Status Admin
        $isActive = $this->status === 'active';

        return $isInTimeRange && $isQuotaAvailable && $isActive;
    }

    public function admins()
{
    return $this->belongsToMany(User::class, 'program_admin');
}



public function subPrograms()
    {
        return $this->hasMany(SubProgram::class);
    }

/**
 * Relasi ke Mahasiswa yang ikut Program ini
 * Kita gunakan tabel 'program_participants' sebagai jembatannya
 */
public function users()
{
    return $this->belongsToMany(User::class, 'program_participants', 'program_id', 'user_id');
}
/**
 * Relasi ke Pengumuman yang ditargetkan ke program ini.
 */
public function announcements(): BelongsToMany
{
    return $this->belongsToMany(Announcement::class, 'announcement_program');
}


}
