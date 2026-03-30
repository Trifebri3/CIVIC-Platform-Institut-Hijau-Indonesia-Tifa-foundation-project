<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileTemplate extends Model
{
    protected $fillable = [
        'field_name',  // key (e.g., 'no_hp')
        'field_label', // label (e.g., 'Nomor WhatsApp')
        'field_type',  // text, textarea, date, select, number
        'options',     // JSON untuk pilihan jika field_type adalah 'select'
        'is_required', // true/false
        'order',       // urutan tampil di form
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
    ];
}
