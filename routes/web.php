<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
// Import Controllers sesuai struktur folder yang kita buat
use App\Http\Controllers\User\ActivationController;
use App\Http\Controllers\AdminProgram\DashboardController as ProgramDashboard;
use App\Http\Controllers\AdminSurat\DashboardController as SuratDashboard;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Livewire\User\ActivationStepper;
use Livewire\Volt\Volt;
use App\Models\Program;
use App\Models\SubProgramTemplate;
use App\Models\SubProgram;
use App\Models\SubProgramContent;
use App\Http\Controllers\AdminProgram\ModulUjian\ADMProgModulujiancontroller;
use App\Models\PenilaianUser;
use App\Models\ProgramKhusus;

use App\Http\Controllers\AdminProgram\TorExportController;

use App\Http\Controllers\Public\MapController;








// Public Routes (Tanpa Login)
Route::view('/', 'welcome')->name('welcome');

Route::get('/public-map', function () {
    return view('pages.public.map-index');
})->name('public.map');

Route::get('/programs/speak-justice', function () {
    return view('public.programs.speak-justice');
})->name('public.speak-justice');

Route::get('/about', function () {
    return view('public.about');
})->name('public.about');

Route::get('/public-map', [MapController::class, 'index'])->name('public.map');










// ROUTE GRUP UNTUK SEMUA USER YANG SUDAH LOGIN & VERIFIKASI EMAIL
Route::middleware(['auth', 'verified'])->group(function () {



// 1. ROLE: USER (PESERTA)
Route::middleware(['auth', 'role:user'])->prefix('participant')->group(function () {

    // GERBANG 1: Aktivasi (Bisa diakses jika is_activated = 0)
    Route::get('/verifikasi-pertanyaan', function () {
        return view('pages.user.activation');
    })->name('user.verifikasipertanyaan');

    // GERBANG 2: Lengkapi Profil (Bisa diakses jika is_activated = 1 & is_profile_completed = 0)
    Route::get('/edit-profile', function () {
        return view('pages.user.profile-edit');
    })->name('user.profile.edit');

    // SEMUA HALAMAN BERIKUTNYA (WAJIB LULUS SEMUA GERBANG)
    Route::middleware(['check.profile'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\User\DashboardController::class, 'index'])
            ->name('user.dashboard');

        Route::get('/settings', function () {
            return view('pages.user.settings');
        })->name('user.settings');
    });




});

Route::middleware(['auth'])->name('user.')->group(function () {

Route::get('/programs', function() {
        return view('pages.user.program.index'); // View yang memanggil @livewire('user.program.index')
    })->name('programs.index');
    // Nama rute ini otomatis jadi 'user.my-programs'
    Route::get('/my-programs', function() {
        return view('pages.user.program.my-programs');
    })->name('my-programs');
    // Detail Program & Form Pendaftaran
    Route::get('/programs/{program:slug}', function(Program $program) {
        return view('pages.user.program.show', compact('program'));
    })->name('programs.show');

});

Route::prefix('user/subprogram')->name('user.subprogram.')->middleware(['auth'])->group(function () {

    // Halaman List Program -> Namanya: user.subprogram.index
    Route::get('/', function () {
        return view('pages.user.subprogram.index');
    })->name('index');

    // Halaman Detail -> Namanya: user.subprogram.show
    // Hapus '/user/subprogram' di awal karena sudah ada di prefix
    Route::get('/{subProgram:slug}', function (App\Models\SubProgram $subProgram) {
        return view('pages.user.subprogram.show', ['subProgram' => $subProgram]);
    })->name('show');

});

// Grouping untuk area User
Route::middleware(['auth', 'verified'])->group(function () {

    // Route untuk detail kelas/sub-program
    Volt::route('/user/kelas/{subProgram}', 'user.kelas.show')
        ->name('user.kelas.show');

});

Route::get('/belajar/ujian/{id}', function($id) {
    return view('pages.user.modul-ujian.ujian-pengerjaan', ['id' => $id]);
})->middleware(['auth'])->name('user.ujian.show');


