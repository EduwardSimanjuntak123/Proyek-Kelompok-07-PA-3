<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelompokMahasiswa extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'kelompok_id',
    ];
    protected $table = 'kelompok_mahasiswa';

    public function kelompok(){
        return $this -> belongsTo(Kelompok::class,'kelompok_id');
    }
    
}
