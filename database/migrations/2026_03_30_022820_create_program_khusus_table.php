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
Schema::create('program_khusus', function (Blueprint $table) {
    $table->id();
    $table->string('nama_program'); // Contoh: "Elite Web Dev Mentoring"
    $table->string('slug')->unique(); // Untuk URL cantik: elite-web-dev
    $table->text('deskripsi_singkat')->nullable();
    $table->longText('konten_eksklusif')->nullable(); // Bisa isi HTML/Markdown buat timeline/info

    // Branding & Visual
    $table->string('banner_url')->nullable(); // Foto premium buat header dashboard
    $table->string('warna_tema')->default('#800000'); // Warna identitas program

    // Status & Periode
    $table->dateTime('start_at')->nullable();
    $table->dateTime('end_at')->nullable();
    $table->boolean('is_active')->default(true);
    $table->integer('max_quota')->default(0); // 0 berarti unlimited

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_khusus');
    }
};
