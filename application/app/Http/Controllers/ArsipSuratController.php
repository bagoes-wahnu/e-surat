<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Firebase;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

use App\Http\Models\TandaTangan;
use App\Http\Models\Stempel;
use App\Http\Models\Surat;
use App\Http\Models\SuratQrCode;
use App\Http\Models\SuratTandaTangan;
use App\Http\Models\SuratStempel;
use App\Http\Models\SuratTimeline;
use App\Http\Models\SuratHistory;
use App\Http\Models\SuratHistoryFile;
use App\Http\Models\SuratJenis;
use App\Http\Models\Jabatan;
use App\Http\Models\JabatanLevel;
use App\Http\Models\Pegawai;
use App\Http\Models\User;

use Spatie\PdfToImage\Pdf as PdfToImage;
use Dompdf\Dompdf;
use QrCode;

class ArsipSuratController extends Controller
{
	public function index()
	{
		$data['title'] = 'Arsip Surat Keluar';
		$data['menu_active'] = 'surat';
		$data['last_level'] = $this->lastGrade();
		$data['grade'] = $this->userdata('grade');
		$data['role'] = $this->userdata('role');
		return view('surat/arsip',$data);
	}

	// public function jsonShow($id_surat, Request $request)
	// {
	// 	$responseCode = 403;
	// 	$responseStatus = '';
	// 	$responseMessage = '';
	// 	$responseData = [];

	// 	if (!$request->ajax()) {
	// 		return $this->accessForbidden();
	// 	}else{
	// 		$get_surat = Surat::get_data($id_surat, false, false);
	// 		if(!empty($get_surat)){
	// 			$file = [];

	// 			if($get_surat->rollback == false){
	// 				$responseData['new'] = true;
	// 				for ($i=1; $i <= $get_surat->halaman ; $i++) { 
	// 					$file[] = ['page' => $i, 'url' => base_url('watch/'.$get_surat->judul.'?un='.$id_surat.'&ct=surat&src=page-'.$i.'.png')];
	// 				}
	// 			}else{
	// 				$responseData['new'] = false;
	// 				$get_last_history = SuratHistory::where('srh_srt_id', $get_surat->id_surat)->where('srh_rollback', true)->orderBy('srh_id', 'DESC')->first();

	// 				$get_file_history = SuratHistoryFile::get_data(false, encText($get_last_history->srh_id.'surat_history', true));

	// 				foreach ($get_file_history as $key => $value) {
	// 					$file[] = ['page' => $value->page, 'url' => base_url('watch/'.$get_surat->judul.'?un='.$value->id_file.'&ct=history&src=page-'.$value->page.'.png')];
	// 				}
	// 			}

	// 			$get_surat->id_surat = encText($get_surat->id_surat.'surat', true);

	// 			$responseData['surat'] = $get_surat;
	// 			$responseData['note_rollback'] = $file;

	// 			/* start : get_user_rollback */
	// 			$arr_user = [];
	// 			$arr_keterangan = [];

	// 			$get_user_rollback = SuratHistory::get_user_rollback($get_surat->id_surat, $get_surat->batch);

	// 			foreach ($get_user_rollback as $key => $value) {
	// 				if(!in_array($value->keterangan, $arr_keterangan)){
	// 					$arr_keterangan[] = $value->keterangan;
	// 					$arr_user[] = ['id_jabatan' => $value->id_jabatan, 'nama_jabatan' => $value->nama_jabatan];
	// 				}
	// 			}
	// 			$responseData['user_revisi'] = $arr_user;
	// 			/* end : get_user_rollback */

	// 			$responseCode = 200;
	// 		}else{
	// 			$responseCode = 400;
	// 			$responseMessage = 'Data tidak tersedia!';
	// 		}
	// 	}

	// 	$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
	// 	return response()->json($response, $responseCode);
	// }

	// public function show($id_surat, Request $request)
	// {
	// 	$get_surat = Surat::get_data($id_surat);
	// 	if(!empty($get_surat) && ($get_surat->state > 1) && !empty($get_surat->tanggal_approve) && ($this->userdata('id_jabatan') == $get_surat->id_jabatan) ){
	// 		$data['title'] = $get_surat->judul;
	// 		$data['menu_active'] = 'surat';
	// 		$data['surat'] = $get_surat;
	// 		$data['surat2'] = Surat::get_data($id_surat, false);
	// 		return view('surat/ttd', $data);
	// 	}else{
	// 		return $this->pageNotFound();
	// 	}
	// }

