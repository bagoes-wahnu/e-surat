<?php

namespace App\Http\Models;

use App\Http\Models\E_Model;

class SuratHalaman extends E_Model
{
    protected $table = 'surat_halaman';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'srt_id',
        'halaman',
        'orientation',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by'];
}