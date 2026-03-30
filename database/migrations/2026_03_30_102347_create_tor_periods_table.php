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
// database/migrations/xxxx_create_tor_periods_table.php

Schema::create('tor_periods', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // Contoh: Hibah Riset Ganjil 2026
    $table->text('description')->nullable();
    $table->datetime('start_at'); // Kapan dibuka
    $table->datetime('end_at');   // Kapan ditutup

    // ATURAN MAIN
    $table->integer('max_submissions_per_user')->default(2); // Batas 2 TOR tadi

    // TEMPLATE FORM (DIPAKAI UNTUK DYNAMIC FORM BUILDER)
    // Isinya: [{label: 'Judul', type: 'text'}, {label: 'Latar Belakang', type: 'richtext'}]
    $table->json('form_template');

    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tor_periods');
    }
};
