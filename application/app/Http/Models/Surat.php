<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Surat extends Model
{
    use SoftDeletes;
    protected $table = 'surat';
    protected $primaryKey = 'srt_id';
    public $connection = 'pgsql';
    public $incrementing = true;


    protected $fillable = [
        'srt_judul',
        'srt_keterangan',
        'srt_tanggal',
        'srt_path_file',
        'srt_halaman',
        'srt_ttd_id',
        'srt_state',
        'srt_pegawai',
        'srt_batch',
        'srt_arsip',
        'srt_portrait',
        'srt_landscape',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by'
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = ['created_at', 'created_by','updated_at', 'updated_by', 'deleted_at', 'deleted_by'];

    public static function get_data($id_surat = false, $md5=true, $filter_status=false, $field=false, $sort=false)
    {
        $encryptPrimary = encText('surat');

        $result = DB::table(DB::raw('surat srt'))
        ->select(DB::raw( (($md5 == true)? "MD5(CONCAT(srt_id, '".$encryptPrimary."'))" : "srt_id" )." AS id_surat, srt_judul AS judul, srt_keterangan AS keterangan,  srt_tanggal AS tanggal, srt_path_file AS path_file, srt_halaman AS halaman, srt_state AS state, srt_ttd_id AS id_ttd, ttd_nama AS nama_ttd, ttd_keterangan AS keterangan_ttd, srt_approved_at AS tanggal_approve, srt_rejected_at AS tanggal_reject, srj_id AS id_jenis, srj_nama AS jenis, srt_rollback AS rollback, srt_jbt_id_start AS id_jabatan_pembuat, srt_jbt_id AS id_jabatan, srt_pegawai AS pegawai, srt_pgw_id AS id_pegawai, pgw_nama AS nama_pegawai, (SELECT COUNT(*) FROM surat_history WHERE srh_srt_id = srt_id) AS langkah, srt_batch AS batch, srt_arsip AS arsip, srt_portrait AS portrait, srt_landscape AS landscape, srt_tanggal_penomoran AS tanggal_penomoran, srt_nomor_surat AS nomor_surat"))
        ->leftJoin(DB::raw('tanda_tangan ttd'), 'ttd_id', '=', 'srt_ttd_id')
        ->leftJoin(DB::raw('surat_jenis srj'), 'srj_id', '=', 'srt_srj_id')
        ->leftJoin(DB::raw('pegawai pgw'), 'pgw_id', '=', 'srt_pgw_id')
        ->whereNull(DB::raw('srt.deleted_at'));

        if($filter_status == true){
            $result = $result->where('srt_state', '=', $filter_status);
        }

        if($field == true && $sort == true){
            $result = $result->orderBy($field, $sort);
        }

        if($id_surat == true){
            $result = $result->where(DB::raw("MD5(CONCAT(srt_id, '".$encryptPrimary."'))"), $id_surat)->first();
        }else{
            $result  = $result->get();
        }
        
        return $result;
    }

    public static function json_grid($start, $length, $search='', $count=false, $sort, $field, $filter_status, $filter_ttd=false, $id_jabatan=false, $arsip='f')
    {
        $encryptPrimary = encText('surat');
        $query_tanggal = helpDateQuery('srt_tanggal', 'mi', 'pgsql');

        if($count == false){
            $result = DB::table(DB::raw('surat srt'))
            ->distinct()
            ->select(DB::raw("MD5(CONCAT(srt.srt_id, '".$encryptPrimary."')) AS id_surat, srt_judul AS judul, srt_keterangan AS keterangan,  srt_tanggal AS tanggal, srt_path_file AS path_file, srt_halaman AS halaman, srt_state AS state, srt_ttd_id AS id_ttd, ttd_nama AS nama_ttd, ttd_keterangan AS keterangan_ttd, srt_approved_at AS tanggal_approve, srt_rejected_at AS tanggal_reject, srj_id AS id_jenis, srj_nama AS jenis, srt_rollback AS rollback, srt_jbt_id_start AS id_jabatan_pembuat, srt_jbt_id AS id_jabatan, srt_pegawai AS pegawai, srt_pgw_id AS id_pegawai, pgw_nama AS nama_pegawai, v_hps.total as langkah, srt_arsip AS arsip, srt_portrait AS portrait, srt_landscape AS landscape, srs.srs_srt_id, srs.srs_path_file, srt.srt_nomor_surat"))
            ->leftJoin(DB::raw('tanda_tangan ttd'), 'ttd_id', '=', 'srt_ttd_id')
            ->leftJoin(DB::raw('surat_jenis srj'), 'srj_id', '=', 'srt_srj_id')
            ->leftJoin(DB::raw('pegawai pgw'), 'pgw_id', '=', 'srt_pgw_id')
            ->leftJoin(DB::raw('v_history_per_surat v_hps'), 'v_hps.srt_id', '=', 'srt.srt_id')
            ->leftJoin(DB::raw('(SELECT * FROM surat_selesai WHERE deleted_at ISNULL) srs'), 'srs_srt_id', '=', 'srt.srt_id')
            ->where('srt_arsip', $arsip)
            ->whereNull(DB::raw('srt.deleted_at'));

            if(!empty($search)){
                $result = $result->where(function($where) use($search, $query_tanggal){
                    $where->where('srt_judul', 'ILIKE', '%'.$search.'%')
                    ->orWhere('srt_pegawai', 'ILIKE', '%'.$search.'%')
                    ->orWhere('pgw_nama', 'ILIKE', '%'.$search.'%')
                    ->orWhere('srt_keterangan', 'ILIKE', '%'.$search.'%')
                    ->orWhere(DB::raw($query_tanggal), 'ILIKE', '%'.$search.'%');
                });
            }

            if($id_jabatan == true){
                $result = $result
                ->leftJoin(DB::raw('surat_history srh'), 'srh_srt_id', '=', 'srt.srt_id')
                ->leftJoin(DB::raw('jabatan jbt'), 'srh_jbt_id', '=', 'jbt.jbt_id')
                ->where('jbt_id', '=', $id_jabatan);
            }

            if($filter_status == true){
                switch ($filter_status) {
                    case 'waiting':
                    $result = $result->where('srt.srt_state', '=', 1);
                    break;
                    case 'approved':
                    $result = $result->whereNotNull('srt_approved_at');
                    break;
                    case 'rejected':
                    $result = $result->whereNotNull('srt_rejected_at');
                    break;

                    default:
                    # code...
                    break;
                }
            }

            $result  = $result->offset($start)->limit($length)->orderBy($field, $sort)->get();
        }else{
            $query = "SELECT COUNT(*) AS total FROM (SELECT DISTINCT srt_id AS id_surat, srt_judul AS judul, srt_keterangan AS keterangan, srt_tanggal AS tanggal, srt_path_file AS path_file, srt_halaman AS halaman, srt_state AS STATE, srt_ttd_id AS id_ttd, ttd_nama AS nama_ttd, ttd_keterangan AS keterangan_ttd, srt_approved_at AS tanggal_approve, srt_rejected_at AS tanggal_reject, srj_id AS id_jenis, srj_nama AS jenis, srt_rollback, srt_jbt_id AS id_jabatan FROM surat srt LEFT JOIN tanda_tangan ttd ON ttd_id = srt_ttd_id LEFT JOIN surat_jenis srj ON srj_id = srt_srj_id LEFT JOIN pegawai pgw ON pgw_id = srt_pgw_id";

            if($id_jabatan == true){
                $query .= " LEFT JOIN surat_history srh ON srh_srt_id = srt_id LEFT JOIN jabatan jbt ON srh_jbt_id = jbt_id";
            } 
            
            $query .= " WHERE srt.deleted_at IS NULL AND srt_arsip = '{$arsip}' ";

            if(!empty($search)){
                $query .= " AND (srt_judul ILIKE '%".$search."%' OR srt_keterangan ILIKE '%".$search."%' OR ".$query_tanggal." ILIKE '%".$search."%')";
            }

            if($id_jabatan == true){
                $query .= " AND jbt_id = ".$id_jabatan;
            }

            $query .= " ORDER BY judul DESC) AS abc";

            $result = DB::select($query);
            $result = $result[0]->total;
        }

        return $result;
    }

    public static function get_stats($filter_ttd=false, $id_jabatan=false)
    {
        $additional_query = '';
        // $additional_query = ($filter_ttd == true)? ' AND srt_ttd_id = '.$filter_ttd.' ' : '';

        $join = '';

        if($id_jabatan == true){
            $join = " LEFT JOIN surat_history srh ON srh_srt_id = srt_id LEFT JOIN jabatan jbt ON srh_jbt_id = jbt_id";
            $additional_query .= " AND jbt_id = ".$id_jabatan;
        }


        return DB::table(DB::raw('(SELECT (SELECT COUNT( * ) FROM (SELECT DISTINCT srt_id FROM surat '.$join.' WHERE surat.deleted_at IS NULL AND srt_approved_at IS NULL AND srt_rollback = TRUE AND srt_state = 1'.$additional_query.' ) AS a ) AS rejected,
            (SELECT COUNT( * ) FROM (SELECT DISTINCT srt_id FROM surat '.$join.' WHERE surat.deleted_at IS NULL AND srt_approved_at IS NOT NULL AND srt_rejected_at IS NULL AND srt_state > 1'.$additional_query.' ) AS b ) approved,
            (SELECT COUNT( * ) FROM (SELECT DISTINCT srt_id FROM surat '.$join.' WHERE surat.deleted_at IS NULL AND srt_approved_at IS NULL AND srt_rollback = FALSE AND srt_state = 1'.$additional_query.' ) AS c ) waiting
            FROM surat LIMIT 1) AS table1'))
        ->select('*')->first();
    }
}