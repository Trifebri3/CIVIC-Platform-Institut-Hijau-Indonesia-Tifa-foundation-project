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
    Schema::create('absensis', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sub_program_id')->constrained()->onDelete('cascade');

        $table->string('title'); // Contoh: "Pre-Test Materi AI"
        $table->enum('type', ['regular', 'pre_test', 'post_test'])->default('regular');

        // Security & Protection
        $table->string('auth_code')->nullable(); // Kode unik (misal: "ABX123")
        $table->boolean('is_protected')->default(false); // Pakai kode atau tidak

        // Waktu & Durasi
        $table->dateTime('open_at')->nullable();
        $table->integer('duration_minutes')->default(30);

        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
