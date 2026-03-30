<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modul_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_program_id')->constrained()->onDelete('cascade');
            $table->string('judul');
            $table->text('instruksi')->nullable();

            // Kolom Sakti: Menyimpan semua lampiran (PDF, Gambar, Link Pendukung)
            $table->json('lampiran_instruksi')->nullable();

            // Kolom Inti: Menyimpan array soal (tipe, pertanyaan, pilihan, dll)
            $table->json('konfigurasi_soal');

            // Pengaturan Waktu & Jenis
            $table->dateTime('deadline')->nullable();
            $table->enum('tipe_ujian', ['tugas', 'kuis', 'ujian_akhir'])->default('tugas');

            // Status & Akses
            $table->boolean('is_active')->default(true);
            $table->integer('max_attempts')->default(1); // Berapa kali boleh ngulang

            $table->timestamps();
        });

        // Tabel untuk Jawaban User & Feedback Admin
        Schema::create('jawaban_ujians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modul_ujian_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Menyimpan jawaban user dalam bentuk JSON
            $table->json('konten_jawaban');

            // Sistem Penilaian
            $table->integer('nilai')->nullable();
            $table->text('feedback_admin')->nullable();
            $table->dateTime('graded_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_ujians');
        Schema::dropIfExists('modul_ujians');
    }
};
