<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    protected $table = 'dosen';

    protected $fillable = [
        'pegawai_id',
        'dosen_id',
        'nip',
        'nama',
        'email',
        'prodi_id',
        'prodi',
        'jabatan_akademik',
        'jabatan_akademik_desc',
        'jenjang_pendidikan',
        'nidn',
        'user_id'
    ];
}