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
    Schema::create('validasinilais', function (Blueprint $table) {
        $table->id();
        $table->string('template_name'); // Contoh: "E-Raport Semester 1" atau "Sertifikat Web Dev"
        $table->json('schema'); // Struktur/Kriteria Nilai (Nama kriteria, Min, Max)
        $table->json('data_nilai'); // Inputan nilainya (JSON Key-Value)
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Pemilik nilai
        $table->string('status')->default('draft'); // draft, validated, published
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validasinilais');
    }
};
