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
    Schema::table('announcements', function (Blueprint $table) {
        // Tambahkan kolom 'type' untuk kategori warna (info, warning, dll)
        if (!Schema::hasColumn('announcements', 'type')) {
            $table->string('type')->default('info')->after('link_url');
        }

        // Pastikan target_type juga ada kalau tadi terlewat
        if (!Schema::hasColumn('announcements', 'target_type')) {
            $table->string('target_type')->default('global')->after('type');
        }
    });
}

public function down(): void
{
    Schema::table('announcements', function (Blueprint $table) {
        $table->dropColumn(['type', 'target_type']);
    });
}
};
