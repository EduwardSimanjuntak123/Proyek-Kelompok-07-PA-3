<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mata_kuliah', function (Blueprint $table) {
            $table->id();

            $table->integer('kuliah_id')->unique();
            $table->string('kode_mk')->index();
            $table->string('nama_matkul');

            $table->integer('sks');
            $table->integer('semester');

            $table->unsignedBigInteger('prodi_id');

            $table->year('tahun_ajaran');
            $table->integer('semester_ta');

            $table->timestamps();

            $table->foreign('prodi_id')->references('id')->on('prodi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mata_kuliah');
    }
};