<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TandaTangan extends Model
{
    use SoftDeletes;
    protected $table = 'tanda_tangan';
    protected $primaryKey = 'ttd_id';
    public $connection = 'pgsql';
    public $incrementing = true;

    protected $fillable = [
        'ttd_nama',
        'ttd_keterangan',
        'ttd_path_file_hitam',
        'ttd_path_file_merah',
        'ttd_path_file_biru',
        'ttd_aktif',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($id_ttd = false, $md5=true, $aktif=false, $field=false, $sort=false)
    {
        $encryptId =  helpEncryptQuery('ttd_id', 'ttd', 'id_ttd', $md5);
        $encryptIdPegawai =  helpEncryptQuery('ttd_pgw_id', 'pegawai', 'id_pegawai', $md5);
        $encryptIdJabatan =  helpEncryptQuery('ttd_jbt_id', 'jabatan', 'id_jabatan', $md5);

        $result = DB::table(DB::raw('tanda_tangan'))
        ->select(DB::raw( $encryptId.", ttd_nama AS nama, ttd_keterangan AS keterangan, ttd_path_file_hitam AS path_file_hitam, ttd_path_file_merah AS path_file_merah, ttd_path_file_biru AS path_file_biru, ttd_aktif AS aktif, ".$encryptIdPegawai.", ".$encryptIdJabatan))
        ->whereNull('deleted_at');

        if($aktif == true){
            $result = $result->where('ttd_aktif', '=', $aktif);
        }

        if($field == true && $sort == true){
            $result = $result->orderBy($field, $sort);
        }

        if($id_ttd == true){
            $result = $result->where(DB::raw("MD5(CONCAT(ttd_id, '".encText('ttd')."'))"), $id_ttd)->first();
        }else{
            $result  = $result->get();
        }
        
        return $result;
    }

    public static function json_grid($start, $length, $search='', $count=false, $sort, $field, $condition)
    {
        $md5 = true;
        $encryptId =  helpEncryptQuery('ttd_id', 'ttd', 'id_ttd', $md5);
        $encryptIdPegawai =  helpEncryptQuery('ttd_pgw_id', 'pegawai', 'id_pegawai', $md5);
        $encryptIdJabatan =  helpEncryptQuery('ttd_jbt_id', 'jabatan', 'id_jabatan', $md5);

        $result = DB::table(DB::raw('tanda_tangan'))
        ->select(DB::raw( $encryptId.", ttd_nama AS nama, ttd_keterangan AS keterangan, ttd_path_file_hitam AS path_file_hitam, ttd_path_file_merah AS path_file_merah, ttd_path_file_biru AS path_file_biru, ttd_aktif AS aktif, ".$encryptIdPegawai.", ".$encryptIdJabatan))
        ->whereNull('deleted_at');

        if(!empty($search)){
            $result = $result->where(function($where) use($search){
                $where->where('ttd_nama', 'ILIKE', '%'.$search.'%')
                ->orWhere('ttd_keterangan', 'ILIKE', '%'.$search.'%');
            });
        }

        if($condition == true){
            $result = $result->where('ttd_aktif', '=', $condition);
        }

        if($count == true){
            $result = $result->count();
        }else{
            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }

        return $result;
    }
}