	// public function cetak($id_surat, Request $request)
	// {
	// 	$get_surat = Surat::get_data($id_surat);
	// 	if(!empty($get_surat)){
	// 		$data['title'] = $get_surat->judul;
	// 		$data['menu_active'] = 'surat';
	// 		$data['surat'] = $get_surat;
	// 		$data['surat_ttd'] = SuratTandaTangan::get_data(false, $id_surat);
	// 		$data['surat_qr'] = SuratQrCode::get_data(false, $id_surat);
	// 		return view('surat/print', $data);
	// 	}else{
	// 		return $this->pageNotFound();
	// 	}
	// }

	public function json(Request $request)
	{
		$responseCode = 200;
		$responseStatus = 'OK';
		$responseMessage = 'Data tersedia';
		$responseData = [];

		if(!$request->ajax()){
			return $this->accessForbidden();
		}else{

			$filter_ttd = false;

			if($this->userdata('role') == 2){
				$filter_ttd = 1;
			}elseif($this->userdata('role') == 3){
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
			
			$condition = ($request->get('aktif')? $request->get('aktif') : false);

			$page = ($start / $perpage) + 1;

			$id_jabatan = ($this->userdata('id_jabatan'))? $this->userdata('id_jabatan') : false;

			$arsip = 't';

			if($page >= 0){
				$total = $m_surat->json_grid($start, $perpage, $search, true, $sort, $field, $condition, $filter_ttd, $id_jabatan, $arsip);
				$result = $m_surat->json_grid($start, $perpage, $search, false, $sort, $field, $condition, $filter_ttd, $id_jabatan, $arsip);
			}else{
				$result = $m_surat::orderBy($field, $sort)->get();
				$total = $m_surat::all()->count();
			}

			$responseData = array("sEcho"=>$echo,"iTotalRecords"=>$total,"iTotalDisplayRecords"=>$total,"aaData"=>$result);
			
			return response()->json($responseData, $responseCode);
		}
	}

	// public function jsonTimeline(Request $request)
	// {
	// 	$responseCode = 200;
	// 	$responseStatus = 'OK';
	// 	$responseMessage = 'Data tersedia';
	// 	$responseData = [];

	// 	$rules['id_surat'] = 'required';

	// 	$validator = Validator::make($request->all(), $rules, $this->validationMessage());

	// 	if($validator->fails()){
	// 		$responseCode = 400;
	// 		$responseStatus = 'Missing Param';
	// 		$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
	// 		$responseData['error_log'] = $validator->errors();
	// 	}elseif(!$request->ajax()){
	// 		return $this->accessForbidden();
	// 	}else{
	// 		$id_surat = $request->input('id_surat');
			
	// 		$get_surat = Surat::get_data($id_surat);
	// 		if(!empty($get_surat)){
	// 			$responseCode = 200;
	// 			$responseData['timeline'] = SuratTimeline::get_data(false, $id_surat);
	// 		}else{
	// 			$responseCode = 400;
	// 			$responseMessage = 'Data tidak tersedia!';
	// 		}
	// 	}

	// 	$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
	// 	return response()->json($response, $responseCode);
	// }

	/* start : Tanda Tangan */
	// public function getTtd($id_surat, Request $request)
	// {
	// 	$responseCode = 403;
	// 	$responseStatus = '';
	// 	$responseMessage = '';
	// 	$responseData = [];
	// 	$responseNote = [];

	// 	$get_surat = Surat::get_data($id_surat);
	// 	if(!empty($get_surat)){
	// 		$responseData['surat_ttd'] = SuratTandaTangan::get_data(false, $id_surat, false);
	// 		$responseCode = 200;
	// 	}else{
	// 		$responseCode = 400;
	// 		$responseMessage = 'Data TIDAK tersedia.';
	// 	}

	// 	$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
	// 	return response()->json($response, $responseCode);
	// }

	// public function addTtd(Request $request, Surat $m_surat)
	// {
	// 	$responseCode = 403;
	// 	$responseStatus = '';
	// 	$responseMessage = '';
	// 	$responseData = [];
	// 	$responseNote = [];
		
	// 	$rules['id_surat'] = 'required';
	// 	$rules['warna'] = 'required';
	// 	$rules['top'] = 'required';
	// 	$rules['left'] = 'required';
	// 	$rules['width'] = 'required';
	// 	$rules['height'] = 'required';
	// 	$rules['category'] = 'required';

	// 	$validator = Validator::make($request->all(), $rules, $this->validationMessage());

	// 	if($validator->fails()){
	// 		$responseCode = 400;
	// 		$responseStatus = 'Missing Param';
	// 		$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
	// 		$responseData['error_log'] = $validator->errors();
	// 	}elseif (!$request->ajax()) {
	// 		return $this->accessForbidden();
	// 	}else{
	// 		$id_surat = $request->input('id_surat');
	// 		$stt_top = $request->input('top');
	// 		$stt_left = $request->input('left');
	// 		$stt_width = $request->input('width');
	// 		$stt_height = $request->input('height');
	// 		$stt_warna = $request->input('warna');
	// 		$category = $request->input('category');
			
	// 		$get_surat = Surat::get_data($id_surat, false);
	// 		if(!empty($get_surat)){
	// 			// if($category == 'uptd' && $get_surat->id_ttd < 3){
	// 			// 	$responseCode = 400;
	// 			// 	$responseMessage = 'Data tidak tersedia!';
	// 			// }else{
	// 			// 	if($category == 'default' && $get_surat->id_ttd == 3){
	// 			// 		$get_surat->id_ttd = 1;
	// 			// 	}

	// 				$m_surat_ttd = new SuratTandaTangan();

	// 				$m_surat_ttd->stt_srt_id = $get_surat->id_surat;
	// 				$m_surat_ttd->stt_top = ($stt_top + 10);
	// 				$m_surat_ttd->stt_left = $stt_left;
	// 				$m_surat_ttd->stt_width = $stt_width;
	// 				$m_surat_ttd->stt_height = $stt_height;
	// 				$m_surat_ttd->stt_warna = $stt_warna;
	// 				$m_surat_ttd->stt_ttd_id = $get_surat->id_ttd;
	// 				$m_surat_ttd->created_by = $this->userdata('id_user');
	// 				$m_surat_ttd->save();

	// 				$responseCode = 200;
	// 				$responseData['surat_ttd'] = SuratTandaTangan::get_data(encText($m_surat_ttd->stt_id.'stt', true));
	// 				$responseData['surat'] = $get_surat;
	// 			// }
	// 		}
	// 	}

	// 	$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
	// 	return response()->json($response, $responseCode);
	// }

	// public function removeTtd(Request $request)
	// {
	// 	$responseCode = 403;
	// 	$responseStatus = '';
	// 	$responseMessage = '';
	// 	$responseData = [];
	// 	$responseNote = [];
		
	// 	$rules['id_detail'] = 'required';

	// 	$validator = Validator::make($request->all(), $rules, $this->validationMessage());

	// 	if($validator->fails()){
	// 		$responseCode = 400;
	// 		$responseStatus = 'Missing Param';
	// 		$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
	// 		$responseData['error_log'] = $validator->errors();
	// 	}elseif (!$request->ajax()) {
	// 		return $this->accessForbidden();
	// 	}else{
	// 		$id_detail = $request->input('id_detail');
			
	// 		$get_stt = SuratTandaTangan::get_data($id_detail, false, false, false);
	// 		if(!empty($get_stt)){
	// 			$m_surat_ttd = SuratTandaTangan::find($get_stt->id_detail)->delete();

	// 			$responseCode = 200;
	// 			$responseMessage = 'Tanda tangan BERHASIL dihapus!';
	// 		}
	// 	}

	// 	$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
	// 	return response()->json($response, $responseCode);
	// }

	// public function setPosition(Request $request)
	// {
	// 	$responseCode = 403;
	// 	$responseStatus = '';
	// 	$responseMessage = '';
	// 	$responseData = [];

	// 	$rules['id_ttd'] = 'required';
	// 	$rules['id_surat'] = 'required';
	// 	$rules['id_detail'] = 'required';

	// 	$validator = Validator::make($request->all(), $rules);

	// 	if ($validator->fails()) {
	// 		$responseCode = 400;
	// 		$responseStatus = 'Missing Param';
	// 		$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
	// 		$responseData['error_log'] = $validator->errors();
	// 	} else {
	// 		$id_detail = $request->input('id_detail');
	// 		$id_surat = $request->input('id_surat');
	// 		$id_ttd = $request->input('id_ttd');

	// 		$stt_page = 1;
	// 		$stt_left = $request->input('left');
	// 		$stt_top = $request->input('top');
	// 		$stt_width = $request->input('width');
	// 		$stt_height = $request->input('height');

	// 		$get_stt = SuratTandaTangan::get_data($id_detail, $id_surat, $id_ttd, false);
	// 		if (!empty($get_stt)) {
	// 			$m_stt = SuratTandaTangan::find($get_stt->id_detail);

	// 			$m_stt->updated_by = $this->userdata('id_user');
	// 			$m_stt->stt_srt_id = $get_stt->id_surat;
	// 			$m_stt->stt_ttd_id = $get_stt->id_ttd;
	// 			$m_stt->stt_page = $stt_page;
	// 			$m_stt->stt_left = $stt_left;
	// 			$m_stt->stt_top = $stt_top;
	// 			$m_stt->stt_width = $stt_width;
	// 			$m_stt->stt_height = $stt_height;
	// 			$m_stt->save();

	// 			$responseCode = 200;
	// 		}else{
	// 			$responseCode = 400;
	// 			$responseMessage = 'Data TIDAK tersedia.';
	// 		}

	// 	}

	// 	$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
	// 	return response()->json($response, $responseCode);
	// }
	/* end : Tanda Tangan */

	/* start : Stempel */
	// public function getStempel($id_surat, Request $request)
	// {
	// 	$responseCode = 403;
	// 	$responseStatus = '';
	// 	$responseMessage = '';
	// 	$responseData = [];
	// 	$responseNote = [];

	// 	$get_surat = Surat::get_data($id_surat);
	// 	if(!empty($get_surat)){
	// 		$responseData['surat_stempel'] = SuratStempel::get_data(false, $id_surat, false);
	// 		$responseCode = 200;
	// 	}else{
	// 		$responseCode = 400;
	// 		$responseMessage = 'Data TIDAK tersedia.';
	// 	}

	// 	$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
	// 	return response()->json($response, $responseCode);
	// }

	// public function addStempel(Request $request, Surat $m_surat)
	// {
	// 	$responseCode = 403;
	// 	$responseStatus = '';
	// 	$responseMessage = '';
	// 	$responseData = [];
	// 	$responseNote = [];
		
	// 	$rules['id_surat'] = 'required';
	// 	$rules['top'] = 'required';
	// 	$rules['left'] = 'required';
	// 	$rules['width'] = 'required';
	// 	$rules['height'] = 'required';

	// 	$validator = Validator::make($request->all(), $rules, $this->validationMessage());

	// 	if($validator->fails()){
	// 		$responseCode = 400;
	// 		$responseStatus = 'Missing Param';
	// 		$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
	// 		$responseData['error_log'] = $validator->errors();
	// 	}elseif (!$request->ajax()) {
	// 		return $this->accessForbidden();
	// 	}else{
	// 		$id_surat = $request->input('id_surat');
	// 		$sstp_top = $request->input('top');
	// 		$sstp_left = $request->input('left');
	// 		$sstp_width = $request->input('width');
	// 		$sstp_height = $request->input('height');
			
	// 		$get_surat = Surat::get_data($id_surat, false);
	// 		if(!empty($get_surat)){
	// 			$m_surat_stempel = new SuratStempel();

	// 			$m_stempel = Stempel::where('stp_uptd', $this->userdata('uptd'))->first();

	// 			$m_surat_stempel->sstp_srt_id = $get_surat->id_surat;
	// 			$m_surat_stempel->sstp_top = ($sstp_top + 10);
	// 			$m_surat_stempel->sstp_left = $sstp_left;
	// 			$m_surat_stempel->sstp_width = $sstp_width;
	// 			$m_surat_stempel->sstp_height = $sstp_height;
	// 			$m_surat_stempel->sstp_stp_id = $m_stempel->stp_id;
	// 			$m_surat_stempel->created_by = $this->userdata('id_user');
	// 			$m_surat_stempel->save();

	// 			$responseCode = 200;
	// 			$responseData['surat_stempel'] = SuratStempel::get_data(encText($m_surat_stempel->sstp_id.'sstp', true));
	// 			$responseData['surat'] = $get_surat;
	// 		}
	// 	}

	// 	$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
	// 	return response()->json($response, $responseCode);
	// }

	// public function removeStempel(Request $request)
	// {
	// 	$responseCode = 403;
	// 	$responseStatus = '';
	// 	$responseMessage = '';
	// 	$responseData = [];
	// 	$responseNote = [];
		
	// 	$rules['id_detail'] = 'required';

	// 	$validator = Validator::make($request->all(), $rules, $this->validationMessage());

	// 	if($validator->fails()){
	// 		$responseCode = 400;
	// 		$responseStatus = 'Missing Param';
	// 		$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
	// 		$responseData['error_log'] = $validator->errors();
	// 	}elseif (!$request->ajax()) {
	// 		return $this->accessForbidden();
	// 	}else{
	// 		$id_detail = $request->input('id_detail');
			
	// 		$get_sstp = SuratStempel::get_data($id_detail, false, false, false);
	// 		if(!empty($get_sstp)){
	// 			$m_surat_stempel = SuratStempel::find($get_sstp->id_detail)->delete();

	// 			$responseCode = 200;
	// 			$responseMessage = 'Stempel BERHASIL dihapus!';
	// 		}
	// 	}

	// 	$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
	// 	return response()->json($response, $responseCode);
	// }

	// public function setStempelPosition(Request $request)
	// {
	// 	$responseCode = 403;
	// 	$responseStatus = '';
	// 	$responseMessage = '';
	// 	$responseData = [];

	// 	$rules['id_stempel'] = 'required';
	// 	$rules['id_surat'] = 'required';
	// 	$rules['id_detail'] = 'required';

	// 	$validator = Validator::make($request->all(), $rules);

	// 	if ($validator->fails()) {
	// 		$responseCode = 400;
	// 		$responseStatus = 'Missing Param';
	// 		$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
	// 		$responseData['error_log'] = $validator->errors();
	// 	} else {
	// 		$id_detail = $request->input('id_detail');
	// 		$id_surat = $request->input('id_surat');
	// 		$id_stempel = $request->input('id_stempel');

	// 		$sstp_page = 1;
	// 		$sstp_left = $request->input('left');
	// 		$sstp_top = $request->input('top');
	// 		$sstp_width = $request->input('width');
	// 		$sstp_height = $request->input('height');

	// 		$get_sstp = SuratStempel::get_data($id_detail, $id_surat, $id_stempel, false);
	// 		if (!empty($get_sstp)) {
	// 			$m_sstp = SuratStempel::find($get_sstp->id_detail);

	// 			$m_sstp->updated_by = $this->userdata('id_user');
	// 			$m_sstp->sstp_srt_id = $get_sstp->id_surat;
	// 			$m_sstp->sstp_stp_id = $get_sstp->id_stempel;
	// 			$m_sstp->sstp_left = $sstp_left;
	// 			$m_sstp->sstp_top = $sstp_top;
	// 			$m_sstp->sstp_width = $sstp_width;
	// 			$m_sstp->sstp_height = $sstp_height;
	// 			$m_sstp->save();

	// 			$responseCode = 200;
	// 		}else{
	// 			$responseCode = 400;
	// 			$responseMessage = 'Data TIDAK tersedia.';
	// 		}
	// 	}

	// 	$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
	// 	return response()->json($response, $responseCode);
	// }
	/* end : Stempel */


	/* start : QR Code */
	// public function getQr($id_surat, Request $request)
	// {
	// 	$responseCode = 403;
	// 	$responseStatus = '';
	// 	$responseMessage = '';
	// 	$responseData = [];
	// 	$responseNote = [];

	// 	$get_surat = Surat::get_data($id_surat);
	// 	if(!empty($get_surat)){
	// 		$responseData['surat_qr'] = SuratQrCode::get_data(false, $id_surat);
	// 		$responseCode = 200;
	// 	}else{
	// 		$responseCode = 400;
	// 		$responseMessage = 'Data TIDAK tersedia.';
	// 	}

	// 	$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
	// 	return response()->json($response, $responseCode);
	// }

	// public function addQr(Request $request, Surat $m_surat)
	// {
	// 	$responseCode = 403;
	// 	$responseStatus = '';
	// 	$responseMessage = '';
	// 	$responseData = [];
	// 	$responseNote = [];
		
	// 	$rules['id_surat'] = 'required';
	// 	$rules['top'] = 'required';
	// 	$rules['left'] = 'required';

	// 	$validator = Validator::make($request->all(), $rules, $this->validationMessage());

	// 	if($validator->fails()){
	// 		$responseCode = 400;
	// 		$responseStatus = 'Missing Param';
	// 		$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
	// 		$responseData['error_log'] = $validator->errors();
	// 	}elseif (!$request->ajax()) {
	// 		return $this->accessForbidden();
	// 	}else{
	// 		$id_surat = $request->input('id_surat');
	// 		$sqr_top = $request->input('top');
	// 		$sqr_left = $request->input('left');
			
	// 		$get_surat = Surat::get_data($id_surat, false);
	// 		if(!empty($get_surat)){
	// 			$m_surat_qr = new SuratQrCode();

	// 			$m_surat_qr->sqr_srt_id = $get_surat->id_surat;
	// 			$m_surat_qr->sqr_top = ($sqr_top + 10);
	// 			$m_surat_qr->sqr_left = $sqr_left;
	// 			$m_surat_qr->save();

	// 			$responseCode = 200;
	// 			$responseData['surat_qr'] = SuratQrCode::get_data(encText($m_surat_qr->sqr_id.'qrcode', true));
	// 			$responseData['surat'] = $get_surat;
	// 		}
	// 	}

	// 	$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
	// 	return response()->json($response, $responseCode);
	// }

	// public function removeQr(Request $request)
	// {
	// 	$responseCode = 403;
	// 	$responseStatus = '';
	// 	$responseMessage = '';
	// 	$responseData = [];
	// 	$responseNote = [];
		
	// 	$rules['id_detail_qr'] = 'required';

	// 	$validator = Validator::make($request->all(), $rules, $this->validationMessage());

	// 	if($validator->fails()){
	// 		$responseCode = 400;
	// 		$responseStatus = 'Missing Param';
	// 		$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
	// 		$responseData['error_log'] = $validator->errors();
	// 	}elseif (!$request->ajax()) {
	// 		return $this->accessForbidden();
	// 	}else{
	// 		$id_detail_qr = $request->input('id_detail_qr');
			
	// 		$get_sqr = SuratQrCode::get_data($id_detail_qr, false, false);
	// 		if(!empty($get_sqr)){
	// 			$m_surat_qr = SuratQrCode::find($get_sqr->id_detail_qr)->delete();

	// 			$responseCode = 200;
	// 			$responseMessage = 'Tanda tangan BERHASIL dihapus!';
	// 		}
	// 	}

	// 	$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
	// 	return response()->json($response, $responseCode);
	// }

	// public function setQrPosition(Request $request)
	// {
	// 	$responseCode = 403;
	// 	$responseStatus = '';
	// 	$responseMessage = '';
	// 	$responseData = [];

	// 	$rules['id_surat'] = 'required';
	// 	$rules['id_detail_qr'] = 'required';

	// 	$validator = Validator::make($request->all(), $rules);

	// 	if ($validator->fails()) {
	// 		$responseCode = 400;
	// 		$responseStatus = 'Missing Param';
	// 		$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
	// 		$responseData['error_log'] = $validator->errors();
	// 	} else {
	// 		$id_detail_qr = $request->input('id_detail_qr');
	// 		$id_surat = $request->input('id_surat');

	// 		$sqr_left = $request->input('left');
	// 		$sqr_top = $request->input('top');

	// 		$get_sqr = SuratQrCode::get_data($id_detail_qr, $id_surat, false);
	// 		if (!empty($get_sqr)) {
	// 			$m_surat_qr = SuratQrCode::find($get_sqr->id_detail_qr);

	// 			$m_surat_qr->updated_by = $this->userdata('id_user');
	// 			$m_surat_qr->sqr_srt_id = $get_sqr->id_surat;
	// 			$m_surat_qr->sqr_left = $sqr_left;
	// 			$m_surat_qr->sqr_top = $sqr_top;
	// 			$m_surat_qr->save();

	// 			$responseCode = 200;
	// 		}else{
	// 			$responseCode = 400;
	// 			$responseMessage = 'Data TIDAK tersedia.';
	// 		}

	// 	}

	// 	$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
	// 	return response()->json($response, $responseCode);
	// }
	/* end : QR Code */	
}