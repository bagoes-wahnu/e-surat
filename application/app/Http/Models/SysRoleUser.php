<?php

namespace App\Http\Models;

use App\Http\Models\E_Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SysRoleUser extends E_Model
{
    use SoftDeletes;
    protected $table = 'sys_role_user';
    protected $primaryKey = 'role_id';
    public $incrementing = true;

    protected $fillable = [
        'role_code',
        'role_keterangan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($role_id=false, $md5=true)
    {        
        $exncryptId = helpEncryptQuery('role_id', 'role', 'id_role', $md5);

        $result = DB::table(DB::raw('sys_role_user'))->select(DB::raw($exncryptId.', role_code AS role, role_keterangan AS keterangan'))->whereNull('deleted_at');

        if($role_id == true){
            $result = $result->where(DB::raw("MD5(CONCAT(role_id, '".encText('role')."'))"), $role_id)->first();
        }else{
            $result = $result->orderBy('role_id', 'asc')->get();
        }

        return $result;
    }
}