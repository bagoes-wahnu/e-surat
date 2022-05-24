<?php

use App\Http\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pass = bcrypt("3n3rg33k");
        $current_time = date('Y-m-d H:i:s');

        $arr_data = [
            [
                'usr_id' => 1,
                'usr_username' => "admin",
                'usr_name' => "Administrator",
                'usr_password' => $pass,
                'usr_email' => "admin@energeek.co.id",
                'usr_role' => "1",
                'created_at' => $current_time,
                'created_by' => "0",
            ],
            // [
            //     'usr_id' => 2,
            //     'usr_username' => "kadis",
            //     'usr_name' => "Kadis",
            //     'usr_password' => $pass,
            //     'usr_email' => "kadis@energeek.co.id",
            //     'usr_role' => "2",
            //     'created_at' => $current_time,
            //     'created_by' => "0",
            // ],
            // [
            //     'usr_id' => 3,
            //     'usr_username' => "sekretaris",
            //     'usr_name' => "Sekretaris",
            //     'usr_password' => $pass,
            //     'usr_email' => "sekretaris@energeek.co.id",
            //     'usr_role' => "3",
            //     'created_at' => $current_time,
            //     'created_by' => "0",
            // ],
            // [
            //     'usr_id' => 4,
            //     'usr_username' => "staff",
            //     'usr_name' => "Staff",
            //     'usr_password' => $pass,
            //     'usr_email' => "staff@energeek.co.id",
            //     'usr_role' => "4",
            //     'created_at' => $current_time,
            //     'created_by' => "0",
            // ]
        ];

        foreach ($arr_data as $key => $value) {
            $m_user = User::find($value['usr_id']);

            if(!$m_user){
                $m_user = new User();
                $m_user->usr_password = $value['usr_password'];
            }

            $m_user->usr_username = $value['usr_username'];
            $m_user->usr_name = $value['usr_name'];
            $m_user->usr_email = $value['usr_email'];
            $m_user->usr_role = $value['usr_role'];
            $m_user->save();
        }
    }
}
