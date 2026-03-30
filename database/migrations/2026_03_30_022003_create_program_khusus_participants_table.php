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
Schema::create('program_khusus_participants', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('sub_program_id')->nullable()->constrained()->onDelete('cascade');

    // Status Akses & Role Khusus
    $table->string('access_role')->default('member'); // misal: 'member', 'mentor', 'lead'
    $table->boolean('is_active')->default(true);

    // Metadata Undangan
    $table->timestamp('invited_at')->nullable();
    $table->timestamp('joined_at')->nullable();
    $table->string('invitation_code')->unique()->nullable(); // Jika butuh join via kode

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_khusus_participants');
    }
};
