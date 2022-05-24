<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SuratQrCode extends Model
{
	// use SoftDeletes;
	protected $table = 'surat_qr_code';
	protected $primaryKey = 'sqr_id';
	public $incrementing = true;
	protected $connection = 'pgsql';

	protected $fillable = [
		'sqr_srt_id',
		'sqr_left',
		'sqr_top',
		'sqr_orientation',
		'created_at',
		'created_by',
		'updated_at',
		'updated_by',
		'deleted_at',
		'deleted_by'
	];

	// protected $dates = ['deleted_at'];

	protected $hidden = ['created_at', 'created_by', 'updated_at', 'updated_by'];

	public static function get_data($id_detail_qr = false, $id_surat = false, $md5 = true, $orientation = false)
	{
		$encryptStt = encText('qrcode');
		$encryptSurat = encText('surat');
		$encryptTtd = encText('ttd');

		$result = DB::table(DB::raw('surat_qr_code'))
			->select(DB::raw((($md5 == true) ? "MD5(CONCAT(sqr_id, '" . $encryptStt . "'))" : "sqr_id") . ' AS id_detail_qr, ' . (($md5 == true) ? "MD5(CONCAT(sqr_srt_id, '" . $encryptSurat . "'))" : "sqr_srt_id") . ' AS id_surat, sqr_left AS left, sqr_top AS top, sqr_orientation AS orientation'));

		if ($orientation == true) {
			$result = $result->where('sqr_orientation', $orientation);
		}

		if ($id_surat == true) {
			$result = $result->where(DB::raw("MD5(CONCAT(sqr_srt_id, '" . $encryptSurat . "'))"), '=', $id_surat);
		}

		if ($id_detail_qr == true) {
			$result = $result->where(DB::raw("MD5(CONCAT(sqr_id, '" . $encryptStt . "'))"), $id_detail_qr)->first();
		} else {
			$result  = $result->get();
		}

		return $result;
	}
}
