<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSuratHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_history', function(Blueprint $table)
        {
            $table->increments('srh_id');
            $table->integer('srh_srt_id')->unsigned()->nullable();
            $table->integer('srh_pgw_id')->unsigned()->nullable();
            $table->integer('srh_jbt_id')->unsigned()->nullable();
            $table->boolean('srh_rollback')->default(false)->nullable();
            $table->integer('created_by')->nullable()->default('0');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('srh_srt_id')->references('srt_id')->on('surat')->onDelete('cascade');
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
