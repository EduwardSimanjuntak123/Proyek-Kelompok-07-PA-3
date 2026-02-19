<?php

use App\Models\Kelompok;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('request_bimbingan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_id')
                  ->constrained('kelompok')
                  ->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('keperluan');
            $table->dateTime('rencana_mulai');
            $table->dateTime('rencana_selesai');
            // $table->string('lokasi');
            $table->foreignId('ruangan_id')
            ->constrained('ruangan')
            ->onDelete('cascade');
            
            $table->enum('status', ['menunggu', 'selesai', 'disetujui', 'ditolak'])
                  ->default('menunggu');
            $table->text('hasil_bimbingan')->nullable(); // Added to match model
            $table->timestamps();    

            // Add index for better performance
            $table->index('kelompok_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_bimbingan');
    }
};