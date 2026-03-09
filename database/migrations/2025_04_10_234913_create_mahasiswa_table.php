<?php

// database/migrations/xxxx_create_mahasiswa_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->integer('dim_id')->unique();
            $table->integer('user_id')->unique();
            $table->string('user_name');
            $table->string('nim')->unique();
            $table->string('nama');
            $table->string('email');
            $table->integer('prodi_id');
            $table->string('prodi_name');
            $table->string('fakultas');
            $table->integer('angkatan');
            $table->string('status');
            $table->string('asrama')->nullable();
            $table->timestamps();

            // Index untuk performa AI Agent
            $table->index('angkatan');
            $table->index('prodi_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};