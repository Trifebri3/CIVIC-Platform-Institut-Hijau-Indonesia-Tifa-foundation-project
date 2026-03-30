<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat_submissions', function (Blueprint $table) {
            $table->id();

            // Relasi User (Siapa yang mengajukan)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // --- BAGIAN HEADER (WARNA MERAH DI TEMPLATE) ---
            // Contoh: CE/001/UND/03/2026 (Diisi oleh adminsurat)
            $table->string('nomor_surat')->nullable();
            // Tanggal Surat (Kanan Atas)
            $table->date('tanggal_surat')->nullable();
            // Jumlah Berkas (Lampiran)
            $table->string('lampiran')->default('-');
            // Lokasi di bagian Perihal (Contoh: Jakarta / Raja Ampat)
            $table->string('wilayah_kegiatan');

            // --- BAGIAN ISI (YTH & DETAIL AGENDA) ---
            $table->string('penerima_surat'); // Yth. Bapak/Ibu...
            $table->string('hari_tanggal');   // Hari / Tanggal pelaksanaan
            $table->string('waktu_pelaksanaan'); // Waktu
            $table->string('tempat_pelaksanaan'); // Tempat

            // Narahubung (Bagian Merah di bawah)
            $table->string('kontak_person');

            // --- STATUS & MANAGEMENT ---
            // 'pending' = baru masuk, 'approved' = nomor sudah diisi & siap cetak, 'rejected' = ditolak
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Siapa admin yang memproses (Role: adminsurat)
            $table->foreignId('processed_by')->nullable()->constrained('users');

            // Catatan jika ditolak atau perlu revisi
            $table->text('admin_note')->nullable();

            // Path file PDF jika sudah di-generate (Opsional)
            $table->string('file_pdf_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_submissions');
    }
};
