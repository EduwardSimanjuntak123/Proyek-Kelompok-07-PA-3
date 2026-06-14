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
        Schema::create('judul_proyek_akhir', function (Blueprint $table) {
    $table->id();

    $table->foreignId('kelompok_id')
        ->unique()
        ->constrained('kelompok')
        ->onDelete('cascade');

    $table->string('judul');
    $table->text('deskripsi')->nullable();

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('judul_proyek_akhir');
    }
};
