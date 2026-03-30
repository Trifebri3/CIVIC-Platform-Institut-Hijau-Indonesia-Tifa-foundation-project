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
    Schema::create('rab_periods', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Contoh: "RAB Kegiatan Milad 2026"
        $table->text('description')->nullable();

        // TEMPLATE CUSTOM: Simpan format kolom & limitasi dalam JSON
        // Contoh isi: [{"label": "Sewa Alat", "max_budget": 5000000, "required": true}, ...]
        $table->json('form_template');

        // TOTAL PLAFON/VAKUM: Batas total anggaran untuk satu periode ini
        $table->decimal('max_total_budget', 15, 2)->default(0);

        // TIMELINE
        $table->dateTime('start_at');
        $table->dateTime('end_at');
        $table->boolean('is_active')->default(true);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rab_periods');
    }
};
