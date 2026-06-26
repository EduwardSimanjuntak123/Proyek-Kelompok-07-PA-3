<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanSeminarFile extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_seminar_files';
    
    protected $fillable = [
        'pengajuan_seminar_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size'
    ];

    /**
     * Get the pengajuan seminar that owns the file.
     */
    public function pengajuanSeminar()
    {
        return $this->belongsTo(PengajuanSeminar::class, 'pengajuan_seminar_id');
    }
}