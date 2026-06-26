<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table ='ruangan';
    protected $fillable = ['ruangan'];
    public function jadwal() {
        return $this->hasMany(Jadwal::class, 'ruangan_id');
    }
}


