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
    Schema::create('rab_submissions', function (Blueprint $table) {
        $table->id();

        // RELASI
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('tor_submission_id')->constrained()->onDelete('cascade'); // Menginduk ke TOR
        $table->foreignId('rab_period_id')->constrained()->onDelete('cascade');

        // DATA RAB
        // Simpan inputan user dalam JSON
        $table->json('items');

        // TOTAL YANG DIAJUKAN
        $table->decimal('total_requested', 15, 2)->default(0);
        $table->decimal('total_approved', 15, 2)->default(0); // Terisi otomatis pas di-ACC

        // TRACKING & FEEDBACK
        // Status: 'pending', 'revision', 'partially_approved', 'approved', 'rejected'
        $table->string('status')->default('pending');

        // REVIEW ADMIN: Simpan data item mana yang di-ceklis (ACC) dan catatan revisi per item
        // Contoh isi: {"item_1": {"status": "acc"}, "item_2": {"status": "rejected", "note": "Terlalu mahal"}}
        $table->json('admin_feedback')->nullable();

        $table->text('general_note')->nullable(); // Catatan umum dari admin

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rab_submissions');
    }
};
