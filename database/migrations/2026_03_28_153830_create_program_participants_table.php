<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
// database/migrations/xxxx_xx_xx_create_program_participants_table.php

public function up(): void
{
    Schema::create('program_participants', function (Blueprint $table) {
        $table->id();
        $table->foreignId('program_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');

        // Nomor Induk Spesifik Program
        $table->string('registration_number')->unique()->nullable();

        // Tracking Pendaftaran
        $table->enum('enrolment_method', ['open_click', 'redeem_code', 'admin_invite', 'manual_add']);
        $table->dateTime('enrolled_at');

        $table->enum('status', ['pending', 'active', 'dropped', 'completed'])->default('active');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_participants');
    }
};
