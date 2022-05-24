<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableTandaTangan2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tanda_tangan', function(Blueprint $table)
        {
            $table->string('ttd_path_file_merah')->nullable();
            $table->string('ttd_path_file_biru')->nullable();
        });

        DB::connection()->getPdo()->exec('ALTER TABLE tanda_tangan RENAME COLUMN ttd_path_file TO ttd_path_file_hitam;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
