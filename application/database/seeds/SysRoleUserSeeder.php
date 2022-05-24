<?php

use Illuminate\Database\Seeder;

class SysRoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $current_time = date('Y-m-d H:i:s');
    	$arr_role = [
    		[
    			'role_id'	=> 1,
    			'role_code'	=> '1',
    			'role_keterangan'	=> 'Administrator'
    		],
            [
                'role_id'   => 2,
                'role_code' => '2',
                'role_keterangan'   => 'Kadis'
            ],
            [
                'role_id'   => 3,
                'role_code' => '3',
                'role_keterangan'   => 'Sekretaris'
            ],
            [
                'role_id'   => 4,
                'role_code' => '4',
                'role_keterangan'   => 'Staf'
            ]
    	];

    	foreach ($arr_role as $key => $value) {
    		$query = "INSERT INTO sys_role_user (role_id, role_code, role_keterangan, created_by, created_at) VALUES (".$value['role_id'].", '".$value['role_code']."', '".$value['role_keterangan']."', 0, '{$current_time}') ON CONFLICT (role_id) DO UPDATE SET role_keterangan = '".$value['role_keterangan']."', updated_by = 0, updated_at = '{$current_time}';";

    		DB::connection()->getPdo()->exec($query);
	    }
    }
}
