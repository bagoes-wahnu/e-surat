<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
use App\Http\Models\SuratSelesai;
use App\Http\Models\Jabatan;
use App\Http\Models\JabatanLevel;
use App\Http\Models\Pegawai;
use App\Http\Models\SuratHalaman;
use App\Http\Models\User;

class SuratIzinTerminalController extends Controller
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

		$data['title'] = 'Daftar Surat Izin Terminal';
		$data['menu_active'] = 'surat';
		$data['last_level'] = $this->lastGrade();
		$data['grade'] = $this->userdata('grade');
		$data['jenis_surat'] = SuratJenis::get_data(false, false, false, 'ASC', 'srj_urutan');
		$data['pegawai'] = Pegawai::get_data(false, $id_jabatan, false, 'pgw_nama', 'ASC');
		$data['role'] = $this->userdata('role');
		return view('surat_izin_terminal/grid', $data);
	}
}
