<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/xxxx_xx_xx_create_programs_table.php

public function up(): void
{
    Schema::create('programs', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->string('banner')->nullable();

        // Timeline & Kuota
        $table->dateTime('registration_start');
        $table->dateTime('registration_end');
        $table->integer('quota')->default(0); // 0 bisa berarti unlimit

        // Fitur Akses
        $table->boolean('is_open')->default(true); // Terbuka tinggal klik
        $table->string('redeem_code')->nullable(); // Kode khusus untuk masuk

        // Pengaturan Nomor Induk (NIP/NIM/Nomor Peserta)
        $table->string('id_number_format')->default('REG-{YEAR}-{ID}');
        $table->boolean('use_global_id')->default(false); // Jika true, pakai nomor induk lama/utama

        $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