Route::get('/program/ujian/{modul_id}', function ($modul_id) {
    return view('pages.user.ujian.kerjakan', ['id' => $modul_id]);
})->name('user.ujian.kerjakan')->middleware(['auth']);

Route::get('my-progress', function () {
    return view('pages.user.progress-index');
})->name('user.progress')->middleware(['auth']);

// Route khusus untuk Stream PDF Raport
Route::get('my-progress/export-pdf', [App\Http\Controllers\User\ProgressController::class, 'exportPdf'])
    ->name('user.progress.pdf')
    ->middleware(['auth']);


Route::get('/certificates', function () {
        return view('pages.user.certificates'); // File ini yang manggil Volt @livewire('user.penilaian.index')
    })->name('user.certificates');


Route::get('/admin/program-management', function () {
        return view('pages.admin-program.khusus.program-management');
    })->name('admin.program.management');

Route::get('/program-khusus/{id}', function($id) {
    $program = App\Models\ProgramKhusus::with('contents')->findOrFail($id);
    return view('pages.user.program-khusus.user-dashboard', compact('program'));
})->name('user.programkhusus.view');

Route::get('/program-khusus/submit/{period}', function($period) {
    $period = \App\Models\TorPeriod::findOrFail($period);
    return view('pages.user.submit-tor', ['period' => $period]);
})->name('user.tor.submit');

// Route untuk Riwayat Pengajuan User
Route::get('/tor/history', function () {
    return view('pages.user.tor.history'); // Sesuaikan dengan nama file history Bos
})->middleware(['auth'])->name('user.tor.history');

// Route untuk Form Pengajuan RAB (Isi/Draft/Preview)
Route::get('/user/rab/period/{period}', function (App\Models\RabPeriod $period) {
    return view('pages.user.rab.form', ['period' => $period]);
})->middleware(['auth'])->name('user.rab.submit');




Route::get('/program-profile', function () {
        return view('pages.user.program-profile');
    })->name('user.program-profile');


Route::get('/report/submit/{period}', function (App\Models\RabPeriod $period) {
        return view('pages.user.reports.create', ['period' => $period]);
    })->name('user.report.submit');










// Contoh penulisan di web.php
Route::get('/admin/program-report/pdf/{id}', [App\Http\Controllers\Admin\ProgramReportController::class, 'generatePdf'])
    ->name('admin.program-report.pdf'); // <--- Nama ini harus SAMA PERSIS

// Contoh penulisan di web.php
// Tambahkan ini di group middleware auth user
Route::get('/user/report/pdf/{id}', [App\Http\Controllers\Admin\ProgramReportController::class, 'generatePdf'])
    ->name('user.report.pdf');




Route::get('/user/surat/download/{id}', [App\Http\Controllers\Admin\SuratController::class, 'generatePdf'])
    ->name('user.surat.download');

Route::view('/pengajuan-surat', 'pages.user.administrasi.surat-index')
        ->name('user.surat.index');



















    Route::get('/admin/tor-approval', function () {
        return view('pages.admin-program.tor.index');
    })->name('admin.tor.approval');










    // 2. ROLE: ADMIN PROGRAM
