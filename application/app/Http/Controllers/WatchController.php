<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Models\Surat;
use App\Http\Models\SuratHistory;
use App\Http\Models\SuratHistoryFile;
use App\Http\Models\SuratMasuk;
use App\Http\Models\TandaTangan;
use App\Http\Models\Stempel;
use Dompdf\Dompdf;

class WatchController extends Controller
{

	public function file_sample(Request $request)
	{
		$source		= $request->get("src");
		$category	= $request->get("ct");

		$image 		= ['.jpg', '.jpeg', '.png'];

		$file = myBasePath().myStorage();

		
		if(!empty($category) && !empty($source)){
			$file .= ('/sample/'.$category.'/'.$source);
		}

		$file = protectPath($file);

		if(file_exists($file) && !is_dir($file)){
			$type	= 'image';
			
			$ext = pathinfo($file, PATHINFO_EXTENSION);
			$ext = strtolower($ext);

			if(in_array(strtolower($ext),$image)){
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.basename($file));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				ob_clean();
				flush();
				readfile($file);
				exit;
			} else {
				header('Content-Type:' . finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $file ));
				header('Content-Length: ' . filesize($file));
				readfile($file);
			}
		} else {
			return $this->pageNotFound();
		}
	}

	public function showScan(Request $request)
	{
		$source		= $request->get("src");
		$unique_id	= $request->get("un");
		$category	= $request->get("ct");

		$image 		= ['.jpg', '.jpeg', '.png'];

		$file = myBasePath().myStorage();
		
		if(!empty($category) && !empty($source) && !empty($unique_id)){
			if($category == 'surat'){
				$get_surat = Surat::get_data($unique_id, false);

				if(!empty($get_surat)){
					$file .= ($category.'/'.$get_surat->id_surat.'/');

					$get_ext = explode('.', $source);
					$src_ext = $get_ext[(count($get_ext) - 1)];
					
					if(strtolower($src_ext) == 'pdf' && file_exists($file.'generated.pdf') && !is_dir($file.'generated.pdf')){
						$file .= 'generated.pdf';
					}else{
						$file .= $source;
					}
				}
			}
		}

		$file = protectPath($file);

		if(file_exists($file) && !is_dir($file)){
			$type	= 'image';
			
			$ext = pathinfo($file, PATHINFO_EXTENSION);
			$ext = strtolower($ext);

			if(in_array(strtolower($ext),$image)){
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.basename($file));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				ob_clean();
				flush();
				readfile($file);
				exit;
			} else {
				header('Content-Type:' . finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $file ));
				header('Content-Length: ' . filesize($file));
				readfile($file);
			}
		} else {
			return $this->pageNotFound();
		}
	}

	public function showFile($aliasName, Request $request)
	{
		$source		= $request->get("src");
		$unique_id	= $request->get("un");
		$category	= $request->get("ct");

		$image 		= ['.jpg', '.jpeg', '.png'];

		$file = myBasePath().myStorage();
		
		if(!empty($category) && !empty($source) && !empty($unique_id)){
			if($category == 'surat'){
				$aliasName = str_replace('/', '-', $aliasName);
				$get_surat = Surat::get_data($unique_id, false);

				if(!empty($get_surat)){
					$judul = str_replace('/', '-', $get_surat->judul);

					if($judul == $aliasName){
						$file .= ($category.'/'.$get_surat->id_surat.'/');

						$get_ext = explode('.', $source);
						$src_ext = $get_ext[(count($get_ext) - 1)];

						if(strtolower($src_ext) == 'pdf' && file_exists($file.'generated.pdf') && !is_dir($file.'generated.pdf')){
							$file .= 'generated.pdf';
						}else{
							$file .= $source;
						}
					}
				}
			}elseif($category == 'surat_selesai'){
				$aliasName = str_replace('/', '-', $aliasName);
				$get_surat = Surat::get_data($unique_id, false);

				if(!empty($get_surat)){
					$judul = str_replace('/', '-', $get_surat->judul);

					if($judul == $aliasName){
						$file .= ($category.'/'.$get_surat->id_surat.'/');

						$get_ext = explode('.', $source);
						$src_ext = $get_ext[(count($get_ext) - 1)];

						if(strtolower($src_ext) == 'pdf' && file_exists($file.'generated.pdf') && !is_dir($file.'generated.pdf')){
							$file .= 'generated.pdf';
						}else{
							$file .= $source;
						}
					}
				}
			}elseif($category == 'surat_masuk'){
				$get_surat = SuratMasuk::get_data($unique_id, false);

				if(!empty($get_surat) && $aliasName == str_replace('/', '-', $get_surat->judul)){
					$file .= ($category.'/'.$get_surat->id_surat_masuk.'/');
					$file .= $source;
				}
			}else if($category == 'qr'){
				$get_surat = Surat::get_data($unique_id, false);

				if(!empty($get_surat)){
					$file .= ('surat/'.$get_surat->id_surat.'/'.$source);
				}
			}else if($category == 'pn'){
				$get_surat = Surat::get_data($unique_id, false);

				if(!empty($get_surat)){
					$file .= ('surat/'.$get_surat->id_surat.'/'.$source);
				}
			}else if($category == 'tanda_tangan'){
				$get_ttd = TandaTangan::get_data($unique_id, false);

				if(!empty($get_ttd)){
					$file .= ($category.'/'.$get_ttd->id_ttd.'/'.$source);
				}
			}else if($category == 'stempel'){
				$get_stempel = Stempel::get_data($unique_id, false);

				if(!empty($get_stempel)){
					$file .= ($category.'/'.$get_stempel->id_stempel.'/'.$source);
				}
			}else if($category == 'history'){
				$cek_id = SuratHistoryFile::get_data($unique_id, false, false);

				if(!empty($cek_id)){
					$cek_history = SuratHistory::get_data(encText($cek_id->id_history.'surat_history', true), false, false);
					$cek_surat = Surat::get_data(encText($cek_history->id_surat.'surat', true), false, false);

					if(!empty($source) && !empty($category) && !empty($cek_surat) && (urldecode($aliasName) == $cek_surat->judul)){
						$file .= ('surat/'.$cek_surat->id_surat.'/'.$category.'/'.$cek_history->id_history.'/'.$source);
					}
				}
			}
		}

		$file = protectPath($file);

		if(file_exists($file) && !is_dir($file)){
			$type	= 'image';
			
			$ext = pathinfo($file, PATHINFO_EXTENSION);
			$ext = strtolower($ext);

			if(in_array(strtolower($ext),$image)){
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.basename($file));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				ob_clean();
				flush();
				readfile($file);
				exit;
			} elseif(finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $file ) == "application/pdf") {
				$fileExtension = substr($file, -3);
				if($fileExtension === "pdf"){
				  $filenameIndex = strpos($file, "pdf/") + 4;
				  $filename = basename($file);
		  
				}
				header('Content-Type: application/pdf');
				header('Content-length: '. filesize($file));
				header('X-Pad: avoid browser bug');
				header('Cache-Control: no-cache');
				header('Content-Transfer-Encoding: chunked');
				readfile($file);
				print file_get_contents($file);
				exit;
			} else {
				header('Content-Type:' . finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $file ).';base64,FileSerializedInBase64');
				header('Content-Length: ' . filesize($file));
				readfile($file);
			}
		} else {
			return $this->pageNotFound();
		}
	}

	public function default(Request $request)
	{
		$source		= $request->get("src");
		$category	= $request->get("type");

		$image 		= ['.jpg', '.jpeg', '.png'];

		$file		= 'assets/extends/';
		
		if(!empty($source) && !empty($category)){
			$file .= ($category.'/'.$source);
		}

		$file = protectPath($file);

		if(file_exists($file) && !is_dir($file)){
			$type	= 'image';
			
			$ext = pathinfo($file, PATHINFO_EXTENSION);
			$ext = strtolower($ext);

			if(in_array(strtolower($ext),$image)){
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.basename($file));
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));
				ob_clean();
				flush();
				readfile($file);
				exit;
			} else {
				header('Content-Type:' . finfo_file( finfo_open( FILEINFO_MIME_TYPE ), $file ));
				header('Content-Length: ' . filesize($file));
				readfile($file);
			}
		} else {
			return $this->pageNotFound();
		}
	}
}