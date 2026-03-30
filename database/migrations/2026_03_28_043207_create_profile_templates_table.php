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
Schema::create('profile_templates', function (Blueprint $table) {
    $table->id();
    $table->string('field_name'); // Contoh: "alamat_domisili"
    $table->string('field_label'); // Contoh: "Alamat Domisili"
    $table->string('field_type'); // text, number, date, textarea, select
    $table->json('options')->nullable(); // Untuk tipe 'select' (pilihan)
    $table->boolean('is_required')->default(false);
    $table->integer('order')->default(0); // Urutan tampilan
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_templates');
    }
};
