<?php

use Illuminate\Database\Seeder;

class SysDeviceOsSeeder extends Seeder
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
    			'sdo_id'	=> 1,
    			'sdo_name'	=> 'Android'
    		]
    	];

    	foreach ($arr_config as $key => $value) {
    		$query = "INSERT INTO sys_device_os (sdo_id, sdo_name, created_by, created_at) VALUES ('".$value['sdo_id']."', '".$value['sdo_name']."', 0, '{$current_time}') ON CONFLICT (sdo_id) DO UPDATE SET sdo_name = '".$value['sdo_name']."', updated_by = 0, updated_at = '{$current_time}';";

    		DB::connection()->getPdo()->exec($query);
	    }
    }
}
