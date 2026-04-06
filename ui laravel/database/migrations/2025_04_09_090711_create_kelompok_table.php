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
        Schema::create('kelompok', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kelompok', 100);

            $table->foreignId('KPA_id')
                ->constrained('kategori_pa')
                ->onDelete('cascade');

            $table->foreignId('prodi_id')
                ->constrained('prodi')
                ->onDelete('cascade');

            $table->foreignId('TM_id')
                ->constrained('tahun_masuk')
                ->onDelete('cascade');

            // ✅ Tambahkan ini
            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->onDelete('cascade');

            $table->enum('status', ['Aktif', 'Tidak-Aktif']);
            $table->timestamps();

            // Update unique constraint supaya include tahun_ajaran
            $table->unique(
                ['nomor_kelompok', 'KPA_id', 'prodi_id', 'TM_id', 'tahun_ajaran_id'],
                'kelompok_unique'
            );

            $table->index('status');
            $table->index('tahun_ajaran_id');
        });



    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompok');
    }
};
