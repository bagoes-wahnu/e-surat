<?php

use Illuminate\Database\Seeder;

class TtdSeeder extends Seeder
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
                'ttd_id' => 1,
                'ttd_nama' => "Kadis",
                'ttd_keterangan' => null,
                'created_at' => $current_time,
                'created_by' => "0",
            ],
            [
                'ttd_id' => 2,
                'ttd_nama' => "Sekretaris",
                'ttd_keterangan' => null,
                'created_at' => $current_time,
                'created_by' => "0",
            ],
            [
                'ttd_id' => 3,
                'ttd_nama' => "UPTD",
                'ttd_keterangan' => null,
                'created_at' => $current_time,
                'created_by' => "0",
            ]
        ];
        

        foreach ($arr_data as $key => $value) {
            
            $query = 'INSERT INTO tanda_tangan (ttd_id, ttd_nama, ttd_keterangan, created_by, created_at) VALUES ('.$value['ttd_id'].', \''.$value['ttd_nama'].'\', \''.$value['ttd_keterangan'].'\', 0, \''.$current_time.'\') ON CONFLICT (ttd_id) DO UPDATE SET ttd_nama = \''.$value['ttd_nama'].'\', ttd_keterangan = \''.$value['ttd_keterangan'].'\', updated_by = 0, updated_at = \''.$current_time.'\';';

            DB::connection()->getPdo()->exec($query);
        }
    }
}
