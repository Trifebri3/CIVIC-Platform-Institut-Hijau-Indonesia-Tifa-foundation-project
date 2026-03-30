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
    Schema::create('pg_moduls', function (Blueprint $table) {
        $table->id();
        $table->foreignId('modul_ujian_id')->constrained('modul_ujians')->onDelete('cascade');
        $table->text('pertanyaan');
        $table->json('opsi'); // Simpan ["A", "B", "C", "D"]
        $table->string('kunci_jawaban'); // Jawaban yang benar (string sesuai isi opsi)
        $table->integer('poin')->default(10); // Poin per soal
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pg_moduls');
    }
};
