<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRawImpactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_impacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('number_of_items')->nullable();
            $table->boolean('by_visual')->default(0);
            $table->boolean('by_audio')->default(0);
            $table->boolean('by_track')->default(0);
            $table->string('report_to')->nullable();
            $table->dateTime('report_date')->nullable();
            $table->text('note')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->integer('villager_id')->unsigned()->nullable();
            $table->text('patroller_note')->nullable();
            $table->integer('victim_type_id')->unsigned()->nullable();
            $table->integer('reason_id')->unsigned()->nullable();
            $table->boolean('excluded')->default(0);
            $table->integer('excluded_reason_id')->unsigned()->nullable();

            $table->foreign('villager_id')->references('id')->on('villagers')->onDelete('set null');
            $table->foreign('victim_type_id')->references('id')->on('settings')->onDelete('set null');
            $table->foreign('reason_id')->references('id')->on('settings')->onDelete('set null');
            $table->foreign('excluded_reason_id')->references('id')->on('settings');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
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
        Schema::dropIfExists('raw_impacts');
    }
}
