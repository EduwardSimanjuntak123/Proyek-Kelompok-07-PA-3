<?php

use App\Models\kategori_PA;
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
        Schema::create('dosen_roles', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->foreignId('KPA_id')->constrained('kategori_pa')->onDelete('cascade');
            $table->foreignId('prodi_id')->constrained('prodi')->onDelete('cascade'); // foreign key
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade'); // foreign key
            $table->foreignId('TM_id')->constrained('tahun_masuk')->onDelete('cascade'); // foreign key
            $table->string('Tahun_Ajaran');
            $table->enum('status',['Aktif','Tidak-Aktif']);
            // Indexing
            $table->index(['user_id', 'role_id', 'prodi_id', 'KPA_id', 'TM_id','status']);
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
