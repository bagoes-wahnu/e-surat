<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNotif extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifikasi', function(Blueprint $table)
        {
            $table->increments('ntf_id');
            $table->integer('ntf_sender_id');
            $table->integer('ntf_receiver_id');
            $table->string('ntf_action')->nullable();
            $table->string('ntf_message')->nullable();
            $table->string('ntf_category')->nullable();
            $table->string('ntf_unique_id')->nullable();
            $table->boolean('ntf_sent')->default(false)->nullable();
            $table->dateTime('ntf_sent_at')->nullable();
            $table->integer('created_by')->nullable()->default('0');
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('ntf_sender_id')->references('usr_id')->on('user')->onDelete('cascade');
            $table->foreign('ntf_receiver_id')->references('usr_id')->on('user')->onDelete('cascade');
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
