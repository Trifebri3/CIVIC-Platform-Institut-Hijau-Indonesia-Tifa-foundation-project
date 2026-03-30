<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramProfile extends Model
{
    use HasFactory;

    /**
     * Karena profil ini global dan unik per user,
     * kita pastikan fillable tidak mengandung rab_period_id.
     */
    protected $fillable = [
        'user_id',
        'program_name',
        'address',
        'province',
        'city_regency',
        'district',
        'village',
        'latitude',
        'longitude',
        
        'coordinator_name',
        'coordinator_phone',
        'main_photo',
        'is_completed',
    ];

    /**
     * Casting status agar terbaca sebagai boolean murni di aplikasi.
     */
    protected $casts = [
        'is_completed' => 'boolean',
    ];

    /**
     * Relasi ke User (Owner dari Profil ini).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor untuk mendapatkan URL foto utama.
     * Sangat berguna untuk menampilkan foto di Blade: <img src="{{ $profile->photo_url }}">
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->main_photo) {
            return asset('storage/' . $this->main_photo);
        }

        // Fallback jika foto kosong, menggunakan UI Avatars
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->program_name) . '&background=random&color=fff';
    }

    /**
     * Helper untuk mendapatkan alamat lengkap dalam satu baris.
     */
    public function getFullAddressAttribute()
    {
        return "{$this->address}, Desa {$this->village}, Kec. {$this->district}, {$this->city_regency}, {$this->province}";
    }
}
