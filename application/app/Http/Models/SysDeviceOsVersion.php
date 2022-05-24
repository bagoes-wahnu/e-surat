<?php

namespace App\Http\Models;

use App\Http\Models\E_Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SysDeviceOsVersion extends E_Model
{
    use SoftDeletes;
    protected $table = 'sys_device_os_version';
    protected $primaryKey = 'sdov_id';
    public $incrementing = true;

    protected $fillable = [
        'sdov_sdo_id',
        'sdov_sdk',
        'sdov_version',
        'sdov_name',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($id_version=false, $id_os=false, $md5=true)
    {
        $encryptId = helpEncryptQuery('sdov_id', 'os_version', 'id_version', $md5);
        $encryptIdOs = helpEncryptQuery('sdov_sdo_id', 'os', 'id_os', $md5);

        $result = DB::table(DB::raw('sys_device_os_version sdov'))
        ->select(DB::raw($encryptId.', '.$encryptIdOs.', sdov_sdk AS sdk, sdov_version AS version, sdov_name AS alias_version, sdo_name AS alias_os'))
        ->leftJoin(DB::raw('sys_device_os sdo'), 'sdo_id', '=', 'sdov_sdo_id')
        ->whereNull(DB::raw('sdov.deleted_at'));

        if($id_os == true){
            $result = $result->where(DB::raw("MD5(CONCAT(sdov_sdo_id, '".encText('os')."'))"), $id_os);
        }

        if($id_version == true){
            $result = $result->where(DB::raw("MD5(CONCAT(sdov_id, '".encText('os_version')."'))"), $id_version)->first();
        }else{
            $result = $result->orderBy('sdov_id', 'asc')->get();
        }

        return $result;
    }
}