// Ubah 'admin-program.' menjadi 'adminprogram.'
Route::prefix('adminprogram')->name('adminprogram.')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminProgram\DashboardController::class, 'index'])->name('dashboard');
});


  Route::prefix('program-admin')->name('admin-program.')->middleware(['auth'])->group(function () {

    // Dashboard: admin-program.dashboard


    // Group Content: admin-program.content.*
    Route::prefix('content')->name('content.')->group(function () {

        // admin-program.content.index
        Route::get('/', function () {
            return view('pages.admin-program.content.index');
        })->name('index');

        // admin-program.content.create
        Route::get('/create', function () {
            return view('pages.admin-program.content.create');
        })->name('create');

        // admin-program.content.edit
        Route::get('/{subProgram}/edit', function (App\Models\SubProgram $subProgram) {
            if (!auth()->user()->managedPrograms->contains($subProgram->program_id)) {
                abort(403);
            }
            return view('pages.admin-program.content.edit', ['subProgram' => $subProgram]);
        })->name('edit');
    });

Route::get('/subprogram', function () {
        return view('pages.admin-program.subprogram.index');
    })->name('subprogram.index');

    // Halaman Detail SubProgram (Show - List Materi di dalamnya)
    Route::get('/subprogram/{subProgram}', function (App\Models\SubProgram $subProgram) {
        return view('pages.admin-program.subprogram.show', ['subProgram' => $subProgram]);
    })->name('subprogram.show');

    // Route Builder yang tadi (Sesuaikan namanya agar sinkron)
    Route::get('/subprogram/{subProgram}/content-builder', function(App\Models\SubProgram $subProgram) {
        return view('pages.admin-program.subprogram.builder', ['subProgram' => $subProgram]);
    })->name('subprogram.builder');

// Route Edit
// Ubah route edit-nya jadi begini saja:
Route::get('/subprogram/{subProgram}/edit-materi', function(App\Models\SubProgram $subProgram) {
    return view('pages.admin-program.subprogram.content.edit', [
        'subProgram' => $subProgram
    ]);
})->name('subprogram.content.edit');

// routes/web.php

// routes/web.php












});



Route::get('/admin/subprogram/{subProgram}/view', function ($subProgram) {
    $subProgramData = \App\Models\SubProgram::with('program')->findOrFail($subProgram);

    return view('pages.admin-program.subprogram.show-content', [
        'subProgram' => $subProgramData
    ]);
})->name('admin-program.subprogram.isicontents'); // NAMA ROUTE HARUS SAMA DENGAN DI BLADE


// routes/web.php

// Route untuk edit Meta Data (Title, Slug, dll) dari SubProgramContent
Route::get('/admin/modul-kelas/{content}/edit', function ($content) {
    $contentData = \App\Models\SubProgramContent::findOrFail($content);

    return view('pages.admin-program.subprogram.edit-content', [
        'content' => $contentData
    ]);
})->name('modulkelas.edit'); // NAMA ROUTE SESUAI REQUEST: modulkelas.edit



// 2. ROUTE BARU (URL kita bedakan sedikit agar tidak tabrakan)
Route::get('/admin/modul-kelas/{content}/settings', function ($content) {
    $contentData = \App\Models\SubProgramContent::findOrFail($content);
    return view('pages.admin-program.subprogram.edit-contentkelas', [
        'content' => $contentData
    ]);
})->name('editkontenkelas');

Route::get('/kelas-pertemuan', function () {
    return view('pages.admin-program.kelas.index'); // Buat file blade kosong yang memanggil @livewire('admin-program.kelas-pertemuan')
})->name('admin-program.kelas.pertemuan');

Route::get('/admin/sub-program/{sub_program_id}/modul-ujian', function ($sub_program_id) {
    $subProgram = \App\Models\SubProgram::findOrFail($sub_program_id);
    return view('pages.admin-program.modul-ujian.index', compact('subProgram'));
})->name('admin.modul-ujian.index');



Route::prefix('admin-program')->middleware(['auth'])->group(function () {

Route::get('/modul-ujian/{id}/grading', function ($id) {
    // Pastikan folder 'pages', 'admin-program', dan 'modul-ujian' ada di dalam resources/views
    return view('pages.admin-program.modul-ujian.grading-center', ['id' => $id]);
})->name('admin.modul-ujian.grading');


    // Route Cetak PDF
    Route::get('/modul-ujian/{id}/rekap-pdf', [ADMProgModulujiancontroller::class, 'rekapSemua'])
        ->name('admin.modul-ujian.rekap-pdf');

    Route::get('/jawaban-ujian/{id}/pdf-satuan', [ADMProgModulujiancontroller::class, 'cetakSatuan'])
        ->name('admin.modul-ujian.cetak-satuan');
});


