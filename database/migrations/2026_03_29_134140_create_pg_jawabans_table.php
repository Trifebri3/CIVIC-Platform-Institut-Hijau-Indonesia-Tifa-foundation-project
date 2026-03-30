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
    Schema::create('pg_jawabans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('modul_ujian_id')->constrained('modul_ujians')->onDelete('cascade');
        $table->json('list_jawaban'); // Simpan jawaban user: {"soal_1": "A", "soal_2": "C"}
        $table->integer('total_benar')->default(0);
        $table->integer('total_salah')->default(0);
        $table->decimal('skor_akhir', 5, 2)->default(0); // Nilai 0-100
        $table->timestamp('submitted_at')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pg_jawabans');
    }
};
