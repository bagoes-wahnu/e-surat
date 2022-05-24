<?php 
function base_url($value='')
{
	$base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
	$base_url .= "://".$_SERVER['HTTP_HOST'];
	$base_url .= preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])).'/'.$value;

	return $base_url;
}

function myBasePath($replace='', $to=''){
	$root = $_SERVER['DOCUMENT_ROOT'];
	$root .= preg_replace('@/+$@','',dirname($_SERVER['SCRIPT_NAME'])).'/';

	if(!empty($replace)){
		$root = str_replace($replace, $to, $root);
	}

	return $root;
}

function myStorage($url_path='')
{
	$dir = 'aefwg4/'.$url_path;

	return $dir;
}

function aset_tema($url_source='', $tema='metronic')
{
	$dir = base_url().'assets/main/'.$tema.'/'.$url_source;

	return $dir;
}

function aset_extends($url_source='')
{
	$dir = base_url().'assets/extends/'.$url_source;

	return $dir;
}

function zip_directory($path='', $zip_name='', $target_dir='')
{
	$zip_name = empty($zip_name)? date('YmdHis').'.zip' : $zip_name.'.zip';
	// if(file_exists($target_dir.$zip_name) and !is_dir($target_dir.$zip_name)){
	// 	unlink($target_dir.$zip_name);
	// 	echo 'a';
	// }
	// Get real path for our folder
	$rootPath = realpath($path);

	// Initialize archive object
	$zip = new ZipArchive();
	$zip->open($target_dir.$zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE);

	// Create recursive directory iterator
	/** @var SplFileInfo[] $files */
	$files = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($rootPath),
		RecursiveIteratorIterator::LEAVES_ONLY
	);

	foreach ($files as $name => $file)
	{
    // Skip directories (they would be added automatically)
		if (!$file->isDir())
		{
        // Get real and relative path for current file
			$filePath = $file->getRealPath();
			$relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
			$zip->addFile($filePath, $relativePath);
		}
	}

	// Zip archive will be created only after closing object
	$zip->close();
}

function helpCreateDirectory($directory='', $akses=0755){
	if(!empty($directory)){
		$explode_dir = explode('/', $directory);

		if(count($explode_dir) > 0){
			$temp_directory = '';
			for ($i=0; $i < count($explode_dir); $i++) {
				if(!empty($explode_dir[$i]) && $explode_dir[$i] != '.'){
					$temp_directory .= $explode_dir[$i];

					if(!is_dir($temp_directory)){
						mkdir($temp_directory, $akses);
					}
				}

				$temp_directory .= '/';
			}
		}else{
			if(!is_dir($directory)){
				mkdir($directory, $akses);
			}
		}
	}

	return true;
}

function protectPath($value='')
{
	$arr_forbidden = ['../', '..'];
	return str_replace($arr_forbidden, '', $value);
}
?>