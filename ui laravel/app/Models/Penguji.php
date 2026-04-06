<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penguji extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'kelompok_id',
    ];
    protected $table='penguji';
    public function dosenRoles()
    {
        return $this->hasOne(DosenRole::class, 'user_id', 'user_id');
    }    
    public function Kelompok()
    {
        return $this->belongsTo(Kelompok::class,'kelompok_id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
