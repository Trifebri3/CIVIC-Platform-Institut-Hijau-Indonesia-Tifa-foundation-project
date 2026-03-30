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
// database/migrations/xxxx_create_sub_programs_table.php

Schema::create('sub_programs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('program_id')->constrained()->onDelete('cascade');
    $table->foreignId('template_id')->constrained('sub_program_templates');

    $table->string('title'); // Nama spesifik sub-programnya
    $table->string('slug')->unique();

    // DATA DINAMIS DISINI (JSON)
    // Isinya menyesuaikan schema di template.
    // Contoh: {"mentor": "Budi Santoso", "modul": "path/to/file.pdf", "zoom_link": "https://..."}
    $table->json('content_data')->nullable();

    $table->integer('order')->default(0); // Buat susunan tampilan (sortable)
    $table->enum('status', ['draft', 'active'])->default('active');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_programs');
    }
};
