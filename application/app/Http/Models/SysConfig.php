<?php

namespace App\Http\Models;

use App\Http\Models\E_Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SysConfig extends E_Model
{
    use SoftDeletes;
    protected $table = 'sys_config';
    protected $primaryKey = 'config_name';
    public $incrementing = false;

    protected $fillable = [
        'config_value',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($config_name=false)
    {
        $result = DB::table(DB::raw('sys_config'))->select(DB::raw('config_name AS config, config_value AS value'))->whereNull('deleted_at');

        if($config_name == true){
            $result = $result->where('config_name', $config_name)->first();
        }else{
            $result = $result->orderBy('config_name', 'asc')->get();
        }

        return $result;
    }
}