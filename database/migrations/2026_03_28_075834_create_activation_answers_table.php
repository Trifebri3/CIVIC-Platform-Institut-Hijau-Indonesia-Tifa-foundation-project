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
    Schema::create('activation_answers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('activation_question_id')->constrained()->onDelete('cascade');

        /**
         * Kolom 'content' menyimpan jawaban user sesuai tipe:
         * {"nama_lengkap": "Budi", "divisi": "IT", "ktp": "path/to/file.pdf"}
         */
        $table->json('content');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activation_answers');
    }
};
