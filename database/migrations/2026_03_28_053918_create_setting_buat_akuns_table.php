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
    Schema::create('settingsbuatakun', function (Blueprint $table) {
        $table->id();
        $table->boolean('is_open')->default(false); // Status buka/tutup
        $table->string('pesan_tutup')->default('Maaf, pendaftaran akun saat ini sedang ditutup.');
        $table->timestamps();
    });

    // Masukkan data awal (Seeder otomatis)
   // DB::table('settingsbuatakun')->insert([
       // 'is_open' => false,
        //'created_at' => now(),
   // ]);
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_buat_akuns');
    }
};
