<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('impacts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('number_of_items')->nullable();
            $table->string('name')->nullable();
            $table->string('employer')->nullable();
            $table->string('license')->nullable();
            $table->boolean('agreement')->default(0);
            $table->boolean('by_visual')->default(0);
            $table->boolean('by_audio')->default(0);
            $table->boolean('by_track')->default(0);
            $table->boolean('burned_wood')->default(0);
            $table->string('report_to')->nullable();
            $table->dateTime('report_date')->nullable();
            $table->boolean('active')->default(1);
            $table->text('note')->nullable();
            $table->text('note_kh')->nullable();
            $table->text('patroller_note')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->boolean('modified')->default(0);
            $table->boolean('excluded')->default(0);
            $table->integer('excluded_reason_id')->unsigned()->nullable();
            $table->text('excluded_note')->nullable();
            $table->string('impact_number');
            $table->integer('villager_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->boolean('category_modified')->default(0);
            $table->integer('offender_id')->unsigned()->nullable();
            $table->integer('threatening_id')->unsigned()->nullable();
            $table->integer('reason_id')->unsigned()->nullable();
            $table->integer('designation_id')->unsigned()->nullable();
            $table->integer('victim_type_id')->unsigned()->nullable();
            $table->integer('proof_id')->unsigned()->nullable();
            $table->string('witness')->nullable();
            $table->string('location')->nullable();
            $table->integer('raw_impact_id')->unsigned()->nullable();

            $table->foreign('villager_id')->references('id')->on('villagers')->onDelete('set null');
            $table->foreign('excluded_reason_id')->references('id')->on('settings')->onDelete('set null');
            $table->foreign('offender_id')->references('id')->on('settings')->onDelete('set null');
            $table->foreign('threatening_id')->references('id')->on('settings')->onDelete('set null');
            $table->foreign('reason_id')->references('id')->on('settings')->onDelete('set null');
            $table->foreign('designation_id')->references('id')->on('settings')->onDelete('set null');
            $table->foreign('victim_type_id')->references('id')->on('settings')->onDelete('set null');
            $table->foreign('proof_id')->references('id')->on('settings')->onDelete('set null');
            $table->foreign('raw_impact_id')->references('id')->on('raw_impacts')->onDelete('set null');
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
        Schema::dropIfExists('impacts');
    }
}
