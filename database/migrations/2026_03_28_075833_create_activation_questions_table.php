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
    Schema::create('activation_questions', function (Blueprint $table) {
        $table->id();
        $table->string('title'); // Judul Pertanyaan
        $table->text('story')->nullable(); // Narasi/Cerita Pendukung
        $table->string('image')->nullable(); // Foto Pertanyaan
        $table->text('example_answer')->nullable(); // Contoh Jawaban (Hint)

        /** * Kolom 'settings' ini akan menyimpan JSON:
         * [
         * {"type": "text", "label": "Nama Lengkap", "required": true},
         * {"type": "select", "label": "Pilih Divisi", "options": ["IT", "HR"]},
         * {"type": "file", "label": "Upload KTP", "mimes": "pdf,jpg"}
         * ]
         */
        $table->json('response_definitions');

        $table->integer('order')->default(0); // Urutan tampil
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activation_questions');
    }
};
