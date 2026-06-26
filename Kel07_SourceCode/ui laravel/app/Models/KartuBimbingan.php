<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuBimbingan extends Model
{
    use HasFactory;
    protected $table = 'kartu_bimbingan';

    // Kolom yang dapat diisi
    protected $fillable = [
        'request_bimbingan_id',
        'pembimbing_id',
        'kelompok_id',
        'tanggal_bimbingan',
        'hasil_bimbingan',
        'tanda_tangan_pembimbing',
    ];

    /**
     * Relasi dengan tabel 'request_bimbingan'
     * Menunjukkan hubungan antara kartu bimbingan dan permintaan bimbingan
     */
    public function bimbingan()
    {
        return $this->belongsTo(Bimbingan::class, 'request_bimbingan_id');
    }

    /**
     * Relasi dengan tabel 'pembimbing'
     * Menunjukkan hubungan antara kartu bimbingan dan pembimbing
     */
    public function pembimbing()
    {
        return $this->belongsTo(pembimbing::class, 'pembimbing_id');
    }

    /**
     * Relasi dengan tabel 'kelompok'
     * Menunjukkan hubungan antara kartu bimbingan dan kelompok
     */
    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_id');
    }
}