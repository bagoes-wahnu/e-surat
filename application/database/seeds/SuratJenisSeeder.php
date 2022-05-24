<?php

use App\Http\Models\SuratJenis;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuratJenisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $current_time = date('Y-m-d H:i:s');
    	$arr_config = [
    		[
    			'srj_id'	=> 1,
    			'srj_nama'	=> 'Anggaran',
                'srj_urutan' => 2,
                'srj_cuti'  => 'f'
    		],
            [
                'srj_id'   => 2,
                'srj_nama'  => 'Surat Perintah',
                'srj_urutan' => 3,
                'srj_cuti'  => 'f'
            ],
            [
                'srj_id'   => 3,
                'srj_nama'  => 'Surat Edaran',
                'srj_urutan' => 4,
                'srj_cuti'  => 'f'
            ],
            [
                'srj_id'   => 4,
                'srj_nama'  => 'Undangan',
                'srj_urutan' => 5,
                'srj_cuti'  => 'f'
            ],
            [
                'srj_id'   => 5,
                'srj_nama'  => 'Nota Dinas',
                'srj_urutan' => 6,
                'srj_cuti'  => 'f'
            ],
            [
                'srj_id'   => 6,
                'srj_nama'  => 'Peringatan',
                'srj_urutan' => 7,
                'srj_cuti'  => 'f'
            ],
            [
                'srj_id'   => 7,
                'srj_nama'  => 'Surat Tugas',
                'srj_urutan' => 8,
                'srj_cuti'  => 'f'
            ],
            [
                'srj_id'   => 8,
                'srj_nama'  => 'Kepegawaian',
                'srj_urutan' => 9,
                'srj_cuti'  => 'f'
            ],
            [
                'srj_id'   => 9,
                'srj_nama'  => 'Monev',
                'srj_urutan' => 10,
                'srj_cuti'  => 'f'
            ],
            [
                'srj_id'   => 10,
                'srj_nama'  => 'Surat Pengantar',
                'srj_urutan' => 11,
                'srj_cuti'  => 'f'
            ],
            [
                'srj_id'   => 11,
                'srj_nama'  => 'Surat Dinas',
                'srj_urutan' => 12,
                'srj_cuti'  => 'f'
            ],
            [
                'srj_id'   => 12,
                'srj_nama'  => 'Lain-lain',
                'srj_urutan' => 13,
                'srj_cuti'  => 'f'
            ],
            [
                'srj_id'   => 13,
                'srj_nama'  => 'Surat Cuti',
                'srj_urutan' => 1,
                'srj_cuti'  => 't'
            ]
    	];

    	foreach ($arr_config as $key => $value) {
            $m_jenis_surat = SuratJenis::find($value['srj_id']);

            if(!$m_jenis_surat){
                $m_jenis_surat = new SuratJenis();
            }

            $m_jenis_surat->srj_nama = $value['srj_nama'];
            $m_jenis_surat->srj_urutan = $value['srj_urutan'];
            $m_jenis_surat->srj_cuti = $value['srj_cuti'];
            $m_jenis_surat->save();
	    }
    }
}
