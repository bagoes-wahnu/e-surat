<?php

namespace App\Http\Models;

use App\Http\Models\E_Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class UserToken extends E_Model
{
    use SoftDeletes;
    protected $table = 'user_token';
    protected $primaryKey = 'ust_id';
    public $incrementing = false;

    protected $fillable = [
        'ust_usr_id',
        'ust_usd_id',
        'ust_token',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($id_token=false, $id_user=false, $showDevice=false)
    {
        $result = DB::table(DB::raw('user_token ust'))
        ->select(DB::raw('ust_id AS id_token, ust_usr_id AS id_user, ust_usd_id AS id_device, ust_token AS token'))
        ->whereNull('ust.deleted_at');

        if($showDevice == true){
            $result = $result->select(DB::raw('ust_id AS id_token, ust_usr_id AS id_user, ust_usd_id AS id_device, ust_token AS token, usd_id AS id_device, usd_token AS device_token, usd_manufacture AS manufacture, usd_brand AS brand, usd_model AS model, usd_build_number AS build_number, usd_sdov_id AS id_version, sdov_sdk AS sdk, sdov_version AS version, sdov_name AS alias_version, sdo_name AS alias_os'))
            ->join(DB::raw('user_device usd'), 'usd_id', '=', 'ust_usd_id')
            ->leftJoin(DB::raw('sys_device_os_version sdov'), 'sdov_id', '=', 'usd_sdov_id')
            ->leftJoin(DB::raw('sys_device_os sdo'), 'sdo_id', '=', 'sdov_sdo_id');
        }

        if($id_user == true){
            $result = $result->where('ust_usr_id', $id_user);
        }

        if($id_token == true){
            $result = $result->where('ust_id', $id_token)->first();
        }else{
            $result = $result->orderBy('ust_id', 'asc')->get();
        }

        return $result;
    }

    public static function get_by_device($id_device=false)
    {
        if($id_device == false){
            return false;
        }

        $result = DB::table(DB::raw('user_token ust'))
        ->select(DB::raw('ust_id AS id_token, ust_usr_id AS id_user, ust_usd_id AS id_device, ust_token AS token'))
        ->whereNull('ust.deleted_at');

        $result = $result->where('ust_usd_id', $id_device)->first();

        return $result;
    }
}