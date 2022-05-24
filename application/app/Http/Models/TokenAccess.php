<?php

namespace App\Http\Models;

use App\Http\Models\E_Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TokenAccess extends E_Model
{
    use SoftDeletes;
    protected $table = 'token_access';
    protected $primaryKey = 'tac_id';
    public $incrementing = false;

    protected $fillable = [
        'tac_usr_id',
        'tac_device_id',
        'tac_token',
        'tac_operation_system',
        'tac_device_type',
        'tac_imei',
        'tac_build_number',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($id_token=false)
    {
        $result = DB::table(DB::raw('token_access'))->select(DB::raw('tac_id AS id_token, tac_usr_id AS id_user, tac_device_id AS id_device, tac_token AS token, tac_operation_system AS operation_system, tac_device_type AS device_type, tac_imei AS imei, tac_build_number AS build_number'))->whereNull('deleted_at');

        if($id_token == true){
            $result = $result->where('tac_id', $id_token)->first();
        }else{
            $result = $result->orderBy('tac_id', 'asc')->get();
        }

        return $result;
    }
}