<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_profiles', function (Blueprint $table) {
            // 1. Hapus Foreign Key dan Kolom rab_period_id
            $table->dropForeign(['rab_period_id']);
            $table->dropColumn('rab_period_id');

            // 2. Tambahkan constraint Unique pada user_id
            // Agar 1 user cuma bisa punya 1 profil selamanya
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('program_profiles', function (Blueprint $table) {
            // Kembalikan jika rollback
            $table->dropUnique(['user_id']);
            $table->foreignId('rab_period_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
};
