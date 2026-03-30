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
// database/migrations/xxxx_create_sub_program_templates_table.php

Schema::create('sub_program_templates', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // Contoh: "Kelas", "Project", "Acara"
    $table->string('slug')->unique();
    $table->text('description')->nullable();

    // Disini kita simpan struktur field-nya (JSON)
    // Isinya nanti: [{"name": "mentor", "type": "text", "label": "Nama Mentor"}, {"name": "modul", "type": "file", "label": "Upload Modul"}]
    $table->json('fields_schema')->nullable();

    $table->string('icon')->nullable(); // Opsional buat hiasan UI
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_program_templates');
    }
};
