<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $table = 'tugas';

    protected $fillable = [
        'user_id',
        'Judul_Tugas',
        'Deskripsi_Tugas',
        'KPA_id',
        'prodi_id',
        'TM_id',
        'tanggal_pengumpulan',
        'file',
        'kategori_tugas',
        'status',
    ];

    protected $casts = [
        'tanggal_pengumpulan' => 'datetime',
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
    public function tahunMasuk()
    {
    return $this->belongsTo(TahunMasuk::class, 'TM_id');
    }
    public function kategoriPA()
    {
        return $this->belongsTo(kategoriPA::class, 'KPA_id');
    }
    public function pengumpulan(){
        return $this->hasMany(pengumpulan_tugas::class);
    }
     public function dosenRoles()
    {
        return $this->hasOne(DosenRole::class, 'user_id', 'user_id');
    }  
}
