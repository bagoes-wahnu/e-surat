<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

use App\Http\Models\TandaTangan;
use App\Http\Models\Surat;
use Illuminate\Support\Facades\DB;


class PenomoranController extends Controller
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

    public function getLetterNumberToday(Request $request, Surat $suratModel) {
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
        $responseNote = [];

		$id_surat = ($request->id_surat !== NULL) ? $request->id_surat : '';

		$encryptPrimary = encText('surat');
		$dataSurat = $suratModel->select('surat.*','pgw.*')
		->where(DB::raw("MD5(CONCAT(srt_id, '".$encryptPrimary."'))"), $id_surat)
		->leftJoin(DB::raw('pegawai pgw'), 'pgw.pgw_id', '=', 'surat.srt_pgw_id')
		->first();
		$id_bidang = ($dataSurat->pgw_unit_id == NULL) ? "" : $dataSurat->pgw_unit_id;
		// dump($dataSurat);
        $url = env('URL_DISHUB_PENOMORAN').'/api/external/number-in-use/number-existing?sector_id='.$id_bidang;
		// dump($url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer $2y$10$xcBY8fO3NY6vCWV/w3t3duoKKr5SKAEeAs/YIG3Lcdswzuhdvp2/i','Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);

        $rawResponse = (array) json_decode($response);
		// dump($rawResponse);
        $rawStatus = (array) $rawResponse['status'];
        $responseCode = $rawStatus['code'];
        if($responseCode == 200) {
            $responseStatus = $rawStatus['message'];
            $responseData = $rawResponse['data'];
        }

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
    }

    public function getLetterNumberByDate(Request $request, Surat $suratModel) {
		$tanggal = ($request->tanggal) ? date('Y-m-d',strtotime($request->tanggal)) : '01-01-1000';
		$id_surat = ($request->id_surat !== NULL) ? $request->id_surat : '';
		$search = ($request->search !== NULL) ? $request->search : '';

		$encryptPrimary = encText('surat');
		$dataSurat = $suratModel->select('surat.*','pgw.*')
		->where(DB::raw("MD5(CONCAT(srt_id, '".$encryptPrimary."'))"), $id_surat)
		->leftJoin(DB::raw('pegawai pgw'), 'pgw.pgw_id', '=', 'surat.srt_pgw_id')
		->first();
		$id_bidang = ($dataSurat->pgw_unit_id == NULL) ? "" : $dataSurat->pgw_unit_id;
		// dump($id_bidang);
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
        $responseNote = [];

        $url = env('URL_DISHUB_PENOMORAN').'/api/external/number-in-use/number-existing-by-date';
		// dump($url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer $2y$10$xcBY8fO3NY6vCWV/w3t3duoKKr5SKAEeAs/YIG3Lcdswzuhdvp2/i','Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"search": "'.$search.'","date": "'.$tanggal.'", "sector_id": "'.$id_bidang.'"}' );
        $response  = curl_exec($ch);
        curl_close($ch);

        $rawResponse = (array) json_decode($response);
		// dump($rawResponse);
        $rawStatus = (array) $rawResponse['status'];
        $responseCode = $rawStatus['code'];
        if($responseCode == 200) {
            $responseStatus = $rawStatus['message'];
			$content = [];
			foreach ($rawResponse['data'] as $value) {
				foreach ($value->number as $value1) {
					$letterCode = (($value->letter_code == NULL || $value->letter_code == 'null') ? "(-) " : "(".$value->letter_code.") ").$value1;
					$content[] = ['id'=>(int)$value1,'name'=>$letterCode];
				}
			}
			$responseData = $content;
        } else {
			$responseCode = 200;
            $responseStatus = 'Data Kosong';
			$responseData = [];
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
    }

	public function getSector(Request $request) {
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];
        $responseNote = [];

        $url = env('URL_DISHUB_PENOMORAN').'/api/sector/select-list/?limit&search=&active_only=1';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer $2y$10$xcBY8fO3NY6vCWV/w3t3duoKKr5SKAEeAs/YIG3Lcdswzuhdvp2/i','Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);

        $rawResponse = (array) json_decode($response);
        $rawStatus = (array) $rawResponse['status'];
        $responseCode = $rawStatus['code'];
        if($responseCode == 200) {
            $responseStatus = $rawStatus['message'];
			$content = [];
			foreach ($rawResponse['data'] as $value) {
				$content[] = ['id'=>$value,'name'=>$value];
			}
			$responseData = $content;
        } else {
			$responseCode = 200;
            $responseStatus = 'Data Kosong';
			$responseData = [];
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
    }

}
