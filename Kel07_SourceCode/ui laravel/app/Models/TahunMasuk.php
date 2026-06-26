<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunMasuk extends Model
{
    use HasFactory;
    protected $fillable = [
         'Tahun_Masuk',
         'Status',
    ];
    protected $table = 'tahun_masuk';
    public function dosenRoles()
    {
        return $this->hasMany(DosenRole::class);
    }
    public function Kelompok()
    {
        return $this->hasMany(Kelompok::class);
    }
    public function jadwal() {
        return $this->hasMany(Jadwal::class, 'TM_id');
    }
    
}
