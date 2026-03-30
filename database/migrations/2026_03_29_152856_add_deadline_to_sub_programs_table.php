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
    Schema::table('sub_programs', function (Blueprint $table) {
        // Kita pakai dateTime agar bisa simpan Tanggal + Jam
        $table->dateTime('deadline')->nullable()->after('status');
    });
}

public function down(): void
{
    Schema::table('sub_programs', function (Blueprint $table) {
        $table->dropColumn('deadline');
    });
}
};
