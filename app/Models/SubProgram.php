<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubProgram extends Model
{
    protected $guarded = [];

    protected $casts = [
        'content_data' => 'array',
        'status' => 'string',
        'deadline' => 'datetime',
    ];

    /**
     * RELASI: Ke Program Utama
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * RELASI: Ke Daftar Materi (Modular Content)
     */
    public function contents()
    {
        return $this->hasMany(SubProgramContent::class)->orderBy('order_position', 'asc');
    }
public function template()
    {
        return $this->belongsTo(SubProgramTemplate::class, 'template_id');
    }

    /**
     * Relasi ke Program Utama
     */

    /**
     * LOGIC: Menghitung Persentase Progres User Aktif
     * Cara pakai di Blade: {{ $subProgram->user_progress_percent }}%
     */
    public function getUserProgressPercentAttribute()
    {
        $userId = Auth::id();
        if (!$userId) return 0;

        $totalMateri = $this->contents()->count();
        if ($totalMateri === 0) return 0;

        $materiSelesai = UserProgress::where('user_id', $userId)
            ->whereIn('sub_program_content_id', $this->contents()->pluck('id'))
            ->count();

        return round(($materiSelesai / $totalMateri) * 100);
    }

    /**
     * LOGIC: Cek apakah user sudah menyelesaikan seluruh materi di SubProgram ini
     */
    public function isCompletedByUser($userId = null)
    {
        $userId = $userId ?? Auth::id();
        return $this->getUserProgressPercentAttribute() === 100;
    }

    /**
     * HELPER: Ambil data dari JSON content_data
     */
    public function getContent($key, $default = null)
    {
        return data_get($this->content_data, $key, $default);
    }

    /**
     * BOOT: Auto-slug
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title) . '-' . Str::lower(Str::random(5));
            }
        });
    }

public function modulUjians()
{
    return $this->hasMany(ModulUjian::class, 'sub_program_id')->where('is_active', true);
}




public function calculateUserAttendance($userId)
{
    // Ambil semua absen yang aktif di sub ini
    $activeAbsens = $this->absensis()->where('is_active', true)->get();
    $totalRequired = $activeAbsens->count();

    if ($totalRequired === 0) return 1; // Jika tidak ada absen, anggap hadir penuh

    // Hitung berapa kali user absen
    $userAttended = KehadiranUser::whereIn('absensi_id', $activeAbsens->pluck('id'))
        ->where('user_id', $userId)
        ->count();

    // Sesuai Request Bos: Jika ada banyak absen, nilainya tetap dikompres jadi 1
    // Return 1 jika semua absen diikuti, return 0.x jika baru sebagian
    return $userAttended >= $totalRequired ? 1 : ($userAttended / $totalRequired);
}

public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class, 'sub_program_id');
    }





















}
