<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubProgramTemplate extends Model
{
    protected $guarded = [];

    // CASTING: field_schema otomatis jadi Array/Object
    protected $casts = [
        'fields_schema' => 'array',
    ];

    /**
     * Relasi ke SubProgram yang menggunakan template ini
     */
    public function subPrograms()
    {
        return $this->hasMany(SubProgram::class, 'template_id');
    }

    /**
     * Helper: Menampilkan icon atau warna default jika kosong
     */
    public function getIconAttribute($value)
    {
        return $value ?? 'heroicon-o-document-text';
    }
}
