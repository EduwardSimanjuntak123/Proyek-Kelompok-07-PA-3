<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';

    protected $fillable = [
        'kuliah_id',
        'kode_mk',
        'nama_matkul',
        'sks',
        'semester',
        'prodi_id',
        'tahun_ajaran',
        'semester_ta'
    ];
}