Route::get('/admin-program/modul-ujian/{id}/kelola-pg', function ($id) {
    return view('pages.admin-program.modul-ujian.kelola-pg', ['id' => $id]);
})->name('admin.modul-ujian.kelola-pg');

Route::get('/admin/program/content/{subProgram:slug}/attendance', function (SubProgram $subProgram) {
        return view('pages.admin-program.absensi.manage', [
            'subProgram' => $subProgram
        ]);
    })->name('admin-program.absensi.manage');


Route::get('/admin/program/content/{subProgram:slug}/rekap-absensi', function (SubProgram $subProgram) {
    return view('pages.admin-program.absensi.rekap', ['subProgram' => $subProgram]);
})->name('admin-program.absensi.rekap');





Route::get('admin/program/{program:slug}/global-tracking', function (Program $program) {
    return view('pages.admin-program.tracking-global', [
        'program' => $program
    ]);
})->name('admin.program.tracking.global')->middleware(['auth']);

Route::get('admin/program/{program:slug}/tracking/user/{user}', function (Program $program, User $user) {
    return view('pages.admin-program.tracking-user-detail', [
        'program' => $program,
        'user' => $user
    ]);
})->name('admin.program.tracking.user-detail')->middleware(['auth']);



Route::get('/penilaian', function () {
        return view('pages.admin-program.penilaian-index'); // Sesuaikan path view-nya
    })->name('admin.penilaian.index');

// Route untuk scan QR (Public)
Route::get('/verify/nilai/{secret}', function ($secret) {
    $data = PenilaianUser::with(['template', 'user'])
            ->where('qr_code_secret', $secret)
            ->firstOrFail();

    return view('pages.public.verify-nilai', compact('data'));
})->name('nilai.verify');

// Route untuk Download PDF


Route::get('/download/nilai/{secret}', [App\Http\Controllers\AdminProgram\PdfController::class, 'generateSingle'])->name('nilai.pdf');
Route::get('/download/rekap/{template_id}', [App\Http\Controllers\AdminProgram\PdfController::class, 'generateRekap'])->name('nilai.rekap');

Route::get('/admin/program-khusus/{id}/dashboard', function ($id) {
        $program = ProgramKhusus::withCount('participants')->findOrFail($id);
        return view('pages.admin-program.programkhusus.dashboardkhusus', compact('program'));
    })->name('admin.program.dashboard-khusus');

Route::get('/admin/program-khusus/manage-tor', function() {
    return view('pages.admin-program.khusus.manage-tor-page'); // Buat page pembungkusnya
})->name('admin.tor.manage');

// Route untuk halaman list jawaban user
Route::get('/admin/program-khusus/submissions', function() {
    return view('pages.admin-program.khusus.submissions-index');
})->name('admin.program.submissions');

// Route untuk download PDF (yang pakai Controller tadi)
Route::get('/admin/program-khusus/download-tor/{id}', [TorExportController::class, 'download'])
    ->name('admin.tor.download');

Route::get('/admin/program-khusus/keuangan', function() {
    return view('pages.admin-program.khusus.keuangan.index');
})->name('admin.program.keuangan.index');

Route::get('/admin/program-khusus/keuangan/create', function() {
    return view('pages.admin-program.khusus.keuangan.create');
})->name('admin.program.keuangan.create');

Route::get('/admin/program-khusus/keuangan/{period}/edit', function(App\Models\RabPeriod $period) {
    return view('pages.admin-program.khusus.keuangan.edit', compact('period'));
})->name('admin.program.keuangan.edit');


Route::get('/admin/program-khusus/keuangan/{period}/submissions', function(App\Models\RabPeriod $period) {
    return view('pages.admin-program.khusus.keuangan.submissions', compact('period'));
})->name('admin.program.keuangan.submissions');

