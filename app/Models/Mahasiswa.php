<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';
    protected $fillable = [
        'dim_id',
        'user_id',
        'user_name',
        'nim',
        'nama',
        'email',
        'prodi_id',
        'prodi_name',
        'fakultas',
        'angkatan',
        'status',
        'asrama'
    ];

    public function kelompok()
    {
        return $this->belongsToMany(
            Kelompok::class,
            'kelompok_mahasiswa',
            'user_id',
            'kelompok_id',
            'user_id',
            'id'
        );
    }
}