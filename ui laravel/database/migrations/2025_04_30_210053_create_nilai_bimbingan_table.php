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
        Schema::create('nilai_bimbingan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penilai_id');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->float('A1');
            $table->float('A2');
            $table->float('A3');
            $table->float('A4');
            $table->float('A5');
            $table->float('Total')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_bimbingan');
    }
};
