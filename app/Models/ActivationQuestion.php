<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ActivationQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'story',
        'image',
        'example_answer',
        'response_definitions', // Ini JSON isinya: type, label, options, dll
        'order',
        'is_active'
    ];

protected $casts = [
        'response_definitions' => 'array',
        'is_active' => 'boolean',

    ];


    /**
     * Helper: Ambil URL Gambar Pertanyaan
     */
    public function getImageUrlAttribute()
    {
        return $this->image
            ? Storage::url($this->image)
            : 'https://ui-avatars.com/api/?name=Question&background=800000&color=fff';
    }

    /**
     * Relasi ke Jawaban User
     */
    public function answers()
    {
        return $this->hasMany(ActivationAnswer::class);
    }

    /**
     * Scope: Hanya ambil pertanyaan yang aktif & urutkan
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order', 'asc');
    }
}
