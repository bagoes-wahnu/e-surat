<?php

namespace App\Http\Models;

use App\Http\Models\E_Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SuratJenis extends E_Model
{
    use SoftDeletes;
    protected $table = 'surat_jenis';
    protected $primaryKey = 'srj_id';
    public $incrementing = true;

    protected $fillable = [
        'srj_nama',
        'srj_urutan',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($id_jenis = false, $md5=true, $single=false, $sort='ASC', $field='srj_nama')
    {
        $encryptId = helpEncryptQuery('srj_id', 'jenis', 'id_jenis, ', $md5);

        $result = DB::table(DB::raw('surat_jenis'))
        ->select(DB::raw($encryptId."srj_nama AS jenis, srj_urutan AS urutan"))
        ->whereNull(DB::raw('deleted_at'))
	->where('srj_tipe',0);
        if($field == true && $sort == true){
            $result = $result->orderBy($field, $sort);
        }

        if($id_jenis == true){
            $result = $result->where(DB::raw("MD5(CONCAT(srj_id, '".encText('jenis')."'))"), $id_jenis)->first();
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
