<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('program_reports', function (Blueprint $table) {
        // 1. Hapus Foreign Key-nya dulu
        $table->dropForeign(['program_profile_id']);
        // 2. Hapus Kolomnya
        $table->dropColumn('program_profile_id');
    });
}

public function down()
{
    Schema::table('program_reports', function (Blueprint $table) {
        $table->unsignedBigInteger('program_profile_id')->nullable();
    });
}
};
