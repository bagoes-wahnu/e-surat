<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\TandaTangan;
use App\Http\Models\Pegawai;
use App\Http\Models\SysConfig;
use Image;

class TandaTanganController extends Controller
{
	public function index()
	{
		$data['title'] = 'Tanda Tangan';
		$data['menu_active'] = 'tanda_tangan';
		$data['tanda_tangan'] = TandaTangan::get_data();
		$data['pegawai'] = Pegawai::get_data();
		return view('master/tanda_tangan/grid',$data);
	}

	public function show($id_tanda_tangan, Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		if(!$request->ajax()){
			return $this->accessForbidden();
		}else{
			$get_tanda_tangan = TandaTangan::get_data($id_tanda_tangan);

			if(!empty($get_tanda_tangan)){
				$responseCode = 200;
				$responseMessage = 'Data tersedia.';
				$responseData['tanda_tangan'] = $get_tanda_tangan;
			}else{
				$responseData['tanda_tangan'] = [];
				$responseStatus = 'No Data Available';
				$responseMessage = 'Data tidak tersedia';
			}

			$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
			return response()->json($response, $responseCode);
		}
	}

	public function store(Request $request, TandaTangan $m_tanda_tangan)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$tanda_tanganIsNew = true;
		
		// $rules['pejabat'] = 'required';
		$rules['action'] = 'required';

		$action = $request->input('action');

		if($action == 'edit'){
			$rules['id_tanda_tangan'] = 'required';
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
			$id_tanda_tangan = $request->input('id_tanda_tangan');
			$id_pegawai = $request->input('pejabat');

			// $get_pegawai = Pegawai::get_data($id_pegawai, false);
			// $ttd_pgw_id = $get_pegawai->id_pegawai;

			if(!empty($id_tanda_tangan)){
				$get_tanda_tangan = TandaTangan::get_data($id_tanda_tangan, false);
				if(!empty($get_tanda_tangan)){
					$m_tanda_tangan = TandaTangan::find($get_tanda_tangan->id_ttd);
					$m_tanda_tangan->updated_by = $this->userdata('id_user');
				}
			}else{
				$m_tanda_tangan->created_by = $this->userdata('id_user');
			}

			// $m_tanda_tangan->ttd_pgw_id = $ttd_pgw_id;
			$m_tanda_tangan->save();

			$destinationPath = myStorage('tanda_tangan/'.$m_tanda_tangan->ttd_id.'/');
			helpCreateDirectory($destinationPath);

			$responseCode = 200;
			$responseMessage = 'Data berhasil disimpan';
			$responseData['tanda_tangan'] = TandaTangan::get_data(encText($m_tanda_tangan->ttd_id.'ttd', true));
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function uploadFile($warna, $id_tanda_tangan, Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		$get_tanda_tangan = TandaTangan::get_data($id_tanda_tangan, false);
		if(!empty($get_tanda_tangan)){
			$file = $request->file('file');
			if(!empty($file)){
				$get_dimensi = SysConfig::get_data('thumbnail_ttd');
				$arr_dimensi = explode('#', $get_dimensi->value);

				$fake_filename = rand_str(15).'.'.$file->getClientOriginalExtension();
				$filename = $file->getClientOriginalName();

				$destinationPath = myStorage('tanda_tangan/'.$get_tanda_tangan->id_ttd.'/');

				helpCreateDirectory($destinationPath);

				$file->move($destinationPath, $fake_filename);

				/* hapus file lama */
				$path_file = NULL;

				switch ($warna) {
					case 'hitam':
					$path_file = $get_tanda_tangan->path_file_hitam;
					break;
					
					case 'merah':
					$path_file = $get_tanda_tangan->path_file_merah;
					break;
					
					case 'biru':
					$path_file = $get_tanda_tangan->path_file_biru;
					break;
					
					default:
					$path_file = NULL;
					break;
				}

				if(!empty($path_file)){
					if(file_exists($destinationPath.$path_file) && !is_dir($destinationPath.$path_file)){
						unlink($destinationPath.$path_file);
					}

					for ($i=0; $i < count($arr_dimensi) ; $i++) {
						$dimensi = explode( 'x', $arr_dimensi[$i]);
						$newWidth = $dimensi[0];
						$newHeight = $dimensi[1];

						$oldThumbnail = $destinationPath.'/thumbnail-'.$newWidth.'x'.$newHeight.'_'.$path_file;
						if(!empty($path_file) && file_exists($oldThumbnail) && !is_dir($oldThumbnail)){
							unlink($oldThumbnail);
						}
					}
				}

				/* start:create thumbnail */
				$arr_allowed_thumbnail = ['jpg', 'png', 'jpeg'];
				$fileExt = pathinfo($destinationPath.'/'.$fake_filename, PATHINFO_EXTENSION);

				if(in_array(strtolower($fileExt), $arr_allowed_thumbnail)){
					$thumbnailImage = Image::make($destinationPath.'/'.$fake_filename);

					$fileInfo = pathinfo($destinationPath.'/'.$fake_filename);

					for ($i=0; $i < count($arr_dimensi) ; $i++) {
						$dimensi = explode( 'x', $arr_dimensi[$i]);
						$newWidth = $dimensi[0];
						$newHeight = $dimensi[1];

						$newFilename = 'thumbnail-'.$newWidth.'x'.$newHeight.'_'.$fileInfo['filename'];
						$extension = $fileInfo['extension'];

						/* MEMBUAT CANVAS IMAGE SEBESAR DIMENSI YANG ADA DI DALAM ARRAY  */
						$canvas = Image::canvas($newWidth, $newHeight);
						/* RESIZE IMAGE SESUAI DIMENSI YANG ADA DIDALAM ARRAY  */
						/* DENGAN MEMPERTAHANKAN RATIO */
						$resizeImage  = Image::make($destinationPath.'/'.$fake_filename)->resize($newWidth, $newHeight, function($constraint) {
							$constraint->aspectRatio();
						});

						/* MEMASUKAN IMAGE YANG TELAH DIRESIZE KE DALAM CANVAS */
						$canvas->insert($resizeImage, 'center');
						/* SIMPAN IMAGE KE DALAM MASING-MASING FOLDER (DIMENSI) */
						$canvas->save($destinationPath.'/thumbnail-'.$newWidth.'x'.$newHeight.'_'.$fake_filename);
					}
				}
				/* end:create thumbnail */

				$m_tanda_tangan = TandaTangan::find($get_tanda_tangan->id_ttd);
				
				switch ($warna) {
					case 'hitam':
					$m_tanda_tangan->ttd_path_file_hitam = $fake_filename;
					break;
					
					case 'merah':
					$m_tanda_tangan->ttd_path_file_merah = $fake_filename;
					break;
					
					case 'biru':
					$m_tanda_tangan->ttd_path_file_biru = $fake_filename;
					break;
					
					default:
					$path_file = NULL;
					break;
				}

				$m_tanda_tangan->save();

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
}
