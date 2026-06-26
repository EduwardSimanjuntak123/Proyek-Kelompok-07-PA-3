<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JudulProyekAkhir extends Model
{
    protected $table = 'judul_proyek_akhir';

    protected $fillable = [
        'kelompok_id',
        'judul',
        'deskripsi',
        'status',   
    ];

    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class);
    }
}