// Route khusus download PDF (Pakai Controller biasa agar lebih stabil untuk DomPDF/Snappy)
Route::get('/admin/program-khusus/keuangan/export-pdf/{submission}', [App\Http\Controllers\AdminProgram\RabExportController::class, 'single'])->name('admin.program.keuangan.pdf.single');
Route::get('/admin/program-khusus/keuangan/export-period-pdf/{period}', [App\Http\Controllers\AdminProgram\RabExportController::class, 'batch'])->name('admin.program.keuangan.pdf.batch');

Route::get('/program-reports/{period?}', function ($period = null) {
        return view('pages.admin-program.laporan.program-reports.index', ['period_id' => $period]);
    })->name('admin.program.reports.index');



Route::get('/admin/report-template/builder/{period}', function ($periodId) {
        $period = \App\Models\RabPeriod::findOrFail($periodId);
        return view('pages.admin-program.laporan.report-template.builder', ['period' => $period]);
    })->name('admin.report-template.builder'); // <--- NAMA INI HARUS PERSIS





















    // 3. ROLE: ADMIN SURAT
    Route::middleware(['role:adminsurat'])->prefix('surat-admin')->group(function () {
        Route::get('/dashboard', [SuratDashboard::class, 'index'])->name('adminsurat.dashboard');
    });
Route::view('/admin/surat-management', 'pages.admin-surat.surat.surat-index')
    ->name('admin.surat.index')
    ->middleware(['auth', 'role:adminsurat']); // Pastikan middleware role sesuai setup kamu



Route::get('/admin/surat/export-excel', [App\Http\Controllers\Admin\SuratController::class, 'exportExcel'])->name('admin.surat.export.excel');
Route::get('/admin/surat/export-pdf', [App\Http\Controllers\Admin\SuratController::class, 'exportPdfRekap'])->name('admin.surat.export.pdf');




































    // 4. ROLE: SUPERADMIN

    });

use App\Http\Controllers\SuperAdmin\ExportController;
use App\Http\Controllers\SuperAdmin\SuperDashboardController;
// Group Superadmin (Cukup satu group besar agar tidak double prefix)
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->group(function () {

    // Dashboard
    Route::get('/dashboard', [SuperDashboardController::class, 'index'])
        ->name('superadmin.dashboard');


    Route::get('/users/download-template', [ExportController::class, 'downloadTemplate'])->name('superadmin.users.template');

    // User Management & Indexing
    Route::get('/users', [ExportController::class, 'index'])->name('superadmin.users.index');
    Route::get('/users/{id}', [ExportController::class, 'show'])->name('superadmin.users.show');

    // Export Routes (Pastikan name-nya sesuai dengan yang dipanggil di Blade)
    Route::get('/export-csv', [ExportController::class, 'exportCSV'])->name('superadmin.export'); // GANTI ke 'superadmin.export'
    Route::get('/export-pdf', [ExportController::class, 'exportPDF'])->name('superadmin.export.pdf');

    // Profile Settings (Template JSON)
    Route::get('/profile-settings', function() {
        return view('pages.super-admin.users.profile-settings'); // Sesuaikan path view kamu
    })->name('superadmin.profile-settings');

    Route::post('/users/store', [ExportController::class, 'store'])->name('superadmin.users.store');
    Route::post('/users/import', [ExportController::class, 'importExcel'])->name('superadmin.users.import');


});

use App\Http\Controllers\SuperAdmin\ActivationQuestionController;
use App\Http\Controllers\Superadmin\SuperadminActivationController;


