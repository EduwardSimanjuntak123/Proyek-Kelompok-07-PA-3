<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Nilai_Bimbingan extends Model
{
    use HasFactory;
    protected $table = 'nilai_bimbingan';
    protected $fillable = [
        'penilai_id', 
        'role_id',
        'user_id',
        'A1',
        'A2',
        'A3',
        'A4',
        'A5',
        'Total',
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
