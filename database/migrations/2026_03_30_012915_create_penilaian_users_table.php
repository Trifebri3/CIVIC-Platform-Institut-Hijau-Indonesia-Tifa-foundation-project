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
    Schema::create('penilaian_users', function (Blueprint $table) {
        $table->id();
        // Hubungkan ke Template mana yang dipakai
        $table->foreignId('validasinilai_id')->constrained('validasinilais')->onDelete('cascade');
        // Hubungkan ke User mana yang dinilai
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        // Simpan hasil inputan nilainya di sini
        $table->json('isi_nilai');
        $table->string('status')->default('published');
        $table->string('qr_code_secret')->unique(); // Untuk validasi QR
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_users');
    }
};
