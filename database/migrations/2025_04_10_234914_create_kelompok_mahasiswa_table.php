<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kelompok_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreignId('kelompok_id')->constrained('kelompok')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['kelompok_id', 'user_id']);
            $table->foreign('user_id')
                ->references('user_id')
                ->on('mahasiswa')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompok_mahasiswa');
    }
};
