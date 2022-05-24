<?php

namespace App\Http\Controllers;

use App\Events\SuratKeluarUploadPdf;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Firebase;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

use App\Http\Models\TandaTangan;
use App\Http\Models\Stempel;
use App\Http\Models\Surat;
use App\Http\Models\SuratQrCode;
use App\Http\Models\SuratPnCode;
use App\Http\Models\SuratTandaTangan;
use App\Http\Models\SuratStempel;
use App\Http\Models\SuratTimeline;
use App\Http\Models\SuratHistory;
use App\Http\Models\SuratHistoryFile;
use App\Http\Models\SuratJenis;
use App\Http\Models\SuratSelesai;
use App\Http\Models\Jabatan;
use App\Http\Models\JabatanLevel;
use App\Http\Models\Pegawai;
use App\Http\Models\SuratHalaman;
use App\Http\Models\User;
use App\Helpers\MyHelper;

use Spatie\PdfToImage\Pdf as PdfToImage;
use Dompdf\Dompdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;

class SuratController extends Controller
{
	public function index(Request $request)
	{
		$data['tanda_tangan'] = [];
		$arr_ttd = [1, 2];
		if ($this->userdata('uptd') == true) {
			$arr_ttd[] = 3;
		}

		foreach ($arr_ttd as $key => $value) {
			$data['tanda_tangan'][] = TandaTangan::get_data(encText($value . 'ttd', true), false, 't');
		}

		$id_jabatan = (empty($this->userdata('id_jabatan'))) ? '' : encText($this->userdata('id_jabatan') . 'jabatan', true);

		$advance = ($this->userdata('role') == 1 && $request->input('code') == 'iuvadfb') ? 't' : 'f';
		$data['advance'] = $advance;

		$data['title'] = 'Daftar Surat Keluar';
		$data['menu_active'] = 'surat';
		$data['last_level'] = $this->lastGrade();
		$data['grade'] = $this->userdata('grade');
		$data['jenis_surat'] = SuratJenis::get_data(false, false, false, 'ASC', 'srj_urutan');
		$data['pegawai'] = Pegawai::get_data(false, $id_jabatan, false, 'pgw_nama', 'ASC');
		$data['role'] = $this->userdata('role');
		return view('surat/grid', $data);

	}

