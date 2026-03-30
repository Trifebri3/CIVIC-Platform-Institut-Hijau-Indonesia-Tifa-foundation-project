<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('rab_period_id')->constrained()->onDelete('cascade'); // Menghubungkan ke periode aktif

            // Detail Lokasi & Identitas
            $table->string('program_name');
            $table->text('address');
            $table->string('province');
            $table->string('city_regency'); // Kabupaten/Kota
            $table->string('district');      // Kecamatan
            $table->string('village');       // Kelurahan/Desa

            // Geospasial & Kontak
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('coordinator_name');
            $table->string('coordinator_phone');

            // Media
            $table->string('main_photo')->nullable(); // Foto Utama Kegiatan/Lokasi

            // Status pengisian
            $table->boolean('is_completed')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_profiles');
    }
};
