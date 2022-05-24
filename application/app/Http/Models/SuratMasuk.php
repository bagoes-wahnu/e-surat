<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SuratMasuk extends Model
{
    use SoftDeletes;
    protected $table = 'surat_masuk';
    protected $primaryKey = 'srm_id';
    public $connection = 'pgsql';
    public $incrementing = true;

    protected $fillable = [
        'srm_no',
        'srm_judul',
        'srm_tanggal',
        'srm_pengirim',
        'srm_path_file',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($id_surat_masuk = false, $md5=true, $field=false, $sort=false)
    {
        $encryptId = helpEncryptQuery('srm_id', 'surat_masuk', 'id_surat_masuk', $md5);

        $result = DB::table(DB::raw('surat_masuk srm'))
        ->select(DB::raw($encryptId.", srm_no AS no_surat, srm_judul AS judul, srm_tanggal AS tanggal, srm_pengirim AS pengirim, srm_path_file AS path_file"))
        ->whereNull(DB::raw('srm.deleted_at'));

        if($field == true && $sort == true){
            $result = $result->orderBy($field, $sort);
        }

        if($id_surat_masuk == true){
            $result = $result->where(DB::raw("MD5(CONCAT(srm_id, '".encText('surat_masuk')."'))"), $id_surat_masuk)->first();
        }else{
            $result  = $result->get();
        }
        
        return $result;
    }

    public static function json_grid($start, $length, $search='', $count=false, $sort, $field, $id_jabatan=false)
    {       
        $encryptId = helpEncryptQuery('srm_id', 'surat_masuk', 'id_surat_masuk', true);

        $result = DB::table(DB::raw('surat_masuk srm'))
        ->select(DB::raw($encryptId.", srm_no AS no_surat, srm_judul AS judul, srm_tanggal AS tanggal, srm_pengirim AS pengirim, srm_path_file AS path_file"))
        ->join(DB::raw('"user" usr'), 'usr_id', '=', 'srm.created_by')
        ->whereNull(DB::raw('srm.deleted_at'));

        if($id_jabatan == true){
            $result = $result->where('usr_jbt_id', '=', $id_jabatan);
        }

        if(!empty($search)){
            $result = $result->where(function($where) use($search){
                $where->where(DB::raw('srm_no'), 'ILIKE', '%'.$search.'%')
                ->orWhere(DB::raw('srm_judul'), 'ILIKE', '%'.$search.'%')
                ->orWhere(DB::raw('srm_pengirim'), 'ILIKE', '%'.$search.'%')
                ->orWhere(DB::raw(helpDateQuery('srm_tanggal', 'mi', 'pgsql')), 'ILIKE', '%'.$search.'%');
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