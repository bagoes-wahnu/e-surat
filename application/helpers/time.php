<?php
/**
 * Function helpIndoDay
 * Fungsi ini digunakan untuk mencari nama hari dalam bahasa Indonesia
 * @access public
 * @param (int) $var Nomor urut hari yang dimulai dari angka 0 untuk hari minggu
 * @return (string) {'Undefined'}
 */
function helpIndoDay($var)
{
	$dayArray = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
	if(array_key_exists($var, $dayArray )){
		return $dayArray[$var];
	}else{
		return 'Undefined';
	}
}

/**
 * Function helpIndoMonth
 * Fungsi ini digunakan untuk mencari nama bulan dalam bahasa Indonesia
 * @access public
 * @param (int) $var Nomor urut bulan yang dimulai dari angka 0 untuk bulan januari
 * @return (string) {'Undefined'}
 */
function helpIndoMonth($num)
{
	$monthArray = array("Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
	if(array_key_exists($num, $monthArray)){
		return $monthArray[$num];
	}else{
		return 'Undefined';
	}
}

function helpReverseMonth($param, $mode='indo')
{
	if($mode == 'indo'){
		$monthArray = array("januari" => 1, "februari" => 2, "maret" => 3, "april" => 4, "mei" => 5, "juni" => 6, "juli" => 7, "agustus" => 8, "september" => 9, "oktober" => 10, "november" => 11, "desember" => 12);
	}else{
		$monthArray = [];
	}

	if(array_key_exists($param, $monthArray)){
		return $monthArray[$param];
	}else{
		return 'Undefined';
	}
}

/**
* Function helpDate
* Fungsi ini digunakan untuk melakukan konversi format tanggal
* @access public
* @param (date) $var Tanggal yang akan dikonversi
* @param (string) $mode Kode untuk model format yang baru
- se (short English)		: (Y-m-d) 2015-31-01
- si (short Indonesia)	: (d-m-Y) 31-01-2015
- me (medium English)	: (F d, Y) January 31, 2015
- mi (medium Indonesia)	: (d F Y) 31 Januari 2015
- le (long English)		: (l F d, Y) Sunday January 31, 2015
- li (long Indonesia)	: (l, d F Y) Senin, 31 Januari 2015
* @return (string) {'Undefined'}
*/
function helpDate($var, $mode = 'se')
{
	switch($mode){
		case 'se':
		return date('Y-m-d', strtotime($var));
		break;
		case 'si':
		return date('d-m-Y', strtotime($var));
		break;
		case 'me':
		return date('F d, Y', strtotime($var));
		break;
		case 'mi':
		$day = date('d', strtotime($var));
		$month = date('n', strtotime($var));
		$year = date('Y', strtotime($var));

		$month = helpIndoMonth($month - 1);
		return $day.' '.$month.' '.$year;
		break;
		case 'le':
		return date('l F d, Y', strtotime($var));
		break;
		case 'li':
		$dow = date('w', strtotime($var));
		$day = date('d', strtotime($var));
		$month = date('n', strtotime($var));
		$year = date('Y', strtotime($var));

		$hari = helpIndoDay($dow);
		$month = helpIndoMonth($month - 1);
		return $hari .', '. $day.' '.$month.' '.$year;
		break;
		case 'bi':
		$month = date('n', strtotime($var));
		$year = date('Y', strtotime($var));

		$month = helpIndoMonth($month - 1);
		return $month.' '.$year;
		break;
		default:
		return 'Undefined';
		break;
	}
}

function helpDateToWeek($date)
{
	$minggu = '';
	$new_date = date('d', strtotime($date));
	if($new_date >= 1 and $new_date <= 7){
		$minggu = '1';
	}elseif ($new_date <= 14) {
		$minggu = '2';
	}elseif ($new_date <= 21) {
		$minggu = '3';
	}else {
		$minggu = '4';
	}

	return $minggu;
}

function helpDateValidation($date, $format = 'Y-m-d')
{
	$d = DateTime::createFromFormat($format, $date);
	return $d && $d->format($format) == $date;
}

function helpTanggalTeks($tgl_mulai='', $tgl_selesai='', $hari=false, $separator=' - ')
{
	$hari_mulai = date('w', strtotime($tgl_mulai));
	$tanggal_mulai = date('j', strtotime($tgl_mulai));
	$bulan_mulai = date('Y-m', strtotime($tgl_mulai));
	$tahun_mulai = date('Y', strtotime($tgl_mulai));

	$hari_pelatihan = helpIndoDay($hari_mulai);
	$tanggal_pelatihan = $tanggal_mulai.' '.helpIndoMonth(date('n', strtotime($tgl_mulai)) - 1).' '.date('Y', strtotime($tgl_mulai));

	if($tgl_mulai != $tgl_selesai){
		$hari_selesai = date('w', strtotime($tgl_selesai));
		$tanggal_selesai = date('j', strtotime($tgl_selesai));
		$bulan_selesai = date('Y-m', strtotime($tgl_selesai));
		$tahun_selesai = date('Y', strtotime($tgl_selesai));

		$hari_pelatihan = helpIndoDay($hari_mulai).$separator.helpIndoDay($hari_selesai);

		if($bulan_mulai == $bulan_selesai){
			$tanggal_pelatihan = $tanggal_mulai.$separator.$tanggal_selesai.' '.helpIndoMonth(date('n', strtotime($tgl_mulai)) - 1).' '.date('Y', strtotime($tgl_mulai));
		}else{
			if($tahun_mulai == $tahun_selesai){
				$tanggal_pelatihan = $tanggal_mulai.' '.helpIndoMonth(date('n', strtotime($tgl_mulai)) - 1).$separator.$tanggal_selesai.' '.helpIndoMonth(date('n', strtotime($tgl_selesai)) - 1).' '.date('Y', strtotime($tgl_mulai));
			}else{
				$tanggal_pelatihan = $tanggal_mulai.' '.helpIndoMonth(date('n', strtotime($tgl_mulai)) - 1).' '.date('Y', strtotime($tgl_mulai)).$separator.$tanggal_selesai.' '.helpIndoMonth(date('n', strtotime($tgl_selesai)) - 1).' '.date('Y', strtotime($tgl_selesai));
			}
		}

	}

	if($hari == true){
		$tanggal_pelatihan = $hari_pelatihan.', '.$tanggal_pelatihan;
	}

	return $tanggal_pelatihan;
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

/**
* Function helpDateQuery
* Fungsi ini digunakan untuk melakukan konversi format tanggal
* @access public
* @param (date) $var Tanggal yang akan dikonversi
* @param (string) $mode Kode untuk model format yang baru
- se (short English)		: (Y-m-d) 2015-31-01				(x)
- si (short Indonesia)	: (d-m-Y) 31-01-2015					(x)
- me (medium English)	: (F d, Y) January 31, 2015				(x)
- mi (medium Indonesia)	: (d F Y) 31 Januari 2015				(v)
- le (long English)		: (l F d, Y) Sunday January 31, 2015	(x)
- li (long Indonesia)	: (l, d F Y) Senin, 31 Januari 2015		(x)
* @return (string) {'Undefined'}
*/
function helpDateQuery($column='', $mode='mi', $db='mysql')
{
	$result = false;
	if($mode == 'mi'){
		if($db == 'mysql'){
			$temp = 'CASE';
			for ($i=0; $i < 12; $i++) { 
				$temp .= ' WHEN MONTH('.$column.') = '.($i + 1).' THEN CONCAT(DAY('.$column.')," ", "'.helpIndoMonth($i).'", " ", YEAR('.$column.'))';
			}

			$temp .= 'END';

			$result = $temp;
		}elseif($db == 'pgsql'){
			$temp = 'CASE';
			for ($i=0; $i < 12; $i++) { 
				$temp .= ' WHEN EXTRACT(MONTH FROM '.$column.') = '.($i + 1).' THEN CONCAT(EXTRACT (DAY FROM '.$column.'),\' \', \''.helpIndoMonth($i).'\', \' \', EXTRACT (YEAR FROM '.$column.'))';
			}

			$temp .= 'END';

			$result = $temp;
		}
	}

	return $result;
}