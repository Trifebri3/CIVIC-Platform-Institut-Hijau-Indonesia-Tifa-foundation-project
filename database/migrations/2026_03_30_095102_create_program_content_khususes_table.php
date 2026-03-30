<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('program_contents', function (Blueprint $table) {
        $table->id();
        // Relasi ke Program Utama
        $table->foreignId('program_khusus_id')->constrained('program_khusus')->onDelete('cascade');

        // Tipe konten: 'timeline', 'asset', 'announcement'
        $table->string('type')->index();

        // Judul Konten (Contoh: "Modul Pertemuan 1" atau "Jadwal Praktikum")
        $table->string('title');

        // Kolom JSON Sakti: Simpan array data apa saja di sini
        // Timeline: ['date' => '...', 'location' => '...']
        // Asset: ['file_path' => '...', 'link' => '...', 'size' => '...']
        $table->json('data')->nullable();

        $table->integer('order')->default(0); // Buat urutan tampilan
        $table->boolean('is_visible')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_content_khususes');
    }
};
