<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTandaTangan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tanda_tangan', function(Blueprint $table)
        {
            $table->increments('ttd_id');
            $table->string('ttd_nama', 150)->nullable();
            $table->string('ttd_keterangan')->nullable();
            $table->string('ttd_path_file')->nullable();
            $table->boolean('ttd_aktif')->nullable()->default(true);
            $table->integer('created_by')->nullable()->default('0');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
