<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai_kelompok extends Model
{
    use HasFactory;
    protected $table = 'nilai_kelompok';
    protected $fillable = [
        'kelompok_id', 
        'user_id',
        'role_id',
        'A11',
        'A12',
        'A13',
        'A1_total',
        'A21',
        'A22',
        'A23',
        'A2_total',
        'A_total',
    ];
    public function Kelompok()
    {
        return $this->belongsTo(Kelompok::class,'kelompok_id');
    }

}
