<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class tahunAjaran extends Model
{
    use HasFactory;
    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'tahun_mulai',
        'tahun_selesai',
        'status',
    ];

    protected $casts = [
        'tahun_mulai'     => 'integer',
        'tahun_selesai'   => 'integer',
    ];
    
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
