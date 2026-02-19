<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bimbingan extends Model
{
    use HasFactory;

    protected $table = 'request_bimbingan'; // Sesuai nama tabel di database
    protected $fillable = [
        'kelompok_id',
        'user_id',
        'keperluan',
        'rencana_mulai',
        'rencana_selesai',
        'ruangan_id',
        'status',
        'hasil_bimbingan', // Tambahkan field ini
    ];
    
    // Relasi ke Kelompok
    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_id');
    }
    
    // Relasi ke KartuBimbingan
    public function kartuBimbingan()
    {
        return $this->hasOne(KartuBimbingan::class, 'request_bimbingan_id');
    }
    public function ruangan(){
        return $this->belongsTo(Ruangan::class,'ruangan_id');
    }
    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }
    public function kategoriPA()
    {
        return $this->belongsTo(kategoriPA::class);
    }
}