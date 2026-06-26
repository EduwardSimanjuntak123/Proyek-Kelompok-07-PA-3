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
            Schema::create('tugas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('Judul_Tugas', 500);
                $table->string('Deskripsi_Tugas', 1000);
                $table->foreignId('KPA_id')->constrained('kategori_pa')->onDelete('cascade');
                $table->foreignId('prodi_id')->constrained('prodi')->onDelete('cascade'); // foreign key
                $table->foreignId('TM_id')->constrained('tahun_masuk')->onDelete('cascade');
                $table->dateTime('tanggal_pengumpulan');
                $table->string('file')->nullable();
                $table->enum('status',['selesai','berlangsung']);
                $table->enum('kategori_tugas',['Tugas','Revisi','Artefak']);
                $table->timestamps();
            });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema :: dropIfExists('tugas');
    }
};
