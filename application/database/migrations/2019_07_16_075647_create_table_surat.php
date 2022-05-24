<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSurat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat', function(Blueprint $table)
        {
            $table->increments('srt_id');
            $table->string('srt_judul')->nullable();
            $table->date('srt_tanggal')->nullable();
            $table->string('srt_path_file')->nullable();
            $table->string('srt_keterangan')->nullable();
            $table->string('srt_halaman')->nullable();
            $table->integer('srt_ttd_id')->unsigned()->nullable();
            $table->integer('srt_state')->nullable();
            $table->dateTime('srt_approved_at')->nullable();
            $table->integer('srt_approved_by')->nullable();
            $table->dateTime('srt_rejected_at')->nullable();
            $table->integer('srt_rejected_by')->nullable();
            $table->integer('created_by')->nullable()->default('0');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('srt_ttd_id')->references('ttd_id')->on('tanda_tangan')->onDelete('cascade');
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
