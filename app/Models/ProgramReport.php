<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramReport extends Model
{
    use HasFactory;

    protected $table = 'program_reports'; // Pastikan nama tabel benar

    protected $fillable = [
        'user_id',
        'rab_period_id',
        'report_template_id',
        'program_profile_id',
        'content',
        'status',
        'admin_note',
        'submitted_at'
    ];

    protected $casts = [
        'content' => 'json', // Gunakan json untuk longtext bin
        'submitted_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function template(): BelongsTo { return $this->belongsTo(ReportTemplate::class, 'report_template_id'); }
    public function period(): BelongsTo { return $this->belongsTo(RabPeriod::class, 'rab_period_id'); }
}
