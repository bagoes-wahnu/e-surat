<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSuratMasuk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_masuk', function(Blueprint $table)
        {
            $table->increments('srm_id');
            $table->string('srm_no')->nullable();
            $table->string('srm_judul')->nullable();
            $table->date('srm_tanggal')->nullable();
            $table->string('srm_pengirim')->nullable();
            $table->string('srm_path_file')->nullable();
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
