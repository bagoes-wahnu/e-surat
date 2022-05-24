<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSuratPnCode01 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surat_pn_code', function (Blueprint $table) {
            $table->string('spn_width')->nullable();
            $table->string('spn_height')->nullable();
            $table->integer('spn_page')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surat_pn_code', function (Blueprint $table) {
            //
        });
    }
}
