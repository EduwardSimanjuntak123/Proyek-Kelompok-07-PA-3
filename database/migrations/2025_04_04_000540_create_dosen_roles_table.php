<?php

use App\Models\kategori_PA;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dosen_roles', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->unsignedBigInteger('user_id');

            $table->foreignId('KPA_id')
                ->constrained('kategori_pa')
                ->onDelete('cascade');

            $table->foreignId('prodi_id')
                ->constrained('prodi')
                ->onDelete('cascade');

            $table->foreignId('role_id')
                ->constrained('roles')
                ->onDelete('cascade');

            $table->foreignId('TM_id')
                ->constrained('tahun_masuk')
                ->onDelete('cascade');

            // ✅ Tambahkan ini
            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->onDelete('cascade');

            $table->enum('status', ['Aktif', 'Tidak-Aktif']);

            // Indexing
            $table->index([
                'user_id',
                'role_id',
                'prodi_id',
                'KPA_id',
                'TM_id',
                'tahun_ajaran_id',
                'status'
            ], 'dr_multi_index');

            $table->unique([
                'user_id',
                'role_id',
                'prodi_id',
                'KPA_id',
                'TM_id',
                'tahun_ajaran_id'
            ], 'dr_unique');
        });

    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen_roles');
    }
};
