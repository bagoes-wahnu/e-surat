<?php

namespace App\Http\Models;

use App\Http\Models\E_Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SysDeviceOs extends E_Model
{
    use SoftDeletes;
    protected $table = 'sys_device_os';
    protected $primaryKey = 'sdo_id';
    public $incrementing = true;

    protected $fillable = [
        'sdo_name',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($id_os=false, $md5=true)
    {
        $encryptId = helpEncryptQuery('sdo_id', 'os', 'id_os', $md5);

        $result = DB::table(DB::raw('sys_device_os'))
        ->select(DB::raw($encryptId.', sdo_name AS nama_os'))->whereNull('deleted_at');

        if($id_os == true){
            $result = $result->where(DB::raw("MD5(CONCAT(sdo_id, '".encText('os')."'))"), $id_os)->first();
        }else{
            $result = $result->orderBy('sdo_id', 'asc')->get();
        }

        return $result;
    }
}