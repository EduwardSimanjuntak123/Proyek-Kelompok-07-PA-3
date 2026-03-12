<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nilai_matkul_mahasiswa', function (Blueprint $table) {
            $table->id();

            // relasi ke mahasiswa
            $table->unsignedInteger('mahasiswa_id');

            // relasi ke mata kuliah pakai kode_mk
            $table->string('kode_mk');

            // nilai
            $table->decimal('nilai_angka', 5, 2)->nullable();
            $table->string('nilai_huruf', 2)->nullable();
            $table->decimal('bobot_nilai', 3, 2)->nullable();

            // informasi akademik
            $table->integer('semester');
            $table->year('tahun_ajaran');

            $table->timestamps();

            $table->foreign('kode_mk')
                ->references('kode_mk')
                ->on('mata_kuliah')
                ->onDelete('cascade');
            $table->foreign('mahasiswa_id')
                ->references('user_id') // Pastikan ini 'user_id'
                ->on('mahasiswa')
                ->onDelete('cascade');
            // index untuk performa
            $table->index('mahasiswa_id');
            $table->index('kode_mk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_mahasiswa');
    }
};