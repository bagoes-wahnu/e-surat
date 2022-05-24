<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SuratTandaTangan extends Model
{
	// use SoftDeletes;
    protected $table = 'surat_tanda_tangan';
    protected $primaryKey = 'stt_id';
    public $incrementing = true;
    protected $connection = 'pgsql';

    protected $fillable = [
        'stt_srt_id',
        'stt_ttd_id',
        'stt_page',
        'stt_left',
        'stt_top',
        'stt_warna',
        'stt_width',
        'stt_height',
        'stt_orientation',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    // protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by'];

    public static function get_data($id_detail=false, $id_surat=false, $id_ttd=false, $md5=true, $orientation=false)
    {
        $encryptId =  helpEncryptQuery('ttd_id', 'ttd', 'id_ttd', $md5);
        $encryptStt = helpEncryptQuery('stt_id', 'stt', 'id_detail', $md5);
        $encryptSurat = helpEncryptQuery('stt_srt_id', 'surat', 'id_surat', $md5);
        $encryptTtd = helpEncryptQuery('stt_ttd_id', 'ttd', 'id_ttd', $md5);

        $result = DB::table(DB::raw('surat_tanda_tangan'))
        ->select(DB::raw( $encryptStt.', '.$encryptSurat.', '.$encryptTtd.', stt_page AS page, stt_left AS left, stt_top AS top, stt_width AS width, stt_height AS height, ttd_nama AS nama_ttd, stt_warna AS warna, (CASE stt_warna WHEN \'hitam\' THEN ttd_path_file_hitam WHEN \'merah\' THEN ttd_path_file_merah WHEN \'biru\' THEN ttd_path_file_biru ELSE NULL END) AS path_file, stt_orientation AS orientation'))
        ->leftJoin(DB::raw('tanda_tangan ttd'), 'ttd_id', '=', 'stt_ttd_id');
        
        if($orientation == true){
            $result = $result->where('stt_orientation', $orientation);
        }
        
        if($id_surat == true){
            $result = $result->where(DB::raw("MD5(CONCAT(stt_srt_id, '".encText('surat')."'))"), '=', $id_surat);
        }

        if($id_ttd == true){
            $result = $result->where(DB::raw("MD5(CONCAT(stt_ttd_id, '".encText('ttd')."'))"), '=', $id_ttd);
        }

        if($id_detail == true){
            $result = $result->where(DB::raw("MD5(CONCAT(stt_id, '".encText('stt')."'))"), $id_detail)->first();
        }else{
            $result  = $result->get();
        }

        return $result;
    }
}