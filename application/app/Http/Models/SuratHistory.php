<?php

namespace App\Http\Models;

use App\Http\Models\E_Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SuratHistory extends E_Model
{
    use SoftDeletes;
    protected $table = 'surat_history';
    protected $primaryKey = 'srh_id';
    public $incrementing = true;

    protected $fillable = [
        'srh_srt_id',
        'srh_pgw_id',
        'srh_jbt_id',
        'srh_rollback',
        'srh_grade',
        'srh_with_upload',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($id_history=false, $id_surat=false, $md5=true, $single=false, $sort='DESC', $field='srh_id')
    {
        $encryptId = helpEncryptQuery('srh_id', 'surat_history', 'id_history', $md5);
        $encryptIdSurat = helpEncryptQuery('srh_srt_id', 'surat', 'id_surat', $md5);

        $result = DB::table(DB::raw('surat_history'))
        ->select(DB::raw($encryptId.", ".$encryptIdSurat.', srh_rollback AS rollback, srh_with_upload AS with_upload'))
        ->whereNull(DB::raw('deleted_at'));

        if($field == true && $sort == true){
            $result = $result->orderBy($field, $sort);
        }

        if($id_surat == true){
            $result = $result->where(DB::raw("MD5(CONCAT(srh_srt_id, '".encText('surat')."'))"), $id_surat);
        }

        if($id_history == true){
            $result = $result->where(DB::raw("MD5(CONCAT(srh_id, '".encText('surat_history')."'))"), $id_history)->first();
        }else{
            if($single == true){
                $result  = $result->first();
            }else{
                $result  = $result->get();
            }
        }
        
        return $result;
    }

    public static function get_user_rollback($id_surat, $batch)
    {
        $encryptPrimary = encText('surat');

        $result = DB::table('surat_history')
        ->select(DB::raw('srh_id AS id_history, srh_grade AS grade, srh_rollback AS rollback, srh_jbt_id AS id_jabatan, srh_keterangan AS keterangan, surat_history.created_at AS created_at, usr_name AS nama_jabatan'))
        ->join(DB::raw('"user"'), 'usr_id', 'surat_history.created_by')
        ->where(DB::raw("MD5(CONCAT(srh_srt_id, '".$encryptPrimary."'))"), $id_surat)
        ->where('srh_batch', $batch)
        ->where('srh_rollback', 't')
        ->orderBy('srh_id', 'ASC')->get();

        return $result;
    }

    public static function get_user_route($id_surat)
    {
        $encryptPrimary = encText('surat');

        $result = DB::table('surat_history')
        ->distinct()
        ->select(DB::raw('srh_srt_id AS id_surat, usr_id AS id_user, srh_jbt_id AS id_jabatan, srh_grade AS grade'))
        ->join(DB::raw('"user"'), 'usr_jbt_id', 'srh_jbt_id')
        ->where(DB::raw("MD5(CONCAT(srh_srt_id, '".$encryptPrimary."'))"), $id_surat)
        ->orderBy('srh_grade', 'DESC')->get();

        return $result;
    }

    public static function json_grid_rollback($start, $length, $search='', $count=false, $sort, $field, $id_surat=false)
    {       
        $encryptId = helpEncryptQuery('srh_id', 'surat_history', 'id_history, ', true);
        $encryptIdSurat = helpEncryptQuery('srh_srt_id', 'surat', 'id_surat, ', true);

        $result = DB::table(DB::raw('surat_history srh'))
        ->select(DB::raw($encryptId.$encryptIdSurat."srh_grade AS grade, srh_batch AS batch, srh_rollback AS rollback, srh_jbt_id AS id_jabatan, srh_keterangan AS keterangan, srh.created_at AS tanggal, usr_name AS nama_jabatan, (SELECT COUNT(*) FROM surat_history_file WHERE srhf_srh_id = srh_id) AS total_file"))
        ->join(DB::raw('"user"'), 'usr_id', 'srh.created_by')
        ->where('srh_rollback', 't')
        ->whereNull(DB::raw('srh.deleted_at'));
        
        if($id_surat == true){
            $result = $result->where(DB::raw("MD5(CONCAT(srh_srt_id, '".encText('surat')."'))"), $id_surat);
        }

        if(!empty($search)){
            $result = $result->where(function($where) use($search){
                $where->where(DB::raw('srh_grade'), 'ILIKE', '%'.$search.'%')
                ->orWhere(DB::raw('srh_keterangan'), 'ILIKE', '%'.$search.'%')
                ->orWhere(DB::raw('usr_name'), 'ILIKE', '%'.$search.'%');
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