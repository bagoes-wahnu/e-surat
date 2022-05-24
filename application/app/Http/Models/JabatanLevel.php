<?php

namespace App\Http\Models;

use App\Http\Models\E_Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class JabatanLevel extends E_Model
{
    use SoftDeletes;
    protected $table = 'jabatan_level';
    protected $primaryKey = 'jbl_id';
    public $incrementing = true;

    protected $fillable = [
        'jbl_level',
        'jbl_nama',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($id_level = false, $md5=true, $single=false, $sort='ASC', $field='jbl_level')
    {
        $encryptId = helpEncryptQuery('jbl_id', 'level', 'id_level', $md5);

        $result = DB::table(DB::raw('jabatan_level'))
        ->select(DB::raw($encryptId.", jbl_level AS level, jbl_nama AS nama"))
        ->whereNull(DB::raw('deleted_at'));

        if($field == true && $sort == true){
            $result = $result->orderBy($field, $sort);
        }

        if($id_level == true){
            $result = $result->where(DB::raw("MD5(CONCAT(jbl_id, '".encText('level')."'))"), $id_level)->first();
        }else{
            if($single == true){
                $result  = $result->first();
            }else{
                $result  = $result->get();
            }
        }
        
        return $result;
    }
}