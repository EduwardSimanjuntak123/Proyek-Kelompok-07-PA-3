<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai_Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'nilai_mahasiswa';

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
    public function KelompokMahasiswa() {
        return $this->hasMany(KelompokMahasiswa::class);
    }
}
