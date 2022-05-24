<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSysDeviceOsVersion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_device_os_version', function(Blueprint $table)
        {
            $table->increments('sdov_id');
            $table->integer('sdov_sdo_id')->nullable()->unsigned();
            $table->integer('sdov_sdk')->nullable();
            $table->string('sdov_version')->nullable();
            $table->string('sdov_name')->nullable();
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
