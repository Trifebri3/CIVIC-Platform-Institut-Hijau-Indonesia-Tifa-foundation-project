<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubProgramContent extends Model
{
    protected $guarded = [];

    // WAJIB: Agar JSON otomatis jadi Array saat dipanggil di Blade
    protected $casts = [
        'modules' => 'array',
    ];

    public function subProgram()
    {
        return $this->belongsTo(SubProgram::class);
    }

    /**
     * Cek apakah materi ini sudah diselesaikan oleh user tertentu
     */
    public function isDoneBy($userId)
    {
        return UserProgress::where('user_id', $userId)
            ->where('sub_program_content_id', $this->id)
            ->exists();
    }

    
}
