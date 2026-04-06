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
        Schema::create('nilai_individu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penilai_id');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->float('B11');
            $table->float('B12');
            $table->float('B13');
            $table->float('B14');
            $table->float('B15');
            $table->float('B1_total')->nullable();
            $table->float('B21');
            $table->float('B22');
            $table->float('B23');
            $table->float('B24');
            $table->float('B25');
            $table->float('B2_total')->nullable();
            $table->float('B31');
            $table->float('B3_total')->nullable();
            $table->float('B_total')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_individu');
    }
};
