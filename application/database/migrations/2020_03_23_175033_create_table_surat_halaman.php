<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSuratHalaman extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('surat_halaman', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('srt_id')->nullable();
            $table->integer('halaman')->nullable();
            $table->string('orientation')->nullable();
            $table->integer('created_by')->nullable()->default('0');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
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