	public function jsonShow($id_surat, Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		if (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$get_surat = Surat::get_data($id_surat, false, false);
			if (!empty($get_surat)) {
				$file = [];
				$with_upload = true;

				if ($get_surat->rollback == false) {
					$responseData['new'] = true;
					for ($i = 1; $i <= $get_surat->halaman; $i++) {
						$file[] = ['page' => $i, 'url' => base_url('watch/' . $get_surat->judul . '?un=' . $id_surat . '&ct=surat&src=page-' . $i . '.png')];
					}
				} else {
					$responseData['new'] = false;
					$get_last_history = SuratHistory::where('srh_srt_id', $get_surat->id_surat)->where('srh_rollback', true)->orderBy('srh_id', 'DESC')->first();

					$with_upload = $get_last_history->srh_with_upload;

					$get_file_history = SuratHistoryFile::get_data(false, encText($get_last_history->srh_id . 'surat_history', true));

					foreach ($get_file_history as $key => $value) {
						$file[] = ['page' => $value->page, 'url' => base_url('watch/' . $get_surat->judul . '?un=' . $value->id_file . '&ct=history&src=page-' . $value->page . '.png')];
					}
				}

				$get_surat->id_surat = encText($get_surat->id_surat . 'surat', true);

				$responseData['surat'] = $get_surat;
				$responseData['note_rollback'] = $file;
				$responseData['with_upload'] = $with_upload;

				/* start : get_user_rollback */
				$arr_user = [];
				$arr_keterangan = [];

				$get_user_rollback = SuratHistory::get_user_rollback($get_surat->id_surat, $get_surat->batch);

				foreach ($get_user_rollback as $key => $value) {
					if (!in_array($value->keterangan, $arr_keterangan)) {
						$arr_keterangan[] = $value->keterangan;
						$arr_user[] = ['id_jabatan' => $value->id_jabatan, 'nama_jabatan' => $value->nama_jabatan];
					}
				}
				$responseData['user_revisi'] = $arr_user;
				/* end : get_user_rollback */

				$responseCode = 200;
			} else {
				$responseCode = 400;
				$responseMessage = 'Data tidak tersedia!';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function show($id_surat, Request $request)
	{
		$arr_orientation = ['portrait', 'landscape', 'default'];

		$get_surat = Surat::get_data($id_surat);
		if (!empty($get_surat) && ($get_surat->state > 1) && !empty($get_surat->tanggal_approve) && ($this->userdata('id_jabatan') == $get_surat->id_jabatan)) {
			$surat2 = Surat::get_data($id_surat, false);

			$urlPage = myStorage('surat/' . $surat2->id_surat . '/page-1.png');
			if (file_exists($urlPage) && !is_dir($urlPage)) {
				$page_orientation = request()->get('orientation');

				if (!empty($page_orientation) && in_array($page_orientation, $arr_orientation)) {
					$data['title'] = $get_surat->judul;
					$data['menu_active'] = 'surat';
					$data['surat'] = $get_surat;
					$data['surat2'] = $surat2;
					$data['halaman'] = SuratHalaman::where('srt_id', $surat2->id_surat)->where('orientation', $page_orientation)->get();
					$data['page_orientation'] = $page_orientation;
					return view('surat/ttd', $data);


				} else {
					return $this->pageNotFound();
				}
			} else {
				return $this->pageNotFound();
			}
		} else {
			return $this->pageNotFound();
		}
	}

	public function pdfToBe($id_surat, Request $request)
	{
		$get_surat = Surat::get_data($id_surat);
		if (!empty($get_surat) && ($get_surat->state > 1) && !empty($get_surat->tanggal_approve) && ($this->userdata('id_jabatan') == $get_surat->id_jabatan)) {
			$surat2 = Surat::get_data($id_surat, false);

			$urlPage = myStorage('surat/' . $surat2->id_surat . '/page-1.png');
			if (file_exists($urlPage) && !is_dir($urlPage)) {
				$data['title'] = $get_surat->judul;
				$data['menu_active'] = 'surat';
				$data['surat'] = $get_surat;
				$data['surat2'] = $surat2;
				$data['surat_ttd'] = SuratTandaTangan::get_data(false, $id_surat, false);
				$data['surat_stempel'] = SuratStempel::get_data(false, $id_surat, false);
				$data['surat_qr'] = SuratQrCode::get_data(false, $id_surat);
				return view('surat/pdf', $data);
			} else {
				return $this->pageNotFound();
			}
		} else {
			return $this->pageNotFound();
		}
	}

	public function scanQr($id_surat, Request $request)
	{
		$get_surat = Surat::get_data($id_surat);
		if (!empty($get_surat) && ($get_surat->state > 1) && !empty($get_surat->tanggal_approve)) {
			$source = base_url('scan/watch/' . $get_surat->judul . '?un=' . $get_surat->id_surat . '&ct=surat&src=' . $get_surat->path_file);

			return redirect()->intended($source);
			// echo file_get_contents($source);
		} else {
			return $this->pageNotFound();
		}
	}

	public function store(Request $request, Surat $m_surat)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$suratIsNew = true;

		$rules['pegawai'] = 'required';
		$rules['judul'] = 'required';
		$rules['jenis_surat'] = 'required';
		$rules['tanggal'] = 'required';
		$rules['pejabat'] = 'required';
		$rules['action'] = 'required';

		$action = $request->input('action');

		if ($action == 'edit') {
			$rules['id_surat'] = 'required';
		}

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		} elseif (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$id_surat = $request->input('id_surat');
			$srt_pgw_id = helpText($request->input('pegawai'));
			$srt_judul = helpText($request->input('judul'));
			$srt_ttd_id = $request->input('pejabat');
			$srt_srj_id = $request->input('jenis_surat');
			$tanggal = $request->input('tanggal');

			$tanggal = explode('/', $tanggal);
			$srt_tanggal = $tanggal[2] . '-' . $tanggal[1] . '-' . $tanggal[0];

			$firebase_receiver = [];

			$id_jabatan = encText($this->userdata('id_jabatan') . 'jabatan', true);
			$get_jabatan = Jabatan::get_data($id_jabatan, false);

			$srh_rollback = false;

			if (!empty($id_surat)) {
				$get_surat = Surat::get_data($id_surat, false);
				if (!empty($get_surat)) {
					$suratIsNew = false;
					$m_surat = Surat::find($get_surat->id_surat);
					// $m_surat->updated_by = $this->userdata('id_user');
					// $m_surat->srt_rollback = false;

					// $batch = $get_surat->batch;
					// $batch++;
					// $m_surat->srt_batch = $batch;

					// 		$stm_keterangan = $this->userdata('nama') . ' melakukan perubahan pada surat';
					// 		$firebase_msg = $this->userdata('nama') . ' telah melakukan perubahan pada ' . $m_surat->srt_judul . ' dan menunggu persetujuan Anda!';
				}
			}
			// else {
			// 	$m_surat->created_by = $this->userdata('id_user');
			// 	$m_surat->srt_halaman = 0;
			// 	$m_surat->srt_state = 1;
			// 	$m_surat->srt_batch = 1;

			// 	$m_surat->srt_jbt_id_start = $this->userdata('id_jabatan');
			// 	$m_surat->srt_jbt_id = $this->userdata('id_jabatan');

			// 	$stm_keterangan = $this->userdata('nama') . ' membuat surat';
			// 	$firebase_msg = $this->userdata('nama') . ' membuat ' . $m_surat->srt_judul . ' dan menunggu persetujuan Anda!';
			// }

			// /* start : set firebase receiver */
			// if (!empty($get_jabatan->id_atasan)) {
			// 	/* start : menentukan jabatan tujuan */
			// 	$m_surat->srt_jbt_id = $get_jabatan->id_atasan;
			// 	/* end : menentukan jabatan tujuan */

			// 	$get_id_user = User::where('usr_jbt_id', $get_jabatan->id_atasan)->first();
			// 	if (!empty($get_id_user)) {
			// 		$firebase_receiver[] = $get_id_user->usr_id;
			// 	}
			// }
			// /* end : set firebase receiver */

			$m_surat->srt_pgw_id = $srt_pgw_id;
			$m_surat->srt_judul = $srt_judul;
			$m_surat->srt_tanggal = $srt_tanggal;
			$m_surat->srt_ttd_id = $srt_ttd_id;
			$m_surat->srt_srj_id = $srt_srj_id;
			$m_surat->save();

			/* start : membuat QR Code */
			$destinationPath = myStorage('surat/' . $m_surat->srt_id . '/');
			helpCreateDirectory($destinationPath);

			QrCode::format('png')->size(200)->margin(1)->generate(base_url('scan/' . encText($m_surat->srt_id . 'surat', true)), $destinationPath . 'qrcode.png');
			/* end : membuat QR Code */

			// /* start : menyimpan timeline */
			// $m_timeline = new SuratTimeline();

			// $m_timeline->stm_srt_id = $m_surat->srt_id;
			// $m_timeline->stm_keterangan = $stm_keterangan;
			// $m_timeline->created_by = $this->userdata('id_user');
			// $m_timeline->save();
			// /* end : menyimpan timeline */

			// /* start : menyimpan history surat */
			// /* jika surat masih baru */
			// if ($suratIsNew == true) {
			// 	$m_history = new SuratHistory();

			// 	$m_history->srh_srt_id = $m_surat->srt_id;
			// 	$m_history->srh_jbt_id = $this->userdata('id_jabatan');
			// 	$m_history->srh_rollback = $srh_rollback;
			// 	$m_history->srh_grade = $this->userdata('grade');
			// 	$m_history->created_by = $this->userdata('id_user');
			// 	$m_history->save();
			// }

			// /* jika pembuat surat punya atasan */
			// if (!empty($get_jabatan->id_atasan)) {
			// 	$m_history = new SuratHistory();
			// 	$get_atasan = Jabatan::get_data(encText($get_jabatan->id_atasan . 'jabatan', true), false);

			// 	$m_history->srh_srt_id = $m_surat->srt_id;
			// 	$m_history->srh_jbt_id = $get_atasan->id_jabatan;
			// 	$m_history->srh_rollback = $srh_rollback;
			// 	$m_history->srh_grade = $get_atasan->level;
			// 	$m_history->created_by = $this->userdata('id_user');
			// 	$m_history->save();
			// }
			// /* end : menyimpan history surat */

			// /* start : mengirim realtime notif */
			// Firebase::send($this->userdata('id_user'), $firebase_receiver, $firebase_msg, 'surat', encText($m_surat->srt_id . 'surat', true));
			// /* end : mengirim realtime notif */

			$responseCode = 200;
			$responseMessage = 'Data berhasil disimpan';
			$responseData['surat'] = Surat::get_data(encText($m_surat->srt_id . 'surat', true));
			$responseData['jabatan'] = $get_jabatan;
			// $responseData['firebase_receiver'] = $firebase_receiver;
			$responseData['mode'] = ($suratIsNew == true) ? 'new' : 'revisi';
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function jsonArchive(Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$rules['id_surat'] = 'required';

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		} elseif (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$id_surat = $request->input('id_surat');

			$get_surat = Surat::get_data($id_surat, false);
			if (!empty($get_surat) && ($this->userdata('id_jabatan') == $get_surat->id_jabatan_pembuat) && !empty($get_surat->tanggal_approve) && ((($get_surat->state > 1) && ($this->userdata('id_jabatan') == $get_surat->id_jabatan))) && $get_surat->arsip == false) {

				Surat::where('srt_id', $get_surat->id_surat)->update(['srt_arsip' => true]);

				$stm_keterangan = $this->userdata('nama') . ' mengarsipkan surat';

				/* start : menyimpan timeline */
				$m_timeline = new SuratTimeline();

				$m_timeline->stm_srt_id = $get_surat->id_surat;
				$m_timeline->stm_keterangan = $stm_keterangan;
				$m_timeline->created_by = $this->userdata('id_user');
				$m_timeline->save();
				/* end : menyimpan timeline */

				$responseCode = 200;
				$responseMessage = 'Surat berhasil diarsipkan';
			} else {
				$responseCode = 400;
				$responseMessage = 'Data tidak tersedia!';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function destroy(Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$rules['id_surat'] = 'required';

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		} elseif (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$id_surat = $request->input('id_surat');

			$get_surat = Surat::get_data($id_surat, false);
			if (!empty($get_surat) && ($this->userdata('id_jabatan') == $get_surat->id_jabatan_pembuat) && empty($get_surat->tanggal_approve) && ((($get_surat->state == 1) && ($this->userdata('id_jabatan') == $get_surat->id_jabatan)) || ($get_surat->langkah < 3)) && $get_surat->arsip == false) {

				Surat::where('srt_id', $get_surat->id_surat)->update(['deleted_by' => $this->userdata('id_user')]);
				Surat::where('srt_id', $get_surat->id_surat)->delete();

				$get_user_route = SuratHistory::get_user_route($id_surat);

				if ($get_user_route->isNotEmpty()) {
					$firebase_receiver = [];

					foreach ($get_user_route as $key => $value) {
						/* start : set firebase receiver */
						if ($value->grade < 5) {
							$firebase_receiver[] = $value->id_user;
						}
						/* end : set firebase receiver */
					}

					/* start : mengirim realtime notif */
					if (!empty($firebase_receiver)) {
						$firebase_msg = $this->userdata('nama') . ' telah menghapus ' . $get_surat->judul . '!';
						Firebase::send($this->userdata('id_user'), $firebase_receiver, $firebase_msg, 'surat', '');
					}
					/* end : mengirim realtime notif */
				}

				$responseCode = 200;
				$responseMessage = 'Surat berhasil dihapus';
			} else {
				$responseCode = 400;
				$responseMessage = 'Data tidak tersedia!';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function quickStats(Request $request, Surat $m_surat)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		if (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$responseCode = 200;

			$filter_ttd = false;

			if ($this->userdata('role') == 2) {
				$filter_ttd = 1;
			} elseif ($this->userdata('role') == 3) {
				$filter_ttd = 2;
			}

			$id_jabatan = ($this->userdata('id_jabatan')) ? $this->userdata('id_jabatan') : false;

			$responseData['stats'] = Surat::get_stats($filter_ttd, $id_jabatan);
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function doRollback(Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$rules['id_surat'] = 'required';
		$rules['keterangan'] = 'required';

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
			$responseData['error_log'] = $validator->errors();
		} else {
			$id_surat = $request->input('id_surat');
			$srt_keterangan = $request->input('keterangan');

			$get_surat = Surat::get_data($id_surat, false);

			if ($this->userdata('role') == 1 && !empty($get_surat) && ($get_surat->state == 1) && $get_surat->id_jabatan_pembuat != $get_surat->id_jabatan) {
				$get_current_user = User::where('usr_jbt_id', $get_surat->id_jabatan)->first();

				$get_before = SuratHistory::where('srh_srt_id', $get_surat->id_surat)->where('srh_grade', '>', $get_current_user->usr_grade)->where('srh_jbt_id', '!=', $get_current_user->usr_jbt_id)->orderBy('created_at', 'DESC')->orderBy('srh_id', 'DESC')->whereNull('deleted_at')->first();

				$id_jabatan = $get_surat->id_jabatan;

				if (!empty($get_before)) {
					/* start : menentukan jabatan tujuan */
					$id_jabatan = $get_before->srh_jbt_id;
					/* end : menentukan jabatan tujuan */
				}

				/* start : menyimpan history surat */
				$m_history = new SuratHistory();
				$get_jabatan = Jabatan::get_data(encText($id_jabatan . 'jabatan', true), false);

				$m_history->srh_srt_id = $get_surat->id_surat;
				$m_history->srh_jbt_id = $id_jabatan;
				$m_history->srh_rollback = true;
				$m_history->srh_grade = $get_jabatan->level;
				$m_history->srh_keterangan = helpText($srt_keterangan);
				$m_history->srh_with_upload = false;
				$m_history->created_by = $this->userdata('id_user');
				$m_history->save();

				$m_history = SuratHistory::find($m_history->srh_id);
				$m_surat = Surat::find($get_surat->id_surat);

				$stm_keterangan = $this->userdata('nama') . ' mengembalikan surat';
				$firebase_msg = $this->userdata('nama') . ' mengembalikan ' . $get_surat->judul;

				$m_surat->srt_rollback = true;
				$m_surat->srt_keterangan = helpText($m_history->srh_keterangan);

				/* start : set firebase receiver */
				/* start : menentukan jabatan tujuan */
				$m_surat->srt_jbt_id = $m_history->srh_jbt_id;
				/* end : menentukan jabatan tujuan */

				$get_id_user = User::where('usr_jbt_id', $m_history->srh_jbt_id)->first();
				if (!empty($get_id_user)) {
					$firebase_receiver[] = $get_id_user->usr_id;
				}
				/* end : set firebase receiver */

				$m_surat->save();

				/* start : menyimpan timeline */
				$m_timeline = new SuratTimeline();

				$m_timeline->stm_srt_id = $m_surat->srt_id;
				$m_timeline->stm_keterangan = $stm_keterangan;
				$m_timeline->created_by = $this->userdata('id_user');
				$m_timeline->save();
				/* end : menyimpan timeline */

				/* start : mengirim realtime notif */
				Firebase::send($m_history->created_by = $this->userdata('id_user'), $firebase_receiver, $firebase_msg, 'surat', encText($get_surat->id_surat . 'surat', true));
				/* end : mengirim realtime notif */

				$responseCode = 200;
				$responseMessage = 'Surat berhasil di-rollback';
			} else {
				$responseCode = 400;
				$responseMessage = 'Data tidak ditemukan';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus, $responseNote);

		return response()->json($response, $responseCode);
	}

	public function cetak($id_surat, Request $request)
	{
		$get_surat = Surat::get_data($id_surat);
		if (!empty($get_surat)) {
			$data['title'] = $get_surat->judul;
			$data['menu_active'] = 'surat';
			$data['surat'] = $get_surat;
			$data['surat_ttd'] = SuratTandaTangan::get_data(false, $id_surat);
			$data['surat_qr'] = SuratQrCode::get_data(false, $id_surat);
			return view('surat/print', $data);
		} else {
			return $this->pageNotFound();
		}
	}

	public function json(Request $request)
	{
		$responseCode = 200;
		$responseStatus = 'OK';
		$responseMessage = 'Data tersedia';
		$responseData = [];

		if (!$request->ajax()) {
			return $this->accessForbidden();
		} else {

			$filter_ttd = false;

			if ($this->userdata('role') == 2) {
				$filter_ttd = 1;
			} elseif ($this->userdata('role') == 3) {
				$filter_ttd = 2;
			}

			$m_surat = new Surat();

			$numbcol = $request->get('order');
			$columns = $request->get('columns');

			$echo = $request->get('draw');
			$start = $request->get('start');
			$perpage = $request->get('length');

			$search = $request->get('search');
			$search = $search['value'];
			$pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
			$search = preg_replace($pattern, '', $search);

			$sort = $numbcol[0]['dir'];
			$field = $columns[$numbcol[0]['column']]['data'];

			$condition = ($request->get('aktif') ? $request->get('aktif') : false);

			$page = ($start / $perpage) + 1;

			$id_jabatan = ($this->userdata('id_jabatan')) ? $this->userdata('id_jabatan') : false;

			if ($page >= 0) {

				$total = $m_surat->json_grid($start, $perpage, $search, true, $sort, $field, $condition, $filter_ttd, $id_jabatan);
				$result = $m_surat->json_grid($start, $perpage, $search, false, $sort, $field, $condition, $filter_ttd, $id_jabatan);
			} else {
				$result = $m_surat::orderBy($field, $sort)->get();
				$total = $m_surat::all()->count();
			}

			$responseData = array("sEcho" => $echo, "iTotalRecords" => $total, "iTotalDisplayRecords" => $total, "aaData" => $result);

			return response()->json($responseData, $responseCode);
		}
	}

	public function jsonTimeline(Request $request)
	{
		$responseCode = 200;
		$responseStatus = 'OK';
		$responseMessage = 'Data tersedia';
		$responseData = [];

		$rules['id_surat'] = 'required';

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		} elseif (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$id_surat = $request->input('id_surat');

			$get_surat = Surat::get_data($id_surat);
			if (!empty($get_surat)) {
				$responseCode = 200;
				$responseData['timeline'] = SuratTimeline::get_data(false, $id_surat);
			} else {
				$responseCode = 400;
				$responseMessage = 'Data tidak tersedia!';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function uploadFile($id_surat, Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		$get_surat = Surat::get_data($id_surat, false);
		if (!empty($get_surat)) {
			$mode = $request->input('mode');
			$file = $request->file('file');
			if (!empty($file)) {
				$fake_filename = rand_str(15) . '.' . $file->getClientOriginalExtension();
				$filename = $file->getClientOriginalName();

				$destinationPath = myStorage('surat/' . $get_surat->id_surat . '/');

				helpCreateDirectory($destinationPath);

				$file->move($destinationPath, $fake_filename);

				if (file_exists($destinationPath . '/' . $fake_filename) && !is_dir($destinationPath . '/' . $fake_filename)) {
					// start : store surat
					if ($mode != 'advance') {
						$m_surat = Surat::find($get_surat->id_surat);
						$firebase_receiver = [];

						$id_jabatan = encText($this->userdata('id_jabatan') . 'jabatan', true);
						$get_jabatan = Jabatan::get_data($id_jabatan, false);

						$srh_rollback = false;

						if ($mode == 'revisi') {
							$m_surat->updated_by = $this->userdata('id_user');
							$m_surat->srt_rollback = false;

							$batch = $get_surat->batch;
							$batch++;
							$m_surat->srt_batch = $batch;

							$stm_keterangan = $this->userdata('nama') . ' melakukan perubahan pada surat';
							$firebase_msg = $this->userdata('nama') . ' telah melakukan perubahan pada ' . $m_surat->srt_judul . ' dan menunggu persetujuan Anda!';
						} elseif ($mode == 'new') {
							$m_surat->created_by = $this->userdata('id_user');
							$m_surat->srt_halaman = 0;
							$m_surat->srt_state = 1;
							$m_surat->srt_batch = 1;

							$m_surat->srt_jbt_id_start = $this->userdata('id_jabatan');
							$m_surat->srt_jbt_id = $this->userdata('id_jabatan');

							$stm_keterangan = $this->userdata('nama') . ' membuat surat';
							$firebase_msg = $this->userdata('nama') . ' membuat ' . $m_surat->srt_judul . ' dan menunggu persetujuan Anda!';
						}

						/* start : set firebase receiver */
						if (!empty($get_jabatan->id_atasan)) {
							/* start : menentukan jabatan tujuan */
							$m_surat->srt_jbt_id = $get_jabatan->id_atasan;
							/* end : menentukan jabatan tujuan */

							$get_id_user = User::where('usr_jbt_id', $get_jabatan->id_atasan)->first();
							if (!empty($get_id_user)) {
								$firebase_receiver[] = $get_id_user->usr_id;
							}
						}
						/* end : set firebase receiver */

						$m_surat->save();

						/* start : menyimpan timeline */
						$m_timeline = new SuratTimeline();

						$m_timeline->stm_srt_id = $m_surat->srt_id;
						$m_timeline->stm_keterangan = $stm_keterangan;
						$m_timeline->created_by = $this->userdata('id_user');
						$m_timeline->save();
						/* end : menyimpan timeline */

						/* start : menyimpan history surat */
						/* jika surat masih baru */
						if ($mode == 'new') {
							$m_history = new SuratHistory();

							$m_history->srh_srt_id = $m_surat->srt_id;
							$m_history->srh_jbt_id = $this->userdata('id_jabatan');
							$m_history->srh_rollback = $srh_rollback;
							$m_history->srh_grade = $this->userdata('grade');
							$m_history->created_by = $this->userdata('id_user');
							$m_history->save();
						}

						/* jika pembuat surat punya atasan */
						if (!empty($get_jabatan->id_atasan)) {
							$m_history = new SuratHistory();
							$get_atasan = Jabatan::get_data(encText($get_jabatan->id_atasan . 'jabatan', true), false);

							$m_history->srh_srt_id = $m_surat->srt_id;
							$m_history->srh_jbt_id = $get_atasan->id_jabatan;
							$m_history->srh_rollback = $srh_rollback;
							$m_history->srh_grade = $get_atasan->level;
							$m_history->created_by = $this->userdata('id_user');
							$m_history->save();
						}
						/* end : menyimpan history surat */

						/* start : mengirim realtime notif */
						Firebase::send($this->userdata('id_user'), $firebase_receiver, $firebase_msg, 'surat', encText($m_surat->srt_id . 'surat', true));
						/* end : mengirim realtime notif */
					}
					// end : store surat





					/* hapus file lama */
					if (!empty($get_surat->path_file)) {
						if (file_exists($destinationPath . $get_surat->path_file) && !is_dir($destinationPath . $get_surat->path_file)) {
							unlink($destinationPath . $get_surat->path_file);
						}
					}

					/* hapus png */
					if ($get_surat->halaman > 0) {
						for ($i = 1; $i <= $get_surat->halaman; $i++) {
							if (file_exists($destinationPath . 'page-' . $i . '.png') && !is_dir($destinationPath . 'page-' . $i . '.png')) {
								$oldFile = 'page-' . $i . '.png';
								unlink($destinationPath . 'page-' . $i . '.png');
							}
						}
					}

					/* start : open pdf */
					$pathPdf = myBasePath() . $destinationPath . $fake_filename;
					$pdf = new PdfToImage($pathPdf);

					$pages = $pdf->getNumberOfPages();

					$portrait = 0;
					$landscape = 0;

					for ($i = 1; $i <= $pages; $i++) {
						$pdf->setPage($i)->saveImage(myBasePath() . $destinationPath . 'page-' . $i . '.png');

						$orientation = 'default';
						$width = 0;
						$height = 0;

						$urlPage = $destinationPath . 'page-' . $i . '.png';

						list($width, $height) = getimagesize(myBasePath() . $urlPage);
						if ($width > $height) {
							$orientation = 'landscape';
							$landscape++;
						} else {
							$orientation = 'portrait';
							$portrait++;
						}

						SuratHalaman::updateOrCreate(['srt_id' => $get_surat->id_surat, 'halaman' => $i], ['orientation' => $orientation]);
					}

					SuratHalaman::where('srt_id', $get_surat->id_surat)->where('halaman', '>', $pages)->delete();
					/* end : open pdf */

					$m_surat = Surat::find($get_surat->id_surat);
					$m_surat->srt_halaman = $pages;
					$m_surat->srt_portrait = $portrait;
					$m_surat->srt_landscape = $landscape;
					$m_surat->srt_path_file = $fake_filename;
					$m_surat->save();

					// event(new SuratKeluarUploadPdf($m_surat));

					$responseCode = 200;
					$responseData['file'] = $m_surat;
					$responseData['pdf'] = $pathPdf;
					$responseData['mode'] = $mode;
				} else {
					$responseCode = 400;
					$responseStatus = 'Missing Param';
					$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';

					if ($mode == 'new') {
						Surat::where($get_surat->id_surat)->delete();
					}
				}
			} else {
				$responseCode = 400;
				$responseStatus = 'Missing Param';
				$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			}
		} else {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Data tidak tersedia';
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}


	public function uploadSuratSelesai($id_surat, Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		$get_surat = Surat::get_data($id_surat, false);
		if (!empty($get_surat)) {
			$mode = $request->input('mode');
			$file = $request->file('file');
			if (!empty($file)) {
				$fake_filename = rand_str(15) . '.' . $file->getClientOriginalExtension();
				$filename = $file->getClientOriginalName();

				$destinationPath = myStorage('surat_selesai/' . $get_surat->id_surat . '/');

				helpCreateDirectory($destinationPath);

				$file->move($destinationPath, $fake_filename);

				if (file_exists($destinationPath . '/' . $fake_filename) && !is_dir($destinationPath . '/' . $fake_filename)) {

					$pathPdf = myBasePath() . $destinationPath . $fake_filename;
					$m_surat = Surat::find($get_surat->id_surat);

					SuratSelesai::where('srs_srt_id', $get_surat->id_surat)->delete();
					$m_surat_selesai = new SuratSelesai;

					$m_surat_selesai->created_by = $this->userdata('id_user');
					$m_surat_selesai->srs_srt_id = $m_surat->srt_id;
					$m_surat_selesai->srs_path_file = $fake_filename;
					$m_surat_selesai->save();


					$responseCode = 200;
					$responseData['file'] = $m_surat;
					$responseData['pdf'] = $pathPdf;
				} else {
					$responseCode = 400;
					$responseStatus = 'Missing Param';
					$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
				}
			} else {
				$responseCode = 400;
				$responseStatus = 'Missing Param';
				$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			}
		} else {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Data tidak tersedia';
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function toPDF($id_surat, Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		// $rules['id_surat'] = 'required';

		// $validator = Validator::make($request->all(), $rules, $this->validationMessage());

		// if($validator->fails()){
		// 	$responseCode = 400;
		// 	$responseStatus = 'Missing Param';
		// 	$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
		// 	$responseData['error_log'] = $validator->errors();
		// }elseif (!$request->ajax()) {
		// 	return $this->accessForbidden();
		// }else{
		// $id_surat = $request->input('id_surat');

		$get_surat = Surat::get_data($id_surat);
		if (!empty($get_surat)) {
			$data['surat'] = $get_surat;
			$data['surat_ttd'] = SuratTandaTangan::get_data(false, $id_surat);

			return view('surat/to_pdf', $data);

			$htmlSample = View::make('export/to_pdf', $data);

			// /* instantiate and use the dompdf class */
			// $dompdf = new Dompdf();
			// $dompdf->loadHtml($htmlSample);
			// // $dompdf->loadView('dompdf', $data);

			// $dompdf->set_option('isRemoteEnabled', TRUE);
			// $dompdf->set_option('isHtml5ParserEnabled', true);
			// /* (Optional) Setup the paper size and orientation */
			// $dompdf->setPaper('A4', 'potrait');

			// /* Render the HTML as PDF */
			// $dompdf->render();

			// /* Output the generated PDF to Browser */
			// // $dompdf->stream();
			// file_put_contents(myBasePath() . myStorage('sample/surat/export.pdf', $dompdf->output()));
			// return $htmlSample;

			// $responseCode = 200;
		}
		// }

		// $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		// return response()->json($response, $responseCode);
	}

	// start : advance mode
	public function json_grid_history_rollback(Request $request, $id_surat)
	{
		$responseCode = 200;
		$responseStatus = 'OK';
		$responseMessage = 'Data tersedia';
		$responseData = [];

		if (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$m_history = new SuratHistory();

			$numbcol = $request->get('order');
			$columns = $request->get('columns');

			$echo = $request->get('draw');
			$start = $request->get('start');
			$perpage = $request->get('length');

			$search = $request->get('search');
			$search = $search['value'];
			$pattern = '/[^a-zA-Z0-9 !@#$%^&*\/\.\,\(\)-_:;?\+=]/u';
			$search = preg_replace($pattern, '', $search);

			$sort = $numbcol[0]['dir'];
			$field = $columns[$numbcol[0]['column']]['data'];

			$page = ($start / $perpage) + 1;

			if ($page >= 0) {
				$total = $m_history->json_grid_rollback($start, $perpage, $search, true, $sort, $field, $id_surat);
				$result = $m_history->json_grid_rollback($start, $perpage, $search, false, $sort, $field, $id_surat);
			} else {
				$result = $m_history::orderBy($field, $sort)->get();
				$total = $m_history::all()->count();
			}

			$responseData = array("sEcho" => $echo, "iTotalRecords" => $total, "iTotalDisplayRecords" => $total, "aaData" => $result);

			return response()->json($responseData, $responseCode);
		}
	}

	public function pageUploadFileHistory(Request $request, $id_history)
	{
		$data['title'] = 'Form Upload History';
		$data['menu_active'] = 'surat';
		$advance = ($this->userdata('role') == 1 && $request->input('code') == 'iuvadfb') ? 't' : 'f';

		$cek_history = SuratHistory::get_data($id_history);

		if ($cek_history && $cek_history->rollback == true && $advance == 't') {
			$data['surat'] = Surat::get_data($cek_history->id_surat);
			$data['history'] = $cek_history;
			return view('surat/form-history', $data);
		} else {
			return $this->pageNotFound();
		}
	}

	public function uploadFileRollback(Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$rules['id_history'] = 'required';
		$rules['page'] = 'required|array';
		$rules['page.*'] = 'required|integer';
		$rules['file'] = 'required|array';
		$rules['file.*'] = 'required|mimes:png';

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
			$responseData['error_log'] = $validator->errors();
		} else {
			$id_history = $request->input('id_history');

			$get_history = SuratHistory::get_data($id_history, false, false);
			if (!empty($get_history)) {
				$arr_page = $request->input('page');
				$arr_file = $request->file('file');

				$errorCounter = 0;
				$fileError = 0;
				for ($index = 0; $index < count($arr_page); $index++) {
					$page = $arr_page[$index];
					$file = $arr_file[$index];

					if (!empty($file)) {
						$fake_filename = 'page-' . $page . '.png';

						$destinationPath = myBasePath() . myStorage('surat/' . $get_history->id_surat . '/history/' . $get_history->id_history);

						helpCreateDirectory($destinationPath);

						$file->move($destinationPath, $fake_filename);

						// check if file is uploaded
						if (file_exists($destinationPath . '/' . $fake_filename) && !is_dir($destinationPath . '/' . $fake_filename)) {
							$m_history_file = new SuratHistoryFile;

							$cek_file = SuratHistoryFile::where('srhf_srh_id', $get_history->id_history)->where('srhf_page', $page)->first();
							if ($cek_file) {
								$m_history_file = SuratHistoryFile::find($cek_file->srhf_id);
							}

							$m_history_file->srhf_srh_id = $get_history->id_history;
							$m_history_file->srhf_page = $page;
							$m_history_file->srhf_path_file = $fake_filename;
							$m_history_file->save();
						} else {
							$errorCounter++;
							$fileError;
						}
					} else {
						$errorCounter++;
					}
				}

				if ($errorCounter > 0) {
					if ($fileError > 0) {
						$responseCode = 409;
						$responseMessage = 'File tidak berhasil diupload.';

						$destinationPath = myBasePath() . myStorage('surat/' . $get_history->id_surat . '/history/' . $get_history->id_history);

						$get_file = SuratHistoryFile::where('srhf_srh_id', $get_history->id_history)->get();

						foreach ($get_file as $key => $value) {
							if (file_exists($destinationPath . '/' . $value->srhf_path_file) && !is_dir($destinationPath . '/' . $value->srhf_path_file)) {
								unlink($destinationPath . '/' . $value->srhf_path_file);
							}
						}
					} else {
						$responseCode = 400;
						$responseStatus = 'Missing Param';
						$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
					}
				} else {
					$responseCode = 200;
					$responseMessage = 'File berhasil diupload';

					$destinationPath = myBasePath() . myStorage('surat/' . $get_history->id_surat . '/history/' . $get_history->id_history);

					$get_file = SuratHistoryFile::where('srhf_srh_id', $get_history->id_history)->whereNotIn('srhf_page', $arr_page)->get();

					foreach ($get_file as $key => $value) {
						if (file_exists($destinationPath . '/' . $value->srhf_path_file) && !is_dir($destinationPath . '/' . $value->srhf_path_file)) {
							unlink($destinationPath . '/' . $value->srhf_path_file);
						}
					}

					SuratHistoryFile::where('srhf_srh_id', $get_history->id_history)->whereNotIn('srhf_page', $arr_page)->forceDelete();
				}
			} else {
				$responseCode = 400;
				$responseStatus = 'Missing Param';
				$responseMessage = 'Data tidak tersedia';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function json_file_history($id_history, Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		if (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$cek_history = SuratHistory::get_data($id_history, false, false);
			if ($cek_history) {
				$get_surat = Surat::get_data(encText($cek_history->id_surat . 'surat', true), false);

				$get_file_history = SuratHistoryFile::get_data(false, $id_history);

				$file = [];
				foreach ($get_file_history as $key => $value) {
					$file[] = ['page' => $value->page, 'url' => base_url('watch/' . $get_surat->judul . '?un=' . $value->id_file . '&ct=history&src=page-' . $value->page . '.png')];
				}

				$responseCode = 200;
				$responseData['surat'] = $get_surat;
				$responseData['file'] = $file;
			} else {
				$responseCode = 400;
				$responseMessage = 'Data tidak tersedia!';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}
	// end : advance mode

	/* start : Tanda Tangan */
	public function getTtd($id_surat, Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$page_orientation = $request->input('orientation');

		$get_surat = Surat::get_data($id_surat);
		if (!empty($get_surat)) {
			$responseData['surat_ttd'] = SuratTandaTangan::get_data(false, $id_surat, false, true, $page_orientation);
			$responseCode = 200;
		} else {
			$responseCode = 400;
			$responseMessage = 'Data TIDAK tersedia.';
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function addTtd(Request $request, Surat $m_surat)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$rules['id_surat'] = 'required';
		$rules['warna'] = 'required';
		$rules['top'] = 'required';
		$rules['left'] = 'required';
		$rules['width'] = 'required';
		$rules['height'] = 'required';
		$rules['category'] = 'required';
		$rules['orientation'] = 'required';

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		} elseif (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$id_surat = $request->input('id_surat');
			$stt_top = $request->input('top');
			$stt_left = $request->input('left');
			$stt_width = $request->input('width');
			$stt_height = $request->input('height');
			$stt_warna = $request->input('warna');
			$category = $request->input('category');
			$stt_orientation = $request->input('orientation');

			$get_surat = Surat::get_data($id_surat, false);
			if (!empty($get_surat)) {
				// if($category == 'uptd' && $get_surat->id_ttd < 3){
				// 	$responseCode = 400;
				// 	$responseMessage = 'Data tidak tersedia!';
				// }else{
				// 	if($category == 'default' && $get_surat->id_ttd == 3){
				// 		$get_surat->id_ttd = 1;
				// 	}

				$m_surat_ttd = new SuratTandaTangan();

				$m_surat_ttd->stt_srt_id = $get_surat->id_surat;
				$m_surat_ttd->stt_top = ($stt_top + 10);
				$m_surat_ttd->stt_left = $stt_left;
				$m_surat_ttd->stt_width = $stt_width;
				$m_surat_ttd->stt_height = $stt_height;
				$m_surat_ttd->stt_warna = $stt_warna;
				$m_surat_ttd->stt_orientation = $stt_orientation;
				$m_surat_ttd->stt_ttd_id = $get_surat->id_ttd;
				$m_surat_ttd->created_by = $this->userdata('id_user');
				$m_surat_ttd->save();

				$responseCode = 200;
				$responseData['surat_ttd'] = SuratTandaTangan::get_data(encText($m_surat_ttd->stt_id . 'stt', true));
				$responseData['surat'] = $get_surat;
				// }
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function removeTtd(Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$rules['id_detail'] = 'required';

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		} elseif (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$id_detail = $request->input('id_detail');

			$get_stt = SuratTandaTangan::get_data($id_detail, false, false, false);
			if (!empty($get_stt)) {
				$m_surat_ttd = SuratTandaTangan::find($get_stt->id_detail)->delete();

				$responseCode = 200;
				$responseMessage = 'Tanda tangan BERHASIL dihapus!';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function setPosition(Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		$rules['id_ttd'] = 'required';
		$rules['id_surat'] = 'required';
		$rules['id_detail'] = 'required';

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
			$responseData['error_log'] = $validator->errors();
		} else {
			$id_detail = $request->input('id_detail');
			$id_surat = $request->input('id_surat');
			$id_ttd = $request->input('id_ttd');

			$stt_page = 1;
			$stt_left = $request->input('left');
			$stt_top = $request->input('top');
			$stt_width = $request->input('width');
			$stt_height = $request->input('height');

			$get_stt = SuratTandaTangan::get_data($id_detail, $id_surat, $id_ttd, false);
			if (!empty($get_stt)) {
				$m_stt = SuratTandaTangan::find($get_stt->id_detail);

				$m_stt->updated_by = $this->userdata('id_user');
				$m_stt->stt_srt_id = $get_stt->id_surat;
				$m_stt->stt_ttd_id = $get_stt->id_ttd;
				$m_stt->stt_page = $stt_page;
				$m_stt->stt_left = $stt_left;
				$m_stt->stt_top = $stt_top;
				$m_stt->stt_width = $stt_width;
				$m_stt->stt_height = $stt_height;
				$m_stt->save();

				$responseCode = 200;
			} else {
				$responseCode = 400;
				$responseMessage = 'Data TIDAK tersedia.';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}
	/* end : Tanda Tangan */

	/* start : Stempel */
	public function getStempel($id_surat, Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$page_orientation = $request->input('orientation');

		$get_surat = Surat::get_data($id_surat);
		if (!empty($get_surat)) {
			$responseData['surat_stempel'] = SuratStempel::get_data(false, $id_surat, false, true, $page_orientation);
			$responseCode = 200;
		} else {
			$responseCode = 400;
			$responseMessage = 'Data TIDAK tersedia.';
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function addStempel(Request $request, Surat $m_surat)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$rules['id_surat'] = 'required';
		$rules['id_stempel'] = 'required';
		$rules['top'] = 'required';
		$rules['left'] = 'required';
		$rules['width'] = 'required';
		$rules['height'] = 'required';
		$rules['orientation'] = 'required';

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		} elseif (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$id_surat = $request->input('id_surat');
			$id_stempel = $request->input('id_stempel');
			$sstp_top = $request->input('top');
			$sstp_left = $request->input('left');
			$sstp_width = $request->input('width');
			$sstp_height = $request->input('height');
			$sstp_orientation = $request->input('orientation');

			$get_surat = Surat::get_data($id_surat, false);
			if (!empty($get_surat)) {
				$m_surat_stempel = new SuratStempel();

				// $m_stempel = Stempel::where('stp_uptd', $this->userdata('uptd'))->first();

				$m_surat_stempel->sstp_srt_id = $get_surat->id_surat;
				$m_surat_stempel->sstp_top = ($sstp_top + 10);
				$m_surat_stempel->sstp_left = $sstp_left;
				$m_surat_stempel->sstp_width = $sstp_width;
				$m_surat_stempel->sstp_height = $sstp_height;
				$m_surat_stempel->sstp_orientation = $sstp_orientation;
				$m_surat_stempel->sstp_stp_id = $id_stempel;
				// $m_surat_stempel->sstp_stp_id = $m_stempel->stp_id;
				$m_surat_stempel->created_by = $this->userdata('id_user');
				$m_surat_stempel->save();

				$responseCode = 200;
				$responseData['surat_stempel'] = SuratStempel::get_data(encText($m_surat_stempel->sstp_id . 'sstp', true));
				$responseData['surat'] = $get_surat;
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function removeStempel(Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$rules['id_detail'] = 'required';

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		} elseif (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$id_detail = $request->input('id_detail');

			$get_sstp = SuratStempel::get_data($id_detail, false, false, false);
			if (!empty($get_sstp)) {
				$m_surat_stempel = SuratStempel::find($get_sstp->id_detail)->delete();

				$responseCode = 200;
				$responseMessage = 'Stempel BERHASIL dihapus!';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function setStempelPosition(Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		$rules['id_stempel'] = 'required';
		$rules['id_surat'] = 'required';
		$rules['id_detail'] = 'required';

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
			$responseData['error_log'] = $validator->errors();
		} else {
			$id_detail = $request->input('id_detail');
			$id_surat = $request->input('id_surat');
			$id_stempel = $request->input('id_stempel');

			$sstp_page = 1;
			$sstp_left = $request->input('left');
			$sstp_top = $request->input('top');
			$sstp_width = $request->input('width');
			$sstp_height = $request->input('height');

			$get_sstp = SuratStempel::get_data($id_detail, $id_surat, $id_stempel, false);
			if (!empty($get_sstp)) {
				$m_sstp = SuratStempel::find($get_sstp->id_detail);

				$m_sstp->updated_by = $this->userdata('id_user');
				$m_sstp->sstp_srt_id = $get_sstp->id_surat;
				$m_sstp->sstp_stp_id = $get_sstp->id_stempel;
				$m_sstp->sstp_left = $sstp_left;
				$m_sstp->sstp_top = $sstp_top;
				$m_sstp->sstp_width = $sstp_width;
				$m_sstp->sstp_height = $sstp_height;
				$m_sstp->save();

				$responseCode = 200;
			} else {
				$responseCode = 400;
				$responseMessage = 'Data TIDAK tersedia.';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}
	/* end : Stempel */


	/* start : QR Code */
	public function getQr($id_surat, Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$page_orientation = $request->input('orientation');

		$get_surat = Surat::get_data($id_surat);
		if (!empty($get_surat)) {
			$responseData['surat_qr'] = SuratQrCode::get_data(false, $id_surat, true, $page_orientation);
			$responseCode = 200;
		} else {
			$responseCode = 400;
			$responseMessage = 'Data TIDAK tersedia.';
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function addQr(Request $request, Surat $m_surat)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$rules['id_surat'] = 'required';
		$rules['top'] = 'required';
		$rules['left'] = 'required';
		$rules['orientation'] = 'required';

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		} elseif (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$id_surat = $request->input('id_surat');
			$sqr_top = $request->input('top');
			$sqr_left = $request->input('left');
			$sqr_orientation = $request->input('orientation');

			$get_surat = Surat::get_data($id_surat, false);
			if (!empty($get_surat)) {
				$m_surat_qr = new SuratQrCode();

				$m_surat_qr->sqr_srt_id = $get_surat->id_surat;
				$m_surat_qr->sqr_top = ($sqr_top + 10);
				$m_surat_qr->sqr_left = $sqr_left;
				$m_surat_qr->sqr_orientation = $sqr_orientation;
				$m_surat_qr->save();

				$responseCode = 200;
				$responseData['surat_qr'] = SuratQrCode::get_data(encText($m_surat_qr->sqr_id . 'qrcode', true));
				$responseData['surat'] = $get_surat;
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function removeQr(Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$rules['id_detail_qr'] = 'required';

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		} elseif (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$id_detail_qr = $request->input('id_detail_qr');

			$get_sqr = SuratQrCode::get_data($id_detail_qr, false, false);
			if (!empty($get_sqr)) {
				$m_surat_qr = SuratQrCode::find($get_sqr->id_detail_qr)->delete();

				$responseCode = 200;
				$responseMessage = 'Tanda tangan BERHASIL dihapus!';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function setQrPosition(Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		$rules['id_surat'] = 'required';
		$rules['id_detail_qr'] = 'required';

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
			$responseData['error_log'] = $validator->errors();
		} else {
			$id_detail_qr = $request->input('id_detail_qr');
			$id_surat = $request->input('id_surat');

			$sqr_left = $request->input('left');
			$sqr_top = $request->input('top');

			$get_sqr = SuratQrCode::get_data($id_detail_qr, $id_surat, false);
			if (!empty($get_sqr)) {
				$m_surat_qr = SuratQrCode::find($get_sqr->id_detail_qr);

				$m_surat_qr->updated_by = $this->userdata('id_user');
				$m_surat_qr->sqr_srt_id = $get_sqr->id_surat;
				$m_surat_qr->sqr_left = $sqr_left;
				$m_surat_qr->sqr_top = $sqr_top;
				$m_surat_qr->save();

				$responseCode = 200;
			} else {
				$responseCode = 400;
				$responseMessage = 'Data TIDAK tersedia.';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}
	/* end : QR Code */


	/* start : QR Penomoran */
	public function getPn($id_surat, Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$page_orientation = $request->input('orientation');

		$get_surat = Surat::get_data($id_surat);
		if (!empty($get_surat)) {
			$responseData['surat_pn'] = SuratPnCode::get_data(false, $id_surat, true, $page_orientation);
			$responseCode = 200;
		} else {
			$responseCode = 400;
			$responseMessage = 'Data TIDAK tersedia.';
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function addPn(Request $request, Surat $m_surat)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$rules['id_surat'] = 'required';
		$rules['top'] = 'required';
		$rules['left'] = 'required';
		$rules['orientation'] = 'required';

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		} elseif (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$id_surat = $request->input('id_surat');
			$spn_top = $request->input('top');
			$spn_left = $request->input('left');
			$spn_orientation = $request->input('orientation');
			// var_dump($get_surat->id_surat); die;
			$get_surat = Surat::get_data($id_surat, false);
			if (!empty($get_surat)) {
				$m_surat_pn = new SuratPnCode();

				$m_surat_pn->spn_srt_id = $get_surat->id_surat;
				$m_surat_pn->spn_top = ($spn_top + 10);
				$m_surat_pn->spn_left = $spn_left;
				$m_surat_pn->spn_orientation = $spn_orientation;
				$m_surat_pn->save();
				// var_dump($m_surat_pn->spn_id); die;

				#GENERATE IMAGE PENOMORAN
				$text = $get_surat->nomor_surat;
				$im = imagecreatetruecolor(110, 50);
				$white = imagecolorallocate($im, 255, 255, 255);
				$black = imagecolorallocate($im, 2, 2, 2);
				$bb = imagecolorallocate($im, 0, 0, 0);
				$fw = imagefontwidth(7);
				$l = strlen($text);
				$tw = $l * $fw;
				$iw = imagesx($im);
				$xpos = ($iw - $tw)/4;
				$ypos = 12;
				$font = MyHelper::myAssetPath();

				if($text !== '')
				{
					imagecolortransparent($im, $bb);
					imagettftext($im, 25, 0, $xpos, 35, $black, $font, $text);

					if (!file_exists('aefwg4/surat/'.$dataSurat->srt_id)) {
						mkdir('aefwg4/surat/'.$dataSurat->srt_id, 0755, true);
					}

					imagepng($im, 'aefwg4/surat/'.$get_surat->id_surat.'/kode-surat.png');
					imagedestroy($im);
				}
				#END GENERATE IMAGE PENOMORAN

				$responseCode = 200;
				$responseData['surat_pn'] = SuratPnCode::get_data(encText($m_surat_pn->spn_id . 'pncode', true));
				$responseData['surat'] = $get_surat;
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function removePn(Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$rules['id_detail_pn'] = 'required';

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		} elseif (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$id_detail_pn = $request->input('id_detail_pn');

			$get_spn = SuratPnCode::get_data($id_detail_pn, false, false);
			if (!empty($get_spn)) {
				$m_surat_pn = SuratPnCode::find($get_spn->id_detail_pn)->delete();

				$responseCode = 200;
				$responseMessage = 'Kode penomoran BERHASIL dihapus!';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function setPnPosition(Request $request)
	{	
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		$rules['id_surat'] = 'required';
		$rules['id_detail_pn'] = 'required';

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
			$responseData['error_log'] = $validator->errors();
		} else {
			$id_detail_pn = $request->input('id_detail_pn');
			$id_surat = $request->input('id_surat');

			$spn_page = 1;
			$spn_left = $request->input('left');
			$spn_top = $request->input('top');
			$spn_width = $request->input('width');
			$spn_height = $request->input('height');

			$get_spn = SuratPnCode::get_data($id_detail_pn, $id_surat, false);
			if (!empty($get_spn)) {
				$m_spn = SuratPnCode::find($get_spn->id_detail_pn);

				$m_spn->updated_by = $this->userdata('id_user');
				$m_spn->spn_srt_id = $get_spn->id_surat;
				$m_spn->spn_page = $spn_page;
				$m_spn->spn_left = $spn_left;
				$m_spn->spn_top = $spn_top;
				$m_spn->spn_width = $spn_width;
				$m_spn->spn_height = $spn_height;
				$m_spn->save();

				$responseCode = 200;
			} else {
				$responseCode = 400;
				$responseMessage = 'Data TIDAK tersedia.';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);

		// $responseCode = 403;
		// $responseStatus = '';
		// $responseMessage = '';
		// $responseData = [];

		// $rules['id_surat'] = 'required';
		// $rules['id_detail_pn'] = 'required';

		// $validator = Validator::make($request->all(), $rules);

		// if ($validator->fails()) {
		// 	$responseCode = 400;
		// 	$responseStatus = 'Missing Param';
		// 	$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
		// 	$responseData['error_log'] = $validator->errors();
		// } else {
		// 	$id_detail_pn = $request->input('id_detail_pn');
		// 	$id_surat = $request->input('id_surat');

		// 	$spn_left = $request->input('left');
		// 	$spn_top = $request->input('top');

		// 	$get_spn = SuratPnCode::get_data($id_detail_pn, $id_surat, false);
		// 	if (!empty($get_spn)) {
		// 		$m_surat_pn = SuratPnCode::find($get_spn->id_detail_pn);

		// 		$m_surat_pn->updated_by = $this->userdata('id_user');
		// 		$m_surat_pn->spn_srt_id = $get_spn->id_surat;
		// 		$m_surat_pn->spn_left = $spn_left;
		// 		$m_surat_pn->spn_top = $spn_top;
		// 		$m_surat_pn->save();

		// 		$responseCode = 200;
		// 	} else {
		// 		$responseCode = 400;
		// 		$responseMessage = 'Data TIDAK tersedia.';
		// 	}
		// }

		// $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		// return response()->json($response, $responseCode);
	}
	/* end : QR Penomoran */

	// public function show()
	// {
	// 	$data['title'] = 'Dokumen';
	// 	$id_stt = md5('1' . encText('stt_id'));
	// 	$id_surat = md5('1' . encText('srt_id'));
	// 	$id_tanda_tangan = md5('1' . encText('ttd_id'));
	// 	$get_surat = Surat::get_data($id_surat, false);

	// 	$data['surat_ttd'] = SuratTandaTangan::get_data($id_stt);
	// 	if (!empty($get_surat)) {
	// 		$data['ttd'] = TandaTangan::get_data($id_tanda_tangan, false);
	// 		$data['surat'] = $get_surat;
	// 		return view('sample.view-docs', $data);
	// 	} else {
	// 		echo 'Not Found';
	// 	}
	// }

	public function textToPng($text, $id_surat)
	{
		#GENERATE IMAGE PENOMORAN
		$im = imagecreatetruecolor(70, 50);
		$red = imagecolorallocate($im, 255, 0, 0);
		$bb = imagecolorallocate($im, 0, 0, 0);
		$bt = imagecolorallocate($im, 1, 0, 0);
		$fw = imagefontwidth(7);
		$l = strlen($text);
		$tw = $l * $fw;
		$iw = imagesx($im);
		$xpos = ($iw - $tw)/2;
		$ypos = 20;

		if($text !== '')
		{
			imagecolortransparent($im, $bb);
			imagestring($im, 5, $xpos, $ypos, $text, $bt);
			imagepng($im, 'aefwg4/surat/'+$id_surat+'/kode-surat.png');
			imagedestroy($im);
		}
	}
	
	public function textToImage()
	{
		#GENERATE IMAGE PENOMORAN
		// $im = imagecreatetruecolor(70, 50);
		// $red = imagecolorallocate($im, 255, 0, 0);
		// $bb = imagecolorallocate($im, 0, 0, 0);
		// $bt = imagecolorallocate($im, 1, 0, 0);
		// $fw = imagefontwidth(7);
		// $l = strlen('5094');
		// $tw = $l * $fw;
		// $iw = imagesx($im);
		// $xpos = ($iw - $tw)/2;
		// $ypos = 12;
		// $font = imageloadfont('aefwg4/arial.gdf');
		// imagecolortransparent($im, $bb);
		// imagestring($im, $font, $xpos, $ypos, '5094', $bt);
		// imagepng($im, 'aefwg4/kode-surat.png');
		// imagedestroy($im);

		#GENERATE IMAGE PENOMORAN
		$im = imagecreatetruecolor(110, 50);
		$white = imagecolorallocate($im, 255, 255, 255);
		$black = imagecolorallocate($im, 2, 2, 2);
		$bb = imagecolorallocate($im, 0, 0, 0);
		$fw = imagefontwidth(7);
		$l = strlen('12');
		$tw = $l * $fw;
		$iw = imagesx($im);
		$xpos = ($iw - $tw)/4;
		$ypos = 12;
		$text = '12';
		$font = MyHelper::myAssetPath().'\extends\font\ArialUnicodeMS.ttf';

		imagecolortransparent($im, $bb);
		imagettftext($im, 25, 0, $xpos, 35, $black, $font, $text);
		imagepng($im, 'aefwg4/kode-surat.png');
		imagedestroy($im);

	}

	public function toPNG()
	{
		$id_surat = md5('1' . encText('srt_id'));
		$get_surat = Surat::get_data($id_surat, false);

		if (!empty($get_surat)) {
			$pathPdf = myBasePath() . myStorage('sample/surat/' . $get_surat->path_file);
			$pdf = new PdfToImage($pathPdf);

			$pages = $pdf->getNumberOfPages();

			for ($i = 1; $i <= $pages; $i++) {
				$pdf->setPage($i)->saveImage(myBasePath() . myStorage('sample/surat/page-' . $i . '.png'));
			}

			$m_surat = Surat::find($get_surat->id_surat);
			$m_surat->srt_halaman = $pages;
			$m_surat->save();

			echo 'Extracted <strong>' . $pages . '</strong> page' . (($pages > 1) ? 's' : '');
		} else {
			echo 'Not Found';
		}
	}



	public function streamPDF(Request $request)
	{
		$data['content'] = $request->input('content');
		$htmlSample = View::make('export/to_pdf', $data);

		/* instantiate and use the dompdf class */
		$dompdf = new Dompdf();
		$dompdf->loadHtml($htmlSample);
		// $dompdf->loadView('dompdf', $data);

		$dompdf->set_option('isRemoteEnabled', TRUE);
		$dompdf->set_option('isHtml5ParserEnabled', true);
		/* (Optional) Setup the paper size and orientation */
		$dompdf->setPaper('A4', 'potrait');

		/* Render the HTML as PDF */
		$dompdf->render();

		/* Output the generated PDF to Browser */
		// $dompdf->stream();
		$dompdf->stream();
		return $htmlSample;
	}

	public function printPDF(Request $request)
	{
		$data['title'] = 'Dokumen';
		$id_stt = md5('1' . encText('stt_id'));
		$id_surat = md5('1' . encText('srt_id'));
		$id_tanda_tangan = md5('1' . encText('ttd_id'));
		$get_surat = Surat::get_data($id_surat, false);

		$data['surat_ttd'] = SuratTandaTangan::get_data($id_stt);
		if (!empty($get_surat)) {
			$data['ttd'] = TandaTangan::get_data($id_tanda_tangan, false);
			$data['surat'] = $get_surat;
			return view('export.print', $data);
		} else {
			echo 'Not Found';
		}
	}

	public function jsonPenomoranDetail($id_surat, Request $request) {
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		if (!$request->ajax()) {
			return $this->accessForbidden();
		} else {
			$get_surat = Surat::get_data($id_surat, false, false);
			if (!empty($get_surat)) {
				$responseData = $get_surat;
				$responseCode = 200;
			} else {
				$responseCode = 400;
				$responseMessage = 'Data tidak tersedia!';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function saveSuratPenomoran(Request $request, Surat $suratModel) {

		$encryptPrimary = encText('surat');
		$dataSurat = $suratModel->select('surat.*','pgw.*')
		->where(DB::raw("MD5(CONCAT(srt_id, '".$encryptPrimary."'))"), $request->input('id_surat'))
		->leftJoin(DB::raw('pegawai pgw'), 'pgw.pgw_id', '=', 'surat.srt_pgw_id')
		->first();

		$tanggalPenomoran = ($request->input('tipe') == 1) ? date('Y-m-d',strtotime($request->input('tanggal_penomoran'))) : date('Y-m-d');

		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$suratIsNew = true;

		$rules['id_surat'] = 'required';
		$rules['tipe'] = 'required';
		$rules['nomor_surat'] = 'required';


		if ($request->input('tipe') == 1) {
			$rules['tanggal_penomoran'] = 'required';
		}

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		} elseif (!$request->ajax()) {
			return $this->accessForbidden();
		} else {

			$suratModel->exists = true;
			$suratModel->srt_id = $dataSurat->srt_id;
			$suratModel->srt_nomor_surat = $request->input('nomor_surat');
			$suratModel->srt_tanggal_penomoran = $tanggalPenomoran;
			$suratModel->updated_at = date('Y-m-d H:i:s');
			
			$url = env('URL_DISHUB_PENOMORAN').'/api/external/number-in-use';

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer $2y$10$xcBY8fO3NY6vCWV/w3t3duoKKr5SKAEeAs/YIG3Lcdswzuhdvp2/i','Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, '{"number": "'.$request->input('nomor_surat').'","date": "'.$tanggalPenomoran.'","user_id": "'.$dataSurat->pgw_id.'","user_name": "'.$dataSurat->pgw_nama.'","surat_id": "'.$dataSurat->srt_id.'"}' );

			$response  = curl_exec($ch);
			curl_close($ch);

			$rawResponse = (array) json_decode($response);
			$rawStatus = (array) $rawResponse['status'];
			$responseCode = $rawStatus['code'];

			if($responseCode == 200) 
			{
				$suratModel->save();

				#GENERATE IMAGE PENOMORAN
				$text = $request->input('nomor_surat');
				$im = imagecreatetruecolor(110, 50);
				$white = imagecolorallocate($im, 255, 255, 255);
				$black = imagecolorallocate($im, 2, 2, 2);
				$bb = imagecolorallocate($im, 0, 0, 0);
				$fw = imagefontwidth(7);
				$l = strlen($text);
				$tw = $l * $fw;
				$iw = imagesx($im);
				$xpos = ($iw - $tw)/4;
				$ypos = 12;
				$font = MyHelper::myAssetPath();

				if($text !== '')
				{
					imagecolortransparent($im, $bb);
					imagettftext($im, 25, 0, $xpos, 35, $black, $font, $text);
					imagepng($im, 'aefwg4/surat/'.$dataSurat->srt_id.'/kode-surat.png');
					imagedestroy($im);
				}
				#END GENERATE IMAGE PENOMORAN

				$responseCode = 200;
				$responseMessage = 'Data berhasil disimpan';
				$responseData = $rawResponse['data'];

			} elseif($responseCode == 401) {
				$responseCode = 401;
				$responseMessage = $rawStatus['message'];
			} else {
				$responseCode = 400;
				$responseMessage = 'Data gagal disimpan';
			}

		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function getFile($id, $token)
    {
		$token_static = env('TOKEN_ESURAT');
		// dump('test');
		if ($token == $token_static){
			$get_surat = Surat::find($id);
			$destinationPath = myStorage('surat/' . $get_surat->srt_id . '/');
			$path = $destinationPath . '/' . $get_surat->srt_path_file;
			// return response()->download($path, $get_surat->srt_path_file);
			return response()->readfile($path, $get_surat->srt_path_file);
			// readfile($path, $get_surat->srt_path_file);
			// print file_get_contents($file);
		} else {
			$responseCode = 404;
			$responseMessage = 'Token Tidak Ditemukan';
			$responseData = [];
			$responseStatus = 'ERROR';
			return view('errors.404');
		}
    }

	// public function setNumberInUse($id) {
	// }
}
