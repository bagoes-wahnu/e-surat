<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SuratPnCode extends Model
{
	// use SoftDeletes;
	protected $table = 'surat_pn_code';
	protected $primaryKey = 'spn_id';
	public $incrementing = true;
	protected $connection = 'pgsql';

	protected $fillable = [
		'spn_srt_id',
		'spn_left',
		'spn_top',
        'spn_width',
        'spn_height',
		'spn_orientation',
		'created_at',
		'created_by',
		'updated_at',
		'updated_by',
		'deleted_at',
		'deleted_by'
	];

	// protected $dates = ['deleted_at'];

	protected $hidden = ['created_at', 'created_by', 'updated_at', 'updated_by'];

	public static function get_data($id_detail_pn = false, $id_surat = false, $md5 = true, $orientation = false)
	{
		$encryptStt = encText('pncode');
		$encryptSurat = encText('surat');
		$encryptTtd = encText('ttd');

		$result = DB::table(DB::raw('surat_pn_code'))
			->select(DB::raw((($md5 == true) ? "MD5(CONCAT(spn_id, '" . $encryptStt . "'))" : "spn_id") . ' AS id_detail_pn, ' . (($md5 == true) ? "MD5(CONCAT(spn_srt_id, '" . $encryptSurat . "'))" : "spn_srt_id") . ' AS id_surat, spn_left AS left, spn_top AS top, spn_height AS height, spn_width AS width, spn_orientation AS orientation'));

		if ($orientation == true) {
			$result = $result->where('spn_orientation', $orientation);
		}

		if ($id_surat == true) {
			$result = $result->where(DB::raw("MD5(CONCAT(spn_srt_id, '" . $encryptSurat . "'))"), '=', $id_surat);
		}

		if ($id_detail_pn == true) {
			$result = $result->where(DB::raw("MD5(CONCAT(spn_id, '" . $encryptStt . "'))"), $id_detail_pn)->first();
		} else {
			$result  = $result->get();
		}

		return $result;
	}
}
