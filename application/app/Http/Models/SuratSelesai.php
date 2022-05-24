<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SuratSelesai extends Model
{
    use SoftDeletes;
    protected $table = 'surat_selesai';
    protected $primaryKey = 'srs_id';
    public $incrementing = true;


    protected $fillable = [
        'srs_id',
        'srs_srt_id',
        'srs_path_file',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

}