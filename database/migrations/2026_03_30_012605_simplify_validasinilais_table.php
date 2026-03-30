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
    Schema::table('validasinilais', function (Blueprint $table) {
        // Hapus yang tidak perlu karena ini murni MASTER TEMPLATE
        $table->dropForeign(['user_id']);
        $table->dropColumn(['user_id', 'data_nilai', 'status']);

        // Tambahkan deskripsi template
        $table->text('description')->nullable()->after('template_name');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
