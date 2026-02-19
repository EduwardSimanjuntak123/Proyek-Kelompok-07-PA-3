<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DosenRole extends Model
{
public function prodi()
{
    return $this->belongsTo(Prodi::class, 'prodi_id');
}

public function role()
{
    return $this->belongsTo(Role::class, 'role_id');
}
public function tahunMasuk()
{
    return $this->belongsTo(TahunMasuk::class, 'TM_id');
}
public function kategoriPA()
{
    return $this->belongsTo(kategoriPA::class, 'KPA_id');
}
public function pembimbing(){
    return $this->belongsTo(pembimbing::class, 'pembimbing_id');
}

    use HasFactory;
    protected $fillable = [
        'user_id',
        'role_id',
        'prodi_id',
        'KPA_id',
        'TM_id',
        'Tahun_Ajaran',
        'status',
    ];
    
}
