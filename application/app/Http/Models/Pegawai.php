<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Pegawai extends Model
{
    use SoftDeletes;
    protected $table = 'pegawai';
    protected $primaryKey = 'pgw_id';
    public $connection = 'pgsql';
    public $incrementing = true;

    protected $fillable = [
        'pgw_nama',
        'pgw_keterangan',
        'pgw_path_file',
        'pgw_aktif',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($id_pegawai = false, $id_jabatan=false, $md5=true, $field=false, $sort=false)
    {
        $encryptId = helpEncryptQuery('pgw_id', 'pegawai', 'id_pegawai, ', $md5);
        $encryptIdJabatan = helpEncryptQuery('pgw_jbt_id', 'jabatan', 'id_jabatan, ', $md5);

        $result = DB::table(DB::raw('pegawai pgw'))
        ->select(DB::raw( $encryptId.$encryptIdJabatan."pgw.pgw_nama AS nama_pegawai, pgw_nik AS nik, pgw_nip AS nip, pgw_email AS email, pgw_telp AS telp, pgw_gender AS gender, pgw_foto AS foto, pgw_tanggal_pns AS tanggal_pns, jbt_nama AS nama_jabatan, jbt_induk AS id_atasan"))
        ->leftJoin(DB::raw('jabatan jbt'), DB::raw('pgw.pgw_jbt_id'), '=', DB::raw('jbt.jbt_id'))
        ->whereNull(DB::raw('pgw.deleted_at'));

        if($id_jabatan == true){
            $result = $result->where(DB::raw("MD5(CONCAT(pgw_jbt_id, '".encText('jabatan')."'))"), $id_jabatan);
        }
        if($field == true && $sort == true){
            $result = $result->orderBy($field, $sort);
        }

        if($id_pegawai == true){
            $result = $result->where(DB::raw("MD5(CONCAT(pgw_id, '".encText('pegawai')."'))"), $id_pegawai)->first();
        }else{
            $result  = $result->get();
        }
        
        return $result;
    }

    public static function json_grid($start, $length, $search='', $count=false, $sort, $field, $id_jabatan=false)
    {
        $md5 = true;
        $encryptId = helpEncryptQuery('pgw_id', 'pegawai', 'id_pegawai, ', $md5);
        $encryptIdJabatan = helpEncryptQuery('pgw_jbt_id', 'jabatan', 'id_jabatan, ', $md5);

        $result = DB::table(DB::raw('pegawai pgw'))
        ->select(DB::raw( $encryptId.$encryptIdJabatan."pgw.pgw_nama AS nama_pegawai, pgw_nik AS nik, pgw_nip AS nip, pgw_email AS email, pgw_telp AS telp, pgw_gender AS gender, pgw_foto AS foto, pgw_tanggal_pns AS tanggal_pns, jbt_nama AS nama_jabatan, jbt_induk AS id_atasan"))
        ->leftJoin(DB::raw('jabatan jbt'), DB::raw('pgw.pgw_jbt_id'), '=', DB::raw('jbt.jbt_id'))
        ->whereNull(DB::raw('pgw.deleted_at'));

        if($id_jabatan == true){
            $result = $result->where(DB::raw("MD5(CONCAT(pgw_jbt_id, '".encText('jabatan')."'))"), $id_jabatan);
        }

        if(!empty($search)){
            $result = $result->where(function($where) use($search){
                $where->where(DB::raw('pgw.pgw_nama'), 'ILIKE', '%'.$search.'%');
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