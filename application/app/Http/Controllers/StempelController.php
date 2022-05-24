<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Models\Stempel;
use App\Http\Models\SysConfig;
use Image;

class StempelController extends Controller
{
	public function index()
	{
		$data['title'] = 'Stempel';
		$data['menu_active'] = 'stempel';
		$data['stempel'] = Stempel::get_data();
		return view('master/stempel/grid',$data);
	}

	public function show($id_stempel, Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		if(!$request->ajax()){
			return $this->accessForbidden();
		}else{
			$get_stempel = Stempel::get_data($id_stempel);

			if(!empty($get_stempel)){
				$responseCode = 200;
				$responseMessage = 'Data tersedia.';
				$responseData['stempel'] = $get_stempel;
			}else{
				$responseData['stempel'] = [];
				$responseStatus = 'No Data Available';
				$responseMessage = 'Data tidak tersedia';
			}

			$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
			return response()->json($response, $responseCode);
		}
	}

	public function store(Request $request, Stempel $m_stempel)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$stempelIsNew = true;
		
		$rules['action'] = 'required';

		$action = $request->input('action');

		if($action == 'edit'){
			$rules['id_stempel'] = 'required';
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
			$id_stempel = $request->input('id_stempel');

			if(!empty($id_stempel)){
				$get_stempel = Stempel::get_data($id_stempel, false);
				if(!empty($get_stempel)){
					$m_stempel = Stempel::find($get_stempel->id_stempel);
					$m_stempel->updated_by = $this->userdata('id_user');
				}
			}else{
				$m_stempel->created_by = $this->userdata('id_user');
			}

			$m_stempel->save();

			$destinationPath = myStorage('stempel/'.$m_stempel->stp_id.'/');
			helpCreateDirectory($destinationPath);

			$responseCode = 200;
			$responseMessage = 'Data berhasil disimpan';
			$responseData['stempel'] = Stempel::get_data(encText($m_stempel->stp_id.'stempel', true));
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function uploadFile($id_stempel, Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		$get_stempel = Stempel::get_data($id_stempel, false);
		if(!empty($get_stempel)){
			$file = $request->file('file');
			if(!empty($file)){
				$get_dimensi = SysConfig::get_data('thumbnail_stempel');
				$arr_dimensi = explode('#', $get_dimensi->value);

				$fake_filename = rand_str(15).'.'.$file->getClientOriginalExtension();
				$filename = $file->getClientOriginalName();

				$destinationPath = myStorage('stempel/'.$get_stempel->id_stempel.'/');

				helpCreateDirectory($destinationPath);

				$file->move($destinationPath, $fake_filename);

				/* hapus file lama */
				$path_file = $get_stempel->path_file;

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

				$m_stempel = Stempel::find($get_stempel->id_stempel);
				
				$m_stempel->stp_path_file = $fake_filename;

				$m_stempel->save();

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
