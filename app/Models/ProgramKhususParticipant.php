<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramKhususParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'program_khusus_id',
        'access_role',
        'is_active',
        'invited_at',
        'joined_at',
        'invitation_code',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'invited_at' => 'datetime',
        'joined_at' => 'datetime',
    ];

    /**
     * Relasi ke User (Siapa yang diundang)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Sub Program (Ke program mana dia diundang)
     */
    public function subProgram(): BelongsTo
    {
        return $this->belongsTo(SubProgram::class, 'sub_program_id');
    }

    /**
     * Scope untuk filter yang aktif saja
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Di dalam model ProgramKhususParticipant
public function program()
{
    return $this->belongsTo(ProgramKhusus::class, 'program_khusus_id');
}


}
