<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';

    protected $fillable = [
        'kelompok_id',
        'waktu_mulai',
        'waktu_selesai',
        'user_id',
        'ruangan_id',
        'KPA_id',
        'prodi_id',
        'TM_id'
    ];

    protected $casts = [
        'waktu' => 'datetime',
    ];

    public function kelompok(){
        return $this->belongsTo(Kelompok::class);
    }

    public function kategoriPA()
    {
        return $this->belongsTo(kategoriPA::class, 'KPA_id');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function tahunMasuk()
    {
        return $this->belongsTo(TahunMasuk::class, 'TM_id');
    }

    public function ruangan(){
        return $this->belongsTo(Ruangan::class,'ruangan_id');
    }
}
