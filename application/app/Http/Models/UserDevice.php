<?php

namespace App\Http\Models;

use App\Http\Models\E_Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class UserDevice extends E_Model
{
    use SoftDeletes;
    protected $table = 'user_device';
    protected $primaryKey = 'usd_id';
    public $incrementing = false;

    protected $fillable = [
        'usd_token',
        'usd_sdov_id',
        'usd_manufacture',
        'usd_brand',
        'usd_model',
        'usd_build_number',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_by_token($token=false)
    {
        if($token == false){
            return false;
        }

        $result = DB::table(DB::raw('user_device usd'))
        ->select(DB::raw('usd_id AS id_device, usd_token AS token, usd_manufacture AS manufacture, usd_brand AS brand, usd_model AS model, usd_build_number AS build_number, usd_sdov_id AS id_version, sdov_version AS version, sdov_name AS alias_version, sdo_name AS alias_os'))
        ->leftJoin(DB::raw('sys_device_os_version sdov'), 'sdov_id', '=', 'usd_sdov_id')
        ->leftJoin(DB::raw('sys_device_os sdo'), 'sdo_id', '=', 'sdov_sdo_id')
        ->whereNull(DB::raw('usd.deleted_at'));

        $result = $result->where('usd_token', $token)->first();

        return $result;
    }
}