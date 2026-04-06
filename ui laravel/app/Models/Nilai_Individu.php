<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai_Individu extends Model
{
    use HasFactory;
    protected $table = 'nilai_individu';
    protected $fillable = [
        'penilai_id', 
        'role_id',
        'user_id',
        'B11',
        'B12',
        'B13',
        'B14',
        'B15',
        'B1_total',
        'B21',
        'B22',
        'B23',
        'B24',
        'B25',
        'B2_total',
        'B31',
        'B3_total',
        'B_total',
    ];
    public function Kelompok()
    {
        return $this->belongsTo(Kelompok::class,'kelompok_id');
    }
    public function dosenRoles()
    {
        return $this->hasOne(DosenRole::class, 'user_id', 'user_id');
    }  
    public function roles()
    {
        return $this->hasOne(Role::class);
    }  
    
}
