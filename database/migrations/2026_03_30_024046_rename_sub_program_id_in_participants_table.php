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
    Schema::table('program_khusus_participants', function (Blueprint $table) {
        // Mengubah sub_program_id menjadi program_khusus_id
        $table->renameColumn('sub_program_id', 'program_khusus_id');
    });
}

public function down(): void
{
    Schema::table('program_khusus_participants', function (Blueprint $table) {
        $table->renameColumn('program_khusus_id', 'sub_program_id');
    });
}
};
