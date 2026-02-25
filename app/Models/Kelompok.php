<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Nilai_kelompok;
class Kelompok extends Model
{
    use HasFactory;

    protected $table = 'kelompok';

    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_id');
    }
    public function TahunMasuk()
    {
        return $this->belongsTo(TahunMasuk::class, 'TM_id');
    }
    public function kategoriPA()
    {
        return $this->belongsTo(kategoriPA::class, 'KPA_id');
    }
    public function jadwal()
    {
        return $this->hasOne(Jadwal::class);
    }
    public function pembimbing()
    {
        return $this->hasMany(pembimbing::class, 'kelompok_id');
    }
    public function nilais()
    {
        return $this->hasMany(Nilai_kelompok::class);
    }   
    public function nilaiindividu()
    {
        return $this->hasMany(Nilai_Individu::class);
    }  
    public function penguji()
    {
        return $this->hasMany(Penguji::class, 'kelompok_id');
    }
    public function KelompokMahasiswa() {
        return $this->hasMany(KelompokMahasiswa::class);
    }
public function tahunAjaran()
{
    return $this->belongsTo(tahunAjaran::class);
}

    public function pengajuanSeminar()
    {
        return $this->hasMany(PengajuanSeminar::class);
    }
    protected $fillable = [
    'nomor_kelompok',
    'KPA_id',
    'prodi_id',
    'TM_id',
    'tahun_ajaran_id', // 🔥 TAMBAHKAN INI
    'status',
];
}
