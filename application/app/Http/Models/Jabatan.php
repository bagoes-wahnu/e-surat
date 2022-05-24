<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Jabatan extends Model
{
    use SoftDeletes;
    protected $table = 'jabatan';
    protected $primaryKey = 'jbt_id';
    public $connection = 'pgsql';
    public $incrementing = true;

    protected $fillable = [
        'jbt_nama',
        'jbt_keterangan',
        'jbt_path_file',
        'jbt_aktif',
        'jbt_uptd',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_pegawai($id_jabatan, $md5=true)
    {
        $result = DB::table(DB::raw('jabatan jbt'))
        ->select(DB::raw( (($md5 == true)? "MD5(CONCAT(jbt_id, '".encText('jabatan')."'))" : "jbt_id" )." AS id_jabatan, jbt_nama AS nama_jabatan, ".(($md5 == true)? "MD5(CONCAT(pgw_id, '".encText('pegawai')."'))" : "pgw_id" )." AS id_pegawai, pgw_nama AS nama_pegawai"))
        ->leftJoin(DB::raw('pegawai pgw'), DB::raw('pgw_jbt_id'), '=', DB::raw('jbt_id'))
        ->whereNull(DB::raw('jbt.deleted_at'))
        ->whereNull(DB::raw('pgw.deleted_at'))
        ->where(DB::raw("MD5(CONCAT(jbt_id, '".encText('jabatan')."'))"), $id_jabatan)
        ->orderBy('pgw_id', 'ASC')
        ->get();
        
        return $result;
    }

    public static function get_data($id_jabatan = false, $md5=true, $field=false, $sort=false)
    {
        $encryptId = helpEncryptQuery('jbt1.jbt_id', 'jabatan', 'id_jabatan', $md5);
        $encryptIdAtasan = helpEncryptQuery('jbt1.jbt_induk', 'jabatan', 'id_atasan', $md5);

        $result = DB::table(DB::raw('jabatan jbt1'))
        ->select(DB::raw( $encryptId.", jbt1.jbt_nama AS nama_jabatan, ".$encryptIdAtasan.", jbt2.jbt_nama AS nama_jabatan_atasan, jbl_id AS id_level, jbl_level AS level, jbl_nama AS nama_level, jbt1.jbt_uptd AS uptd"))
        ->leftJoin(DB::raw('jabatan jbt2'), DB::raw('jbt2.jbt_id'), '=', DB::raw('jbt1.jbt_induk'))
        ->leftJoin(DB::raw('jabatan_level jbl'), 'jbl_id', '=', DB::raw('jbt1.jbt_jbl_id'))
        ->whereNull(DB::raw('jbt1.deleted_at'))
        ->whereNull(DB::raw('jbt2.deleted_at'));

        if($field == true && $sort == true){
            $result = $result->orderBy($field, $sort);
        }

        if($id_jabatan == true){
            $result = $result->where(DB::raw("MD5(CONCAT(jbt1.jbt_id, '".encText('jabatan')."'))"), $id_jabatan)->first();
        }else{
            $result  = $result->get();
        }
        
        return $result;
    }

    public static function json_grid($start, $length, $search='', $count=false, $sort, $field, $md5=true)
    {       
        $encryptId = helpEncryptQuery('jbt1.jbt_id', 'jabatan', 'id_jabatan', $md5);
        $encryptIdAtasan = helpEncryptQuery('jbt1.jbt_induk', 'jabatan', 'id_atasan', $md5);

        $result = DB::table(DB::raw('jabatan jbt1'))
        ->select(DB::raw( $encryptId.", jbt1.jbt_nama AS nama_jabatan, ".$encryptIdAtasan.", jbt2.jbt_nama AS nama_jabatan_atasan, jbl_id AS id_level, jbl_level AS level, jbl_nama AS nama_level, jbt1.jbt_uptd AS uptd"))
        ->leftJoin(DB::raw('jabatan jbt2'), DB::raw('jbt2.jbt_id'), '=', DB::raw('jbt1.jbt_induk'))
        ->leftJoin(DB::raw('jabatan_level jbl'), 'jbl_id', '=', DB::raw('jbt1.jbt_jbl_id'))
        ->whereNull(DB::raw('jbt1.deleted_at'))
        ->whereNull(DB::raw('jbt2.deleted_at'));

        if(!empty($search)){
            $result = $result->where(function($where) use($search){
                $where->where(DB::raw('jbt1.jbt_nama'), 'ILIKE', '%'.$search.'%')
                ->orWhere(DB::raw('jbt2.jbt_nama'), 'ILIKE', '%'.$search.'%');
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