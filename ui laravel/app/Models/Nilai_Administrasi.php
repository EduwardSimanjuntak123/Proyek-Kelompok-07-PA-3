<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Nilai_Administrasi extends Model
{
    use HasFactory;
    protected $table = 'nilai_administrasi';
    protected $fillable = [
        'kelompok_id', 
        'user_id',
        'Administrasi',
        'Pameran',
        'Total',
    ];
    public function Kelompok()
    {
        return $this->belongsTo(Kelompok::class,'kelompok_id');
    }

   
}
