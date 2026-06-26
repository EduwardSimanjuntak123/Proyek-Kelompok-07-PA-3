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
            Schema::create('nilai_kelompok', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kelompok_id')->constrained('kelompok')->onDelete('cascade');
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
                $table->float('A11');
                $table->float('A12');
                $table->float('A13');
                $table->float('A1_total')->nullable();
                $table->float('A21');
                $table->float('A22');
                $table->float('A23');
                $table->float('A2_total')->nullable();
                $table->float('A_total')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_kelompok');
    }
};
