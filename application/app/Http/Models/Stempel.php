<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Stempel extends Model
{
    use SoftDeletes;
    protected $table = 'stempel';
    protected $primaryKey = 'stp_id';
    public $connection = 'pgsql';
    public $incrementing = true;

    protected $fillable = [
        'stp_nama',
        'stp_path_file',
        'stp_aktif',
        'stp_uptd',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($id_stempel = false, $md5=true, $field=false, $sort=false)
    {
        $encryptId = helpEncryptQuery('stp_id', 'stempel', 'id_stempel', $md5);

        $result = DB::table(DB::raw('stempel'))
        ->select(DB::raw( $encryptId.", stp_nama AS nama_stempel, stp_path_file AS path_file, stp_aktif AS aktif, stp_uptd AS uptd"))
        ->whereNull(DB::raw('deleted_at'));

        if($field == true && $sort == true){
            $result = $result->orderBy($field, $sort);
        }

        if($id_stempel == true){
            $result = $result->where(DB::raw("MD5(CONCAT(stp_id, '".encText('stempel')."'))"), $id_stempel)->first();
        }else{
            $result  = $result->get();
        }
        
        return $result;
    }

    public static function json_grid($start, $length, $search='', $count=false, $sort, $field, $md5=true)
    {       
        $encryptId = helpEncryptQuery('stp_id', 'stempel', 'id_stempel', $md5);
        $result = DB::table(DB::raw('stempel'))
        ->select(DB::raw( $encryptId.", stp_nama AS nama_stempel, stp_path_file AS path_file, stp_aktif AS aktif, stp_uptd AS uptd"))
        ->whereNull(DB::raw('deleted_at'));

        if(!empty($search)){
            $result = $result->where(function($where) use($search){
                $where->where(DB::raw('stp_nama'), 'ILIKE', '%'.$search.'%');
            });
        }

        if($count == true){
            $result = $result->count();
        }else{
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }
}