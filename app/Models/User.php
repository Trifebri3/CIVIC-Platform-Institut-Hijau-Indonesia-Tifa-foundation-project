<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;



class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // superadmin, adminprogram, adminsurat, user
        'initial_questions_data', // Data JSON aktivasi awal
        'avatar',             // TAMBAHKAN INI
        'is_activated',
    'is_profile_completed', // TAMBAHKAN INI
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'initial_questions_data' => 'array', // Otomatis jadi array saat dipanggil
    ];

    // RELASI KE PROFILE
// app/Models/User.php
public function profile()
{
    return $this->hasOne(Profile::class, 'user_id', 'id');
}

    // HELPER UNTUK CEK ROLE (Bisa dipakai di Blade/Controller)
    // Contoh: if(auth()->user()->isAdminProgram())
    public function isSuperAdmin() { return $this->role === 'superadmin'; }
    public function isAdminProgram() { return $this->role === 'adminprogram'; }
    public function isAdminSurat() { return $this->role === 'adminsurat'; }
    public function isUser() { return $this->role === 'user'; }

    public function sendEmailVerificationNotification()
{
    $this->notify(new CustomVerifyEmail);
}

public function sendPasswordResetNotification($token)
{
    $this->notify(new CustomResetPassword($token));
}

/**
 * Relasi ke semua jawaban aktivasi yang pernah diisi user
 */
public function activationAnswers()
{
    return $this->hasMany(ActivationAnswer::class);
}

/**
 * Cek apakah user sudah menjawab semua pertanyaan wajib
 */
public function hasCompletedActivation()
{
    return $this->is_profile_completed === true; // Atau logika jumlah jawaban
}

public function enrolledPrograms(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
{
    return $this->belongsToMany(Program::class, 'program_participants')
                ->using(ProgramParticipant::class) // Menghubungkan ke Pivot Model tadi
                ->withPivot(['registration_number', 'enrolment_method', 'status', 'enrolled_at'])
                ->withTimestamps();
}

public function isEnrolledIn($programId)
{
    return $this->enrolledPrograms()->where('program_id', $programId)->exists();
}

// app/Models/User.php

public function managedPrograms()
{
    // Relasi ke tabel programs melalui tabel pivot program_admin
    return $this->belongsToMany(Program::class, 'program_admin', 'user_id', 'program_id');
}


public function progresses()
{
    return $this->hasMany(UserProgress::class);
}

/**
 * Cek cepat: Apakah user ini sudah menyelesaikan materi X?
 * Contoh: if(Auth::user()->hasCompleted($contentId)) { ... }
 */
public function hasCompleted($contentId)
{
    return $this->progresses()->where('sub_program_content_id', $contentId)->exists();
}


/**
 * Relasi ke materi yang sudah diselesaikan (Progress)
 */
public function progress()
{
    return $this->belongsToMany(
        SubProgramContent::class,
        'user_progress',
        'user_id',
        'sub_program_content_id' // <--- SESUAIKAN DENGAN HASIL TERMINAL TADI
    )->withPivot('completed_at')->withTimestamps();
}

/**
 * Relasi ke nilai ujian
 */
public function jawaban_ujians()
{
    return $this->hasMany(JawabanUjian::class, 'user_id');
}
/**
 * Relasi ke Program yang diikuti oleh User
 */
public function programs()
{
    // Asumsi nama tabel pivotnya adalah 'program_user'
    return $this->belongsToMany(Program::class, 'program_user', 'user_id', 'program_id')
                ->withTimestamps();
}

/**
 * Cek apakah user punya akses ke Program Khusus
 */
public function hasSpecialAccess(): bool
{
    return $this->hasOne(ProgramKhususParticipant::class)
                ->where('is_active', true)
                ->exists();
}

/**
 * Ambil detail partisipasi program khusus
 */
public function specialProgramDetails()
{
    return $this->hasOne(ProgramKhususParticipant::class)->where('is_active', true);
}

/**
 * Relasi ke Profil Program.
 */
public function programProfile()
{
    return $this->hasOne(ProgramProfile::class);
}
/**
 * Check apakah user sudah melengkapi profil untuk periode tertentu.
 */
public function hasCompletedProfile($periodId)
{
    return $this->programProfile()
        ->where('rab_period_id', $periodId)
        ->where('is_completed', true)
        ->exists();
}




/**
 * Cek apakah user boleh mengajukan RAB di periode baru.
 * Syarat: Laporan periode aktif sebelumnya harus sudah APPROVED.
 */
public function canApplyForNewRab()
{
    // 1. Cari pengajuan RAB terakhir milik user yang statusnya APPROVED
    $lastApprovedRab = $this->hasMany(RabSubmission::class)
        ->where('status', 'approved')
        ->latest()
        ->first();

    // Jika belum pernah punya RAB yang di-ACC, boleh buat baru
    if (!$lastApprovedRab) {
        return true;
    }

    // 2. Cek apakah ada laporan (ProgramReport) untuk periode tersebut yang sudah APPROVED
    $isReportCleared = ProgramReport::where('user_id', $this->id)
        ->where('rab_period_id', $lastApprovedRab->rab_period_id)
        ->where('status', 'approved')
        ->exists();

    return $isReportCleared;
}

/**
 * Daftar pengajuan surat milik user ini
 */
public function suratSubmissions()
{
    return $this->hasMany(SuratSubmission::class);
}






}
