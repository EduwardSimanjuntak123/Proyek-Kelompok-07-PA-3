<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class kategoriPA extends Model
{
    protected $table = 'kategori_pa';

    protected $fillable = ['kategori_pa'];
    
    // Optional: relasi ke dosen_roles
    public function dosenRoles()
    {
        return $this->hasMany(DosenRole::class);
    }
    public function Kelompok()
    {
        return $this->hasMany(Kelompok::class);
    }
    public function jadwal() {
        return $this->hasMany(Jadwal::class, 'KPA_id');
    }
    
}
