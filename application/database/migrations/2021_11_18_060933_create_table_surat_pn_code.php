<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSuratPnCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_pn_code', function (Blueprint $table) {
            $table->increments('spn_id');
            $table->integer('spn_srt_id')->unsigned()->nullable();
            $table->string('spn_left')->nullable();
            $table->string('spn_top')->nullable();
            $table->string('spn_orientation')->nullable();
            $table->integer('created_by')->nullable()->default('0');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('spn_srt_id')->references('srt_id')->on('surat')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('surat_pn_code');
    }
}
