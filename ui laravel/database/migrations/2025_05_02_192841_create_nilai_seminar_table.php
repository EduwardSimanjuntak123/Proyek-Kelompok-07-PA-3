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
        Schema::create('nilai_seminar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreignId('kelompok_id')->constrained('kelompok')->onDelete('cascade');
            $table->Float ('nilai_kelompok_role_2');
            $table->Float ('nilai_individu_role_2');
            $table->float('total_role_2');
            $table->Float ('nilai_kelompok_role_3');
            $table->Float ('nilai_individu_role_3');
            $table->float('total_role_3');
            $table->Float ('nilai_kelompok_role_4');
            $table->Float ('nilai_individu_role_4');
            $table->float('total_role_4');
            $table->Float ('nilai_kelompok_role_5');
            $table->Float ('nilai_individu_role_5');
            $table->float('total_role_5');
            $table->float('nilai_seminar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_seminar');
    }
};
