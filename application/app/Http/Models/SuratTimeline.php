<?php

namespace App\Http\Models;

use App\Http\Models\E_Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SuratTimeline extends E_Model
{
    use SoftDeletes;
    protected $table = 'surat_timeline';
    protected $primaryKey = 'stm_id';
    public $incrementing = true;

    protected $fillable = [
        'stm_srt_id',
        'stm_keterangan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($id_timeline = false, $id_surat = false, $md5=true)
    {
        $encryptIdTimeline = helpEncryptQuery('stm_id', 'timeline', 'id_timeline', $md5);
        $encryptIdSurat = helpEncryptQuery('stm_srt_id', 'surat', 'id_surat', $md5);
        $tanggal = helpDateQuery('created_at', 'mi', 'pgsql');

        $result = DB::table(DB::raw('surat_timeline stm'))
        ->select(DB::raw($encryptIdTimeline.", stm_keterangan AS keterangan, ".$encryptIdSurat.", created_at AS tanggal_input, ".$tanggal." tanggal"));

        if($id_surat == true){
            $result = $result->where(DB::raw("MD5(CONCAT(stm_srt_id, '".encText('surat')."'))"), $id_surat)->orderBy('created_at', 'ASC');
        }

        if($id_timeline == true){
            $result = $result->where(DB::raw("MD5(CONCAT(stm_id, '".encText('timeline')."'))"), $id_timeline)->first();
        }else{
            $result  = $result->get();
        }
        
        return $result;
    }
}