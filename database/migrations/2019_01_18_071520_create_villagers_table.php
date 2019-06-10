<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVillagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('villagers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('device_imei')->unique();
            $table->string('name');
            $table->integer('province_id')->unsigned()->nullable();
            $table->integer('user_group_id')->unsigned()->nullable();
            $table->string('password')->nullable();
            $table->longText('access_token')->nullable();
            $table->dateTime('token_expiration_date')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('province_id')->references('id')->on('settings')->onDelete('set null');
            $table->foreign('user_group_id')->references('id')->on('user_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('villagers');
    }
}
