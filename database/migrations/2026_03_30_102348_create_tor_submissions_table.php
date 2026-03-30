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
// database/migrations/xxxx_create_tor_submissions_table.php

Schema::create('tor_submissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('tor_period_id')->constrained()->onDelete('cascade');

    // IDENTITAS PENGAJUAN
    $table->string('submission_code')->unique(); // Contoh: TOR-2026-001
    $table->string('title');

    // DATA FORM (HASIL JAWABAN USER)
    // Isinya JSON: { 'Latar Belakang': 'isi text panjang...', 'Foto': ['path/img.jpg'], 'Tabel': [...] }
    $table->json('submission_data');

    // TRACKING STATUS & FEEDBACK
    // status: draft (belum kirim), pending (menunggu), revision (revisi), approved (acc), rejected (tolak)
    $table->string('status')->default('draft');
    $table->text('admin_feedback')->nullable(); // Catatan kalau disuruh revisi

    $table->timestamp('submitted_at')->nullable(); // Kapan user klik "Submit"
    $table->timestamp('reviewed_at')->nullable();  // Kapan Admin respon (ACC/Revisi)
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tor_submissions');
    }
};
