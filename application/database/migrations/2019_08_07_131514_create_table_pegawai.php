<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePegawai extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pegawai', function(Blueprint $table)
        {
            $table->increments('pgw_id');
            $table->integer('pgw_jbt_id')->nullable()->unsigned();
            $table->string('pgw_nama')->nullable();
            $table->string('pgw_nik', 20)->nullable();
            $table->string('pgw_nip', 50)->nullable();
            $table->string('pgw_email', 50)->nullable();
            $table->string('pgw_telp', 20)->nullable();
            $table->integer('pgw_gender')->nullable();
            $table->string('pgw_foto')->nullable();
            $table->date('pgw_tanggal_pns')->nullable();
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
