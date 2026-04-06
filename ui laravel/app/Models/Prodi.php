<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Prodi extends Model
{
    use HasFactory;
    protected $fillable = [
        'nama_prodi',
        'maks_project',
    ];
    protected $table = 'prodi';
    public function dosenRoles()
    {
        return $this->hasMany(DosenRole::class);
    }
    public function Kelompok()
    {
        return $this->hasMany(Kelompok::class);
    }

    public function pengumuman()
    {
        return $this->hasMany(Pengumuman::class, 'prodi_id');
    }
    public function jadwal() {
        return $this->hasMany(Jadwal::class);
    }
    
}

