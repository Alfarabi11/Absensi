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
        Schema::create('pengajuan_izin', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_pengajuan'); // Cuti, Izin, Sakit
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('berkas_lampiran')->nullable();
            $table->string('status_persetujuan')->default('Pending'); // Pending, Approved, Rejected
            $table->timestamp('tanggal_persetujuan')->nullable();
            
            // Foreign Key
            $table->foreignId('users_id')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izin');
    }
};
