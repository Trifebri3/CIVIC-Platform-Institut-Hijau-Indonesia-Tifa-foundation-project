<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Template Laporan (Dibuat Admin per Periode)
        Schema::create('report_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rab_period_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Contoh: Laporan Pertanggungjawaban Tahap 1
            $table->json('fields'); // Struktur Form: [{"label": "Foto", "type": "image"}, {"label": "PDF", "type": "file"}]
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel Laporan (Diisi User)
        Schema::create('program_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('rab_period_id')->constrained()->onDelete('cascade');
            $table->foreignId('report_template_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_profile_id')->constrained()->onDelete('cascade');

            $table->json('content'); // Data inputan user (Teks, Nama File, dll)
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->text('admin_note')->nullable(); // Catatan revisi dari admin

            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_reports');
        Schema::dropIfExists('report_templates');
    }
};
