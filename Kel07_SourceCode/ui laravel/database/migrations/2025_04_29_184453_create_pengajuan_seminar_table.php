<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengajuanSeminarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengajuan_seminar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kelompok_id');
            $table->unsignedBigInteger('pembimbing_id');
            // Remove the file column since we're using a separate table for files
            // $table->string('file'); 
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('kelompok_id')->references('id')->on('kelompok')->onDelete('cascade');
            $table->foreign('pembimbing_id')->references('id')->on('pembimbing')->onDelete('cascade');
            
            // Index untuk mempercepat query
            $table->index('kelompok_id');
            $table->index('pembimbing_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pengajuan_seminar');
    }
}