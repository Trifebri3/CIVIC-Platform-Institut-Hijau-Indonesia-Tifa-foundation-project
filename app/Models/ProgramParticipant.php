<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProgramParticipant extends Pivot
{
    protected $table = 'program_participants';

    public $incrementing = true; // Karena kita pakai ID di migrasi tadi

    protected $fillable = [
        'program_id', 'user_id', 'registration_number',
        'enrolment_method', 'enrolled_at', 'status'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Fungsi Static untuk Generate Nomor Induk Otomatis
     * Contoh Format: REG-{YEAR}-{0001}
     */
    public static function generateIdNumber(Program $program)
    {
        $format = $program->id_number_format; // Misal: CIVIC-{YEAR}-{0000}

        // Hitung urutan pendaftar ke-berapa
        $count = self::where('program_id', $program->id)->count() + 1;
        $paddedCount = str_pad($count, 4, '0', STR_PAD_LEFT); // Jadi 0001, 0002, dst

        // Replace Placeholder
        $finalId = str_replace(
            ['{YEAR}', '{MM}', '{ID}'],
            [date('Y'), date('m'), $paddedCount],
            $format
        );

        return $finalId;
    }
}
