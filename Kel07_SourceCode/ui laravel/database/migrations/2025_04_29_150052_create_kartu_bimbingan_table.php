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
        Schema::create('kartu_bimbingan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_bimbingan_id')
                  ->constrained('request_bimbingan') // Changed from 'request_bimbingan' to match model's relationship
                  ->onDelete('cascade');
            $table->foreignId('pembimbing_id')
                  ->constrained('pembimbing')
                  ->onDelete('cascade');
            $table->foreignId('kelompok_id')
                  ->constrained('kelompok')
                  ->onDelete('cascade');
            $table->date('tanggal_bimbingan');
            $table->text('hasil_bimbingan');
            $table->string('tanda_tangan_pembimbing')->nullable();
            $table->timestamps();
            
            // Optional: Add indexes for better performance
            $table->index('request_bimbingan_id');
            $table->index('pembimbing_id');
            $table->index('kelompok_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_bimbingan');
    }
};