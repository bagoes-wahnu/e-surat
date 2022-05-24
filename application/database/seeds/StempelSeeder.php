<?php

use Illuminate\Database\Seeder;

class StempelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $current_time = date('Y-m-d H:i:s');

        $arr_data = [
            [
                'stp_id' => 1,
                'stp_nama' => "Dinas",
                'stp_uptd' => 'f',
                'created_at' => $current_time,
                'created_by' => "0",
            ],
            [
                'stp_id' => 2,
                'stp_nama' => "UPTD",
                'stp_uptd' => 't',
                'created_at' => $current_time,
                'created_by' => "0",
            ]
        ];        

        foreach ($arr_data as $key => $value) {
            
            $query = 'INSERT INTO stempel (stp_id, stp_nama, stp_uptd, created_by, created_at) VALUES ('.$value['stp_id'].', \''.$value['stp_nama'].'\', \''.$value['stp_uptd'].'\', 0, \''.$current_time.'\') ON CONFLICT (stp_id) DO UPDATE SET stp_nama = \''.$value['stp_nama'].'\', stp_uptd = \''.$value['stp_uptd'].'\', updated_by = 0, updated_at = \''.$current_time.'\';';

            DB::connection()->getPdo()->exec($query);
        }
    }
}
