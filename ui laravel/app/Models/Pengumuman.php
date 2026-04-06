<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use HasFactory;

    // Menentukan nama tabel jika berbeda dari nama model
    protected $table = 'pengumuman';
    public function dosenRoles()
    {
        return $this->hasMany(DosenRole::class,'user_id', 'user_id');
    }
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
    public function tahunMasuk()
    {
        return $this->belongsTo(tahunMasuk::class, 'TM_id');
    }
    public function kategoriPA()
    {
        return $this->belongsTo(kategoriPA::class, 'KPA_id');
    }
    // Menentukan kolom yang dapat diisi mass-assignment
    protected $fillable = [
        'judul', 
        'deskripsi', 
        'tanggal_penulisan', 
        'file', 
        'status',
        'user_id',
        'KPA_id',
        'prodi_id',
        'TM_id',

    ];

}
   
