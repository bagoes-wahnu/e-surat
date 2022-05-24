<?php

use Illuminate\Database\Seeder;

class SysConfigSeeder extends Seeder
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
    			'config_name'	=> 'default_password',
    			'config_value'	=> 'dishub2019'
    		],
            [
                'config_name'   => 'thumbnail_dimension',
                'config_value'  => '250x250#500x500'
            ],
            [
                'config_name'   => 'thumbnail_ttd',
                'config_value'  => '500x261'
            ],
            [
                'config_name'   => 'thumbnail_stempel',
                'config_value'  => '500x500'
            ],
            [
                'config_name'   => 'url_esdm',
                'config_value'  => 'https://dishub.surabaya.go.id/e_sdm_dishub/'
            ],
            [
                'config_name'   => 'api_level',
                'config_value'  => 'e-surat/api/get_level'
            ],
            [
                'config_name'   => 'api_jabatan',
                'config_value'  => 'e-surat/api/get_jabatan'
            ],
            [
                'config_name'   => 'api_pegawai',
                'config_value'  => 'e-surat/api/get_pegawai'
            ],
            [
                'config_name'   => 'api_pegawai_kontrak',
                'config_value'  => 'e-surat/api/get_pegawai_kontrak'
            ]
    	];

    	foreach ($arr_config as $key => $value) {
    		$query = "INSERT INTO sys_config (config_name, config_value, created_by, created_at) VALUES ('".$value['config_name']."', '".$value['config_value']."', 0, '{$current_time}') ON CONFLICT (config_name) DO UPDATE SET config_value = '".$value['config_value']."', updated_by = 0, updated_at = '{$current_time}';";

    		DB::connection()->getPdo()->exec($query);
	    }
    }
}
