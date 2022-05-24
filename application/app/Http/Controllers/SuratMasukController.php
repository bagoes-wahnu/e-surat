<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Firebase;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

use App\Http\Models\SuratMasuk;
use App\Http\Models\SysConfig;

class SuratMasukController extends Controller
{
	public function index()
	{
		$data['title'] = 'Daftar Surat Masuk';
		$data['menu_active'] = 'surat_masuk';
		$data['last_level'] = $this->lastGrade();
		$data['grade'] = $this->userdata('grade');
		$data['role'] = $this->userdata('role');
		return view('surat_masuk/grid',$data);
	}

	public function jsonShow($id_surat_masuk, Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		if (!$request->ajax()) {
			return $this->accessForbidden();
		}else{
			$get_surat = SuratMasuk::get_data($id_surat_masuk);
			if(!empty($get_surat)){
				$responseData['surat_masuk'] = $get_surat;
				$responseCode = 200;
			}else{
				$responseCode = 400;
				$responseMessage = 'Data tidak tersedia!';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function jsonStore(Request $request, SuratMasuk $m_surat_masuk)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$suratIsNew = true;
		
		$rules['no_surat'] = 'required';
		$rules['judul'] = 'required';
		$rules['tanggal'] = 'required';
		$rules['pengirim'] = 'required';
		$rules['action'] = 'required';

		$action = $request->input('action');

		if($action == 'edit'){
			$rules['id_surat_masuk'] = 'required';
		}

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if($validator->fails()){
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		}elseif (!$request->ajax()) {
			return $this->accessForbidden();
		}else{
			$id_surat_masuk = $request->input('id_surat_masuk');
			$srm_no = $request->input('no_surat');
			$srm_judul = helpText($request->input('judul'));
			$srm_pengirim = $request->input('pengirim');
			$tanggal = $request->input('tanggal');

			$tanggal = explode('/', $tanggal);
			$srm_tanggal = $tanggal[2].'-'.$tanggal[1].'-'.$tanggal[0];

			$firebase_receiver = [];

			if(!empty($id_surat_masuk)){
				$get_surat = SuratMasuk::get_data($id_surat_masuk, false);
				if(!empty($get_surat)){
					$suratIsNew = false;
					$m_surat_masuk = SuratMasuk::find($get_surat->id_surat_masuk);
					$m_surat_masuk->updated_by = $this->userdata('id_user');
				}
			}else{
				$m_surat_masuk->created_by = $this->userdata('id_user');
			}

			$m_surat_masuk->srm_no = $srm_no;
			$m_surat_masuk->srm_judul = $srm_judul;
			$m_surat_masuk->srm_tanggal = $srm_tanggal;
			$m_surat_masuk->srm_pengirim = $srm_pengirim;
			$m_surat_masuk->save();

			$responseCode = 200;
			$responseMessage = 'Data berhasil disimpan';
			$responseData['surat_masuk'] = SuratMasuk::get_data(encText($m_surat_masuk->srm_id.'surat_masuk', true));
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function jsonGrid(Request $request)
	{
		$responseCode = 200;
		$responseStatus = 'OK';
		$responseMessage = 'Data tersedia';
		$responseData = [];

		if(!$request->ajax()){
			return $this->accessForbidden();
		}else{
			$m_surat_masuk = new SuratMasuk();

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

			$id_jabatan = ($this->userdata('id_jabatan')) ? $this->userdata('id_jabatan') : false;

			if($page >= 0){
				$total = $m_surat_masuk->json_grid($start, $perpage, $search, true, $sort, $field, $id_jabatan);
				$result = $m_surat_masuk->json_grid($start, $perpage, $search, false, $sort, $field, $id_jabatan);
			}else{
				$result = $m_surat_masuk::orderBy($field, $sort)->get();
				$total = $m_surat_masuk::all()->count();
			}

			$responseData = array("sEcho"=>$echo,"iTotalRecords"=>$total,"iTotalDisplayRecords"=>$total,"aaData"=>$result);
			
			return response()->json($responseData, $responseCode);
		}
	}

	public function uploadFile($id_surat_masuk, Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		$get_surat = SuratMasuk::get_data($id_surat_masuk, false);
		if(!empty($get_surat)){
			$file = $request->file('file');
			if(!empty($file)){
				$fake_filename = rand_str(15).'.'.$file->getClientOriginalExtension();
				$filename = $file->getClientOriginalName();

				$destinationPath = myStorage('surat_masuk/'.$get_surat->id_surat_masuk.'/');

				helpCreateDirectory($destinationPath);

				$file->move($destinationPath, $fake_filename);

				/* hapus file lama */
				if(!empty($get_surat->path_file)){
					if(file_exists($destinationPath.$get_surat->path_file) && !is_dir($destinationPath.$get_surat->path_file)){
						unlink($destinationPath.$get_surat->path_file);
					}

				}

				$m_surat_masuk = SuratMasuk::find($get_surat->id_surat_masuk);
				$m_surat_masuk->srm_path_file = $fake_filename;
				$m_surat_masuk->save();

				$responseCode = 200;
			}else{
				$responseCode = 400;
				$responseStatus = 'Missing Param';
				$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			}
		}else{
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Data tidak tersedia';
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function deleteFile(Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		$rules['id_surat_masuk'] = 'required';

		$action = $request->input('action');

		$validator = Validator::make($request->all(), $rules);

		if($validator->fails()){
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		}elseif (!$request->ajax()) {
			return $this->accessForbidden();
		}else{
			$responseCode = 400;
			$responseMessage = 'File Tidak Tersedia';

			$id_surat_masuk = $request->input('id_surat_masuk');

			$cek_surat_masuk = SuratMasuk::get_data($id_surat_masuk, false);

			if(!empty($cek_surat_masuk)){
				$m_surat_masuk = SuratMasuk::find($cek_surat_masuk->id_surat_masuk);

				$destinationPath = myStorage('surat_masuk/'.$cek_surat_masuk->id_surat_masuk.'/');

				$targetFile = $destinationPath.$cek_surat_masuk->path_file;

				if(file_exists($targetFile) && !is_dir($targetFile)){
					unlink($targetFile);
				}

				$get_dimensi = SysConfig::get_data('thumbnail_dimension');
				$arr_dimensi = explode('#', $get_dimensi->value);

				for ($i=0; $i < count($arr_dimensi) ; $i++) {
					$dimensi = explode('x', $arr_dimensi[$i]);
					$newWidth = $dimensi[0];
					$newHeight = $dimensi[1];

					$oldThumbnail = $destinationPath.'/thumbnail-'.$newWidth.'x'.$newHeight.'_'.$cek_surat_masuk->path_file;
					if(file_exists($oldThumbnail) && !is_dir($oldThumbnail)){
						unlink($oldThumbnail);
					}
				}

				$m_surat_masuk->srm_path_file = null;
				$m_surat_masuk->save();

				$responseCode = 200;
				$responseMessage = 'File berhasil dihapus';
			}
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}
}