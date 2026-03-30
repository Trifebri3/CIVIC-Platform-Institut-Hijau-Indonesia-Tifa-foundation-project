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
    Schema::table('tor_submissions', function (Blueprint $table) {
        // Gunakan json() jika database Bos mendukung (MySQL 5.7+),
        // kalau tidak yakin, gunakan longText()
        $table->json('answers')->nullable()->after('tor_period_id');

        // Tambahkan juga submission_code jika tadi di error log terlihat kolom itu dipanggil
        if (!Schema::hasColumn('tor_submissions', 'submission_code')) {
            $table->string('submission_code')->unique()->after('status');
        }
    });
}

public function down(): void
{
    Schema::table('tor_submissions', function (Blueprint $table) {
        $table->dropColumn(['answers', 'submission_code']);
    });
}
};
