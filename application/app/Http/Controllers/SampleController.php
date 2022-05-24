<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

use App\Http\Models\TandaTangan;
use App\Http\Models\Surat;
use App\Http\Models\SuratTandaTangan;

use Spatie\PdfToImage\Pdf as PdfToImage;
use Dompdf\Dompdf;

class SampleController extends Controller
{
	public function index()
	{
		return view('sample.grid');
	}

	public function show()
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
			return view('sample.view-docs', $data);
		} else {
			echo 'Not Found';
		}
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

	public function toPDF(Request $request)
	{
		// return view('export/to_pdf');
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
		file_put_contents(myBasePath() . myStorage('sample/surat/export.pdf'), $dompdf->output());
		return $htmlSample;
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

	public function setPosition(Request $request)
	{
		$responseCode = 403;
		$responseStatus = '';
		$responseMessage = '';
		$responseData = [];

		$rules['id_ttd'] = 'required';
		$rules['id_surat'] = 'required';
		$rules['page'] = 'required';

		$validator = Validator::make($request->all(), $rules);

		if ($validator->fails()) {
			$responseCode = 400;
			$responseStatus = 'Missing Param';
			$responseMessage = 'Silahkan isi form dengan benar terlebih dahulu';
			$responseData['error_log'] = $validator->errors();
		} else {
			$m_stt = new SuratTandaTangan();

			$stt_id = $request->input('id_stt');
			$stt_srt_id = $request->input('id_surat');
			$stt_ttd_id = $request->input('id_ttd');
			$stt_page = $request->input('page');
			$stt_left = $request->input('left');
			$stt_top = $request->input('top');

			$id_stt = md5($stt_id . encText('stt_id'));
			$id_surat = md5($stt_srt_id . encText('srt_id'));
			$id_tanda_tangan = md5($stt_ttd_id . encText('ttd_id'));

			if (!empty($id_stt)) {
				$get_stt = SuratTandaTangan::get_data($id_stt, false, false, false);

				if (!empty($get_stt)) {
					$m_stt = SuratTandaTangan::find($get_stt->id_detail);
					// $m_stt->updated_by = $this->userdata('id_user');
				} else {
					// $m_stt->created_by = $this->userdata('id_user');
				}
			} else {
				// $m_stt->created_by = $this->userdata('id_user');
			}

			$m_stt->stt_srt_id = $stt_srt_id;
			$m_stt->stt_ttd_id = $stt_ttd_id;
			$m_stt->stt_page = $stt_page;
			$m_stt->stt_left = $stt_left;
			$m_stt->stt_top = $stt_top;
			$m_stt->save();

			$responseCode = 200;
		}

		$response = helpResponse($responseCode, $responseData, $responseMessage, $responseStatus);
		return response()->json($response, $responseCode);
	}
}
