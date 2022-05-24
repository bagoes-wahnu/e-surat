<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableUser3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function(Blueprint $table)
        {
            $table->integer('usr_jbt_id')->unsigned()->nullable();
        });

        if (Schema::hasColumn('user', 'usr_pgw_id')) {
            Schema::table('user', function (Blueprint $table) {
                $table->dropColumn('usr_pgw_id');
            });
        }
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
