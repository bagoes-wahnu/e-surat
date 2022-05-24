<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSurat3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('surat', 'srt_keterangan')) {
            Schema::table('surat', function (Blueprint $table)
            {
                $table->dropColumn('srt_keterangan');
            });
        }

        Schema::table('surat', function (Blueprint $table)
        {
            $table->text('srt_keterangan')->nullable();
        });
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
