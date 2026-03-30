<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ActivationAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activation_question_id',
        'content' // Ini JSON isinya: { "field_id": "jawaban", "field_file": "path/file.jpg" }
    ];

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Relasi balik ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi balik ke Pertanyaan
     */
    public function question()
    {
        return $this->belongsTo(ActivationQuestion::class, 'activation_question_id');
    }

    /**
     * Helper: Ambil jawaban spesifik berdasarkan ID input di JSON
     */
    public function getResponse($fieldId)
    {
        return $this->content[$fieldId] ?? null;
    }

    /**
     * Helper: Jika ada jawaban berupa file, ambil URL-nya
     */
    public function getFileUrl($fieldId)
    {
        $path = $this->getResponse($fieldId);
        return $path ? Storage::url($path) : null;
    }
}
