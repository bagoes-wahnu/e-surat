<?php

namespace App\Http\Models;

use App\Http\Models\E_Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class UserLog extends E_Model
{
    use SoftDeletes;
    protected $table = 'user_log';
    protected $primaryKey = 'usl_id';
    public $incrementing = true;

    protected $fillable = [
        'usl_usr_id',
        'usl_ip',
        'usl_url',
        'usl_agent',
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