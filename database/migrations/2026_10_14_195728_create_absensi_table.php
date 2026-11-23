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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->timestamp('waktu_check_in')->nullable();
            $table->timestamp('waktu_check_out')->nullable();
            $table->string('koordinat_in')->nullable();
            $table->string('koordinat_out')->nullable();
            $table->string('status_kehadiran')->nullable(); // Hadir, Terlambat, Alpha
            
            // Foreign Keys
            $table->foreignId('users_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('qr_code_id')->nullable()->constrained('qr_code')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
