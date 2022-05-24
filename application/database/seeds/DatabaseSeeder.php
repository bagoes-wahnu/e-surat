<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // COMMENT WHAT YOU DON'T NEED
        $this->call(UserSeeder::class);
        $this->call(SysConfigSeeder::class);
        $this->call(SysRoleUserSeeder::class);
        $this->call(SuratJenisSeeder::class);
        $this->call(TtdSeeder::class);
        $this->call(StempelSeeder::class);
        $this->call(SysDeviceOsSeeder::class);
        $this->call(SysDeviceOsVersionSeeder::class);
    }
}
