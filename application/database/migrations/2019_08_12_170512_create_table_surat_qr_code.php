<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSuratQrCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_qr_code', function(Blueprint $table)
        {
            $table->increments('sqr_id');
            $table->integer('sqr_srt_id')->unsigned()->nullable();
            $table->string('sqr_left')->nullable();
            $table->string('sqr_top')->nullable();
            $table->integer('created_by')->nullable()->default('0');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('sqr_srt_id')->references('srt_id')->on('surat')->onDelete('cascade');
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
