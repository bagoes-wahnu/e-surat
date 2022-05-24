<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SuratStempel extends Model
{
    // use SoftDeletes;
    protected $table = 'surat_stempel';
    protected $primaryKey = 'sstp_id';
    public $incrementing = true;
    protected $connection = 'pgsql';

    protected $fillable = [
        'sstp_srt_id',
        'sstp_stp_id',
        'sstp_left',
        'sstp_top',
        'sstp_warna',
        'sstp_width',
        'sstp_height',
        'sstp_orientation',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    // protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by', 'updated_at', 'updated_by'];

    public static function get_data($id_detail = false, $id_surat = false, $id_stempel = false, $md5 = true, $orientation = false)
    {
        $encryptSstp = helpEncryptQuery('sstp_id', 'sstp', 'id_detail', $md5);
        $encryptSurat = helpEncryptQuery('sstp_srt_id', 'surat', 'id_surat', $md5);
        $encryptStp = helpEncryptQuery('sstp_stp_id', 'stempel', 'id_stempel', $md5);

        $result = DB::table(DB::raw('surat_stempel'))
            ->select(DB::raw($encryptSstp . ', ' . $encryptSurat . ', ' . $encryptStp . ', sstp_left AS left, sstp_top AS top, sstp_width AS width, sstp_height AS height, stp_nama AS nama_stempel, stp_path_file AS path_file, sstp_orientation AS orientation'))
            ->leftJoin(DB::raw('stempel stp'), 'stp_id', '=', 'sstp_stp_id');

        if ($orientation == true) {
            $result = $result->where('sstp_orientation', $orientation);
        }

        if ($id_surat == true) {
            $result = $result->where(DB::raw("MD5(CONCAT(sstp_srt_id, '" . encText('surat') . "'))"), '=', $id_surat);
        }

        if ($id_stempel == true) {
            $result = $result->where(DB::raw("MD5(CONCAT(sstp_stp_id, '" . encText('stempel') . "'))"), '=', $id_stempel);
        }

        if ($id_detail == true) {
            $result = $result->where(DB::raw("MD5(CONCAT(sstp_id, '" . encText('sstp') . "'))"), $id_detail)->first();
        } else {
            $result  = $result->get();
        }

        return $result;
    }
}
