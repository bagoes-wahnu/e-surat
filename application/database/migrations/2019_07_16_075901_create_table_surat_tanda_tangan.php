<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSuratTandaTangan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_tanda_tangan', function(Blueprint $table)
        {
            $table->increments('stt_id');
            $table->integer('stt_srt_id')->unsigned()->nullable();
            $table->integer('stt_ttd_id')->unsigned()->nullable();
            $table->integer('stt_page')->nullable();
            $table->string('stt_left')->nullable();
            $table->string('stt_top')->nullable();
            $table->integer('created_by')->nullable()->default('0');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('stt_srt_id')->references('srt_id')->on('surat')->onDelete('cascade');
            $table->foreign('stt_ttd_id')->references('ttd_id')->on('tanda_tangan')->onDelete('cascade');
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
