<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiMatkulMahasiswa extends Model
{
    protected $table = 'nilai_matkul_mahasiswa';

    protected $fillable = [
        'nim',
        'kode_mk',
        'tahun_ajaran',
        'semester_ta',
        'nilai_angka',
        'nilai_huruf',
        'sks'
    ];
}
