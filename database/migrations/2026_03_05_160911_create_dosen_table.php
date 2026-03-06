<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->id();
            $table->integer('pegawai_id')->nullable();
            $table->integer('dosen_id')->nullable();
            $table->string('nip')->nullable();
            $table->string('nama');
            $table->text('email')->nullable();
            $table->integer('prodi_id')->nullable();
            $table->string('prodi')->nullable();
            $table->string('jabatan_akademik')->nullable();
            $table->string('jabatan_akademik_desc')->nullable();
            $table->string('jenjang_pendidikan')->nullable();
            $table->string('nidn')->nullable();
            $table->integer('user_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen');
    }
};