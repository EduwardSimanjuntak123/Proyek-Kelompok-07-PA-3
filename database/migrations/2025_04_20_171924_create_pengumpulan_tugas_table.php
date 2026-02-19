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
        Schema::create('pengumpulan_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')->constrained('kelompok')->onDelete('cascade');
            $table->foreignId('tugas_id')->constrained('tugas')->onDelete('cascade');
            $table->dateTime('waktu_submit')->nullable();
            $table->string('file_path')->nullable(); 
            $table->enum('status', ['Submitted', 'Late', 'Belum'])->default('Submitted');
            $table->text('feedback')->nullable();
            $table->text('feedback_pembimbing')->nullable();
            $table->text('feedback_penguji')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumpulan_tugas');
    }
};
