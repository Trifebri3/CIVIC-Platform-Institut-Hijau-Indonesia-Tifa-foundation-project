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
        // Kita tambahkan kolom approved_at setelah kolom status
        $table->timestamp('approved_at')->nullable()->after('status');
    });
}

public function down(): void
{
    Schema::table('tor_submissions', function (Blueprint $table) {
        $table->dropColumn('approved_at');
    });
}
};
