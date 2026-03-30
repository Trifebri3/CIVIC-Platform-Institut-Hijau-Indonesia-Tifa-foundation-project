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
// 1. Tabel Utama Pengumuman
Schema::create('announcements', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('banner')->nullable(); // Gambar ala WA
    $table->text('message');
    $table->string('link_label')->nullable();
    $table->string('link_url')->nullable();
    $table->enum('target_type', ['global', 'program'])->default('global'); // TIPE TARGET
    $table->boolean('send_email')->default(false);
    $table->timestamps();
});

// 2. Tabel Pivot Target Program
Schema::create('announcement_program', function (Blueprint $table) {
    $table->id();
    $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
    $table->foreignId('program_id')->constrained()->onDelete('cascade');
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
