<?php

use Illuminate\Database\Seeder;

class SysDeviceOsVersionSeeder extends Seeder
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
    			'sdov_id'	=> 1,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 7,
    			'sdov_version'	=> '2.1',
    			'sdov_name'	=> 'Eclair',
    		],
    		[
    			'sdov_id'	=> 2,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 8,
    			'sdov_version'	=> '2.2',
    			'sdov_name'	=> 'Froyo',
    		],
    		[
    			'sdov_id'	=> 3,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 9,
    			'sdov_version'	=> '2.3',
    			'sdov_name'	=> 'Gingerbread',
    		],
    		[
    			'sdov_id'	=> 4,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 10,
    			'sdov_version'	=> '2.3.3',
    			'sdov_name'	=> 'Gingerbread',
    		],
    		[
    			'sdov_id'	=> 5,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 11,
    			'sdov_version'	=> '3.0',
    			'sdov_name'	=> 'Honeycomb',
    		],
    		[
    			'sdov_id'	=> 6,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 12,
    			'sdov_version'	=> '3.1',
    			'sdov_name'	=> 'Honeycomb',
    		],
    		[
    			'sdov_id'	=> 7,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 13,
    			'sdov_version'	=> '3.2',
    			'sdov_name'	=> 'Honeycomb',
    		],
    		[
    			'sdov_id'	=> 8,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 14,
    			'sdov_version'	=> '4.0',
    			'sdov_name'	=> 'IceCreamSandwich',
    		],
    		[
    			'sdov_id'	=> 9,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 15,
    			'sdov_version'	=> '4.0.3',
    			'sdov_name'	=> 'IceCreamSandwich',
    		],
    		[
    			'sdov_id'	=> 10,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 16,
    			'sdov_version'	=> '4.1',
    			'sdov_name'	=> 'Jelly Bean',
    		],
    		[
    			'sdov_id'	=> 11,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 17,
    			'sdov_version'	=> '4.2',
    			'sdov_name'	=> 'Jelly Bean',
    		],
    		[
    			'sdov_id'	=> 12,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 18,
    			'sdov_version'	=> '4.3',
    			'sdov_name'	=> 'Jelly Bean',
    		],
    		[
    			'sdov_id'	=> 13,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 19,
    			'sdov_version'	=> '4.4',
    			'sdov_name'	=> 'KitKat',
    		],
    		[
    			'sdov_id'	=> 14,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 20,
    			'sdov_version'	=> '4.4W',
    			'sdov_name'	=> 'KitKat Wear',
    		],
    		[
    			'sdov_id'	=> 15,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 21,
    			'sdov_version'	=> '5.0',
    			'sdov_name'	=> 'Lollipop',
    		],
    		[
    			'sdov_id'	=> 16,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 22,
    			'sdov_version'	=> '5.1',
    			'sdov_name'	=> 'Lollipop',
    		],
    		[
    			'sdov_id'	=> 17,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 23,
    			'sdov_version'	=> '6.0',
    			'sdov_name'	=> 'Marshmallow',
    		],
    		[
    			'sdov_id'	=> 18,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 24,
    			'sdov_version'	=> '7.0',
    			'sdov_name'	=> 'Nougat',
    		],
    		[
    			'sdov_id'	=> 19,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 25,
    			'sdov_version'	=> '7.1.1',
    			'sdov_name'	=> 'Nougat',
    		],
    		[
    			'sdov_id'	=> 20,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 26,
    			'sdov_version'	=> '8.0',
    			'sdov_name'	=> 'Oreo',
    		],
    		[
    			'sdov_id'	=> 21,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 27,
    			'sdov_version'	=> '8.1',
    			'sdov_name'	=> 'Oreo',
    		],
    		[
    			'sdov_id'	=> 22,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 28,
    			'sdov_version'	=> '9.0',
    			'sdov_name'	=> 'Pie',
    		],
    		[
    			'sdov_id'	=> 23,
    			'sdov_sdo_id'	=> 1,
    			'sdov_sdk'	=> 29,
    			'sdov_version'	=> '9.+',
    			'sdov_name'	=> 'Q',
    		],
    	];

    	foreach ($arr_config as $key => $value) {
    		$query = "INSERT INTO sys_device_os_version (sdov_id, sdov_sdo_id, sdov_sdk, sdov_version, sdov_name, created_by, created_at) VALUES (".$value['sdov_id'].", ".$value['sdov_sdo_id'].", ".$value['sdov_sdk'].", '".$value['sdov_version']."', '".$value['sdov_name']."', 0, '{$current_time}') ON CONFLICT (sdov_id) DO UPDATE SET sdov_name = '".$value['sdov_name']."', updated_by = 0, updated_at = '{$current_time}';";

    		DB::connection()->getPdo()->exec($query);
	    }
    }
}
