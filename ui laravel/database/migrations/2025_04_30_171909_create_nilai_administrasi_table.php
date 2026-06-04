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
        Schema::create('nilai_administrasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kelompok_id')
                ->constrained('kelompok')
                ->onDelete('cascade');

            $table->foreignId('user_id');

            // Kolom lama (tetap dipertahankan)
            $table->float('Administrasi');
            $table->float('Pameran');
            $table->float('Total');

            // Kolom baru rincian administrasi
            $table->float('C1')->nullable(); // DPP
            $table->float('C2')->nullable(); // TOR
            $table->float('C3')->nullable(); // Bukti Kartu Bimbingan
            $table->float('C4')->nullable(); // Turnitin
            $table->float('C5')->nullable(); // Kode
            $table->float('C_total')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_administrasi');
    }
};