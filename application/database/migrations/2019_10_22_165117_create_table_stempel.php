<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStempel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stempel', function(Blueprint $table)
        {
            $table->increments('stp_id');
            $table->string('stp_nama', 150)->nullable();
            $table->string('stp_path_file')->nullable();
            $table->boolean('stp_aktif')->nullable()->default(true);
            $table->boolean('stp_uptd')->nullable()->default(false);
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
