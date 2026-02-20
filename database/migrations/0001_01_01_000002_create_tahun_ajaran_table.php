<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tahun_ajaran', function (Blueprint $table) {
    $table->id();

    $table->year('tahun_mulai');        // 2024
    $table->year('tahun_selesai');      // 2025

    $table->enum('status', ['Aktif', 'Nonaktif'])
          ->default('Nonaktif');

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('tahun_ajaran');
    }
};
