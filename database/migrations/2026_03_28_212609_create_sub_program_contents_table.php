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
 Schema::create('sub_program_contents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('sub_program_id')->constrained()->onDelete('cascade');
    $table->string('title');
    $table->string('slug')->unique();
    // Kolom kunci: Menampung array of objects (video, file, text, link)
    $table->json('modules')->nullable();
    $table->integer('order_position')->default(0);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_program_contents');
    }
};
