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
    Schema::create('kehadiran_users', function (Blueprint $table) {
        $table->id();
        $table->foreignId('absensi_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');

        $table->dateTime('submitted_at');
        $table->decimal('score', 5, 2)->default(1); // Default 1 (hadir), bisa 100 kalau pre/post test
        $table->string('status')->default('present'); // present, late, alpha

        $table->timestamps();
        $table->unique(['absensi_id', 'user_id']); // Proteksi Double Absen
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kehadiran_users');
    }
};
