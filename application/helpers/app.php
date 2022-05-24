<?php
function app_info($key=''){
	$app_info = [
		'title' => 'Aplikasi E-Surat',
		'description' => 'Aplikasi E-Surat Dinas Perhubungan Kota Surabaya',
		'name' => 'Aplikasi E-Surat',
		'shortname' => 'E-Surat',
		'icon' => aset_extends('img/logo/favicon-esurat.png'),
		'client' => [
			'shortname' => 'Dishub',
			'fullname' => 'Dinas Perhubungan',
			'city' => 'Kota Surabaya',
			'category' => 'Government'
		],
		'copyright' => [
			'year' => '2019',
			'text' => '&copy; 2019 Dinas Perhubungan Kota Surabaya'
		],
		'vendor' => [
			'company' => 'Energeek The E-Government Solution',
			'office' => 'Jl Baratajaya 3/16, Surabaya, Jawa Timur',
			'contact' => [
				'phone' => '+62 856-3306-260',
				'email' => 'aditya.tanjung@energeek.co.id',
				'instagram' => 'https://www.instagram.com/energeek.co.id/'
			],
			'site' => 'http://energeek.co.id/'
		]
	];

	$error=0;
	if(empty($key)){
		$result = $app_info;
	}else{
		$result = false;
		$key = explode('.', $key);
		if(is_array($key)){
			$temp = $app_info;
			for ($i=0; $i < count($key); $i++) {
				$error++;
				if(is_array($temp) and count($temp) > 0){
					if(array_key_exists($key[$i], $temp)){
						$error--;
						$result = $temp[$key[$i]];
						$temp = $temp[$key[$i]];
					}
				}
			}
		}
	}

	if($error > 0){
		$result = false;
	}

	return $result;
}

function encText($value='', $md5=false)
{
	$result = $value.'-energeek';

	return (($md5 == false)? $result : md5($result));
}

?>