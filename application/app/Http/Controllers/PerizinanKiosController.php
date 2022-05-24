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
use App\Http\Models\SuratTandaTangan;
use App\Http\Models\SuratStempel;
use App\Http\Models\SuratTimeline;
use App\Http\Models\SuratHistory;
use App\Http\Models\SuratHistoryFile;
use App\Http\Models\SuratJenis;
use App\Http\Models\Jabatan;
use App\Http\Models\JabatanLevel;
use App\Http\Models\Pegawai;
use App\Http\Models\SuratHalaman;
use App\Http\Models\User;
use App\Http\Models\TokenStatis;

use Spatie\PdfToImage\Pdf as PdfToImage;
use Dompdf\Dompdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PerizinanKiosController extends Controller
{
	public function checkToken($request)
	{
		$bearer = $request->header('authorization') ? $request->header('authorization') : $request->input('authorization');

        	if($bearer){
            		$token = explode(' ', $bearer);
            		$token = $token[1];
			$check_token = TokenStatis::where('token',$token)->first();
			//var_dump($check_token); die;
			if($check_token == NULL){
				return FALSE;
			} else {
				return $check_token;
			}

        	}else{
            		return FALSE;
        	}
	}

	public function store(Request $request, Surat $m_surat)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
		$responseNote = [];

		$statusToken = $this->checkToken($request);
                if($statusToken){

		$suratIsNew = true;

		$rules['pegawai'] = 'required';
		$rules['judul'] = 'required';
		//$rules['jenis_surat'] = 'required';
		$rules['tanggal'] = 'required';
		//$rules['pejabat'] = 'required';
		//$rules['action'] = 'required';

		$action = "add";

		if ($action == 'edit') {
			$rules['id_surat'] = 'required';
		}

		$validator = Validator::make($request->all(), $rules, $this->validationMessage());

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu!';
			$responseData['error_log'] = $validator->errors();
		//} elseif (!$request->ajax()) {
		//	return $this->accessForbidden();
		} else {
			$id_surat = $request->input('id_surat');
			$srt_pgw_id = helpText($request->input('pegawai'));
			$srt_judul = helpText($request->input('judul'));
			$srt_ttd_id = 1;
			$srt_srj_id = 14;
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
				}
			}
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

			$responseCode = 200;
			$responseMessage = 'Data berhasil disimpan';
			$responseData['surat'] = Surat::get_data(encText($m_surat->srt_id . 'surat', true));
			$responseData['jabatan'] = $get_jabatan;
			$responseData['mode'] = ($suratIsNew == true) ? 'new' : 'revisi';
		}
		}else{
			$responseCode = 400;
                        $responseMessage = 'Token Invalid';
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

		//var_dump($id_surat); die;
		$statusToken = $this->checkToken($request);
                if($statusToken){

		$get_surat = Surat::get_data($id_surat, false);
		//var_dump($get_surat); die;

		if (!empty($get_surat)) {
			$mode = 'new';
			$file = $request->file('file');
			//var_dump($file); die;
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
						$m_pegawai = Pegawai::find($m_surat->srt_pgw_id);
						$m_user = User::where('usr_jbt_id',$m_pegawai->pgw_jbt_id)->first();
						//var_dump($m_surat->pgw_jbt_id); die;
						$firebase_receiver = [];

						$id_jabatan = encText($m_pegawai->pgw_jbt_id . 'jabatan', true);
						$get_jabatan = Jabatan::get_data($id_jabatan, false);

						$srh_rollback = false;

						if ($mode == 'revisi') {
							$m_surat->updated_by =  $m_user->usr_id;
							$m_surat->srt_rollback = false;

							$batch = $get_surat->batch;
							$batch++;
							$m_surat->srt_batch = $batch;

							$stm_keterangan =  $m_user->usr_username . ' melakukan perubahan pada surat';
							$firebase_msg =  $m_user->usr_username . ' telah melakukan perubahan pada ' . $m_surat->srt_judul . ' dan menunggu persetujuan Anda!';
						} elseif ($mode == 'new') {
							$m_surat->created_by =  $m_user->usr_id;
							$m_surat->srt_halaman = 0;
							$m_surat->srt_state = 1;
							$m_surat->srt_batch = 1;

							$m_surat->srt_jbt_id_start = $m_pegawai->pgw_jbt_id;
							$m_surat->srt_jbt_id =$m_pegawai->pgw_jbt_id;

							$stm_keterangan =  $m_user->usr_username . ' membuat surat';
							$firebase_msg =  $m_user->usr_username . ' membuat ' . $m_surat->srt_judul . ' dan menunggu persetujuan Anda!';
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
						$m_timeline->created_by =  $m_user->usr_id;
						$m_timeline->save();
						/* end : menyimpan timeline */

						/* start : menyimpan history surat */
						/* jika surat masih baru */
						if ($mode == 'new') {
							$m_history = new SuratHistory();

							$m_history->srh_srt_id = $m_surat->srt_id;
							$m_history->srh_jbt_id = $m_pegawai->pgw_jbt_id;
							$m_history->srh_rollback = $srh_rollback;
							$m_history->srh_grade =  $m_user->usr_grade;
							$m_history->created_by = $m_user->usr_id;
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
							$m_history->created_by =  $m_user->usr_id;
							$m_history->save();
						}
						/* end : menyimpan history surat */

						/* start : mengirim realtime notif */
						Firebase::send( $m_user->usr_id, $firebase_receiver, $firebase_msg, 'surat', encText($m_surat->srt_id . 'surat', true));
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
		} else {
                        $responseCode = 400;
                        $responseMessage = 'Token Tidak Valid';
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}

	public function getStatus($id,Request $request){
		$responseCode = 403;
                $responseStatus = '';
                $responseMessage = '';
                $responseData = [];
		
		$statusToken = $this->checkToken($request);
                if($statusToken){
			$status = 'Menunggu';
			$data = Surat::get_data($id, false, false);
			if($data == NULL){
				$status = 'Menunggu';
			} else {
				$data_array = (array)$data;
				if($data_array['tanggal_approve'] == NULL)
				$status = 'Menunggu';
				else
				$status = 'Approved';
			}
		
			$responseCode = 200;
			$responseStatus = 'OK';
			$responseMessage = 'Sukses';
			$responseData = ['status' => $status];
		} else {
			$responseCode = 400;
                        $responseMessage = 'Token Tidak Valid';
		}


		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
                return response()->json($response, $responseCode);
	
	}

	public function listPegawai(Request $request){
		$responseCode = 403;
                $responseStatus = '';
                $responseMessage = '';
                $responseData = [];
		
		$statusToken = $this->checkToken($request);
                if($statusToken){

			$data = Pegawai::where('pgw_jbt_id',316)->get();
			$pegawai = [];
			foreach($data as $index => $val){
				$pegawai[] = [
						'id' => $val['pgw_id'],
						'nama' => $val['pgw_nama']
					     ];
			}
			//var_dump($pegawai); die;
			$responseCode = 200;
                	$responseStatus = 'OK';
                	$responseMessage = 'Sukses';
                	$responseData = $pegawai;
	

                } else {
                        $responseCode = 400;
                        $responseMessage = 'Token Tidak Valid';
                }

                $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
                return response()->json($response, $responseCode);

	}

	public function detailPegawai($id, Request $request){
		$responseCode = 403;
                $responseStatus = '';
                $responseMessage = '';
                $responseData = [];

                $statusToken = $this->checkToken($request);
                if($statusToken){

                        $data = Pegawai::where('pgw_id',$id)->first();
                        $responseCode = 200;
                        $responseStatus = 'OK';
                        $responseMessage = 'Sukses';
                        $responseData = $data;


                } else {
                        $responseCode = 400;
                        $responseMessage = 'Token Tidak Valid';
                }

                $response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
                return response()->json($response, $responseCode);

	}
}

