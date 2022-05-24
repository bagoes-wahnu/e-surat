<?php

namespace App\Http\Models;

use App\Http\Models\E_Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SuratHistoryFile extends E_Model
{
    use SoftDeletes;
    protected $table = 'surat_history_file';
    protected $primaryKey = 'srhf_id';
    public $incrementing = true;

    protected $fillable = [
        'srhf_srh_id',
        'srhf_page',
        'srhf_path_file',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($id_file=false, $id_history=false, $md5=true, $single=false, $sort='ASC', $field='srhf_id')
    {
        $encryptId = helpEncryptQuery('srhf_id', 'surat_history_file', 'id_file', $md5);
        $encryptIdHistory = helpEncryptQuery('srhf_srh_id', 'surat_history', 'id_history', $md5);

        $result = DB::table(DB::raw('surat_history_file'))
        ->select(DB::raw($encryptId.", ".$encryptIdHistory.", srhf_page AS page, srhf_path_file AS path_file"))
        ->whereNull(DB::raw('deleted_at'));

        if($field == true && $sort == true){
            $result = $result->orderBy($field, $sort);
        }

        if($id_history == true){
            $result = $result->where(DB::raw("MD5(CONCAT(srhf_srh_id, '".encText('surat_history')."'))"), $id_history);
        }

        if($id_file == true){
            $result = $result->where(DB::raw("MD5(CONCAT(srhf_id, '".encText('surat_history_file')."'))"), $id_file)->first();
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