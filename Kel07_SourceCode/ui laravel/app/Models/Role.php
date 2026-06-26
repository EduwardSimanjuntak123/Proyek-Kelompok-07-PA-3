<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'role_name'
    ];
    protected $table = 'roles';
    public function dosenRoles()
    {
        return $this->hasMany(DosenRole::class);
    }
    
}