Route::group(['prefix' => 'super-admin', 'as' => 'superadmin.'], function () {

    // 1. Group Laporan (Taruh SEMUA yang urusan cetak/rekap di sini)
    Route::prefix('activation-report')->name('activation.')->group(function () {

        Route::get('/', [SuperadminActivationController::class, 'rekap'])
            ->name('rekap');

        Route::get('/print-all', [SuperadminActivationController::class, 'printAll'])
            ->name('print-all');

        Route::get('/print-user/{id}', [SuperadminActivationController::class, 'printUser'])
            ->name('print-user');

        // PINDAHKAN KE SINI!
        // Sekarang URL-nya: /super-admin/activation-report/export-excel
        Route::get('/export-excel', [SuperadminActivationController::class, 'exportExcel'])
            ->name('export-excel');
    });

    // 2. Resource CRUD Pertanyaan (Taruh di bawah agar tidak membajak URL lain)
    Route::resource('activation', ActivationQuestionController::class);
// Pastikan rute ini ada di dalam group prefix 'super-admin'
    Route::delete('/users/{id}', [ExportController::class, 'destroy'])->name('users.destroy');

    Route::get('/users/{user}/edit', function (User $user) {
        return view('pages.super-admin.users.edit', compact('user'));
    })->name('users.edit');

    //ROUTE PROGRAM
    Route::get('/programs', function() {
        return view('pages.super-admin.program.index');
    })->name('programs.index');

    Route::get('/programs/create', function() {
        return view('pages.super-admin.program.create');
    })->name('programs.create');

    Route::get('/programs/{program}/edit', function(\App\Models\Program $program) {
        return view('pages.super-admin.program.edit', compact('program'));
    })->name('programs.edit');

// Pastikan namanya ADALAH 'programs.enroll-manual' (tanpa prefix super-admin jika kamu panggilnya begitu)
Route::get('/super-admin/programs/enroll-manual', function() {
    return view('pages.super-admin.program.enroll-manual');
})->name('programs.enroll-manual');




    Route::get('/templates', function () {
        return view('pages.super-admin.templates.index');
    })->name('templates.index');

    Route::get('/templates/create', function () {
        return view('pages.super-admin.templates.create');
    })->name('templates.create');

Route::get('/templates/{template}/edit', function (SubProgramTemplate $template) { // Tambahkan tipe data di sini
    return view('pages.super-admin.templates.edit', ['template' => $template]);
})->name('templates.edit');

    // Route untuk Show Sub-Program
    Route::get('/sub-programs/{sub_program:slug}', function ($sub_program) {
        return view('pages.super-admin.templates.show', ['subProgram' => $sub_program]);
    })->name('sub-programs.show');


Route::get('/programs/{program}/delegate', function (App\Models\Program $program) {
    return view('pages.super-admin.program.delegate', ['program' => $program]);
})->name('programs.delegate');




});


Route::middleware(['auth', 'verified'])->group(function () {

    // Gunakan name() yang spesifik sesuai panggilan di sidebar
    Route::get('/super-admin/program/invite', function () {
        return view('pages.super-admin.program.invite-page');
    })->name('admin.invite');

});


Route::get('super-admin/announcements', function () {
    return view('pages.super-admin.announcement-index');
})->name('superadmin.announcements')->middleware(['auth', 'role:superadmin']);

Route::get('/super-admin/validasi-nilai', function () {
    return view('pages.super-admin.validasi-nilai-index');
})->name('superadmin.validasi-nilai');


Route::get('/super-admin/program-khusus', function () {
    return view('pages.super-admin.programkhusus.index');
})->name('superadmin.programkhusus');




































    // Route Dashboard Utama (Pintu Masuk Awal / Redirector)
    // Kamu bisa arahkan ini ke Controller yang mengecek role dan melakukan redirect otomatis



// Route Profile Bawaan Breeze
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');


// routes/web.php
// routes/web.php
Route::get('/notifications/{id}', function ($id) {
    return view('pages.notifications.show', ['id' => $id]);
})->name('notification.detail'); // Ganti .show menjadi .detail




use App\Models\ProgramKhususParticipant;

Route::get('/program-invitation/accept/{id}', function ($id) {
    // Cari data partisipan milik user yang sedang login
    $participant = ProgramKhususParticipant::where('user_id', auth()->id())
        ->findOrFail($id);

    // Update status jadi aktif
    $participant->update([
        'is_active' => true,
        'joined_at' => now()
    ]);

    return back()->with('success', 'Selamat! Anda resmi bergabung dalam program.');
})->name('program.accept')->middleware('auth');





require __DIR__.'/auth.php';
