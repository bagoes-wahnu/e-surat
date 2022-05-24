<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSuratStempel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_stempel', function(Blueprint $table)
        {
            $table->increments('sstp_id');
            $table->integer('sstp_srt_id')->unsigned()->nullable();
            $table->integer('sstp_stp_id')->unsigned()->nullable();
            $table->string('sstp_left')->nullable();
            $table->string('sstp_top')->nullable();
            $table->string('sstp_width')->nullable();
            $table->string('sstp_height')->nullable();
            $table->integer('created_by')->nullable()->default('0');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('sstp_srt_id')->references('srt_id')->on('surat')->onDelete('cascade');
            $table->foreign('sstp_stp_id')->references('stp_id')->on('stempel')->onDelete('cascade');
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
