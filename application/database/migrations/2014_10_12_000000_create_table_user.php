<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('usr_id');
            $table->string('usr_username');
            $table->string('usr_name');
            $table->string('usr_email', 150)->nullable();
            $table->string('usr_password');
            $table->integer('usr_role');
            $table->boolean('usr_aktif')->default(true);
            $table->rememberToken();
            $table->boolean('usr_token_permission')->nullable()->default(true);
            $table->integer('usr_token_limits')->nullable()->default('1');
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
