<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('file_name');
            $table->string('file_type');
            $table->integer('impact_id')->unsigned()->nullable();
            $table->boolean('is_imported')->default(0);
            $table->dateTime('import_date')->nullable();
            $table->boolean('facebook_post')->default(0);
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->dateTime('report_date')->nullable();
            $table->string('original_file_name')->nullable();
            $table->boolean('converted')->default(0);
            $table->dateTime('converted_at')->nullable();

            $table->foreign('impact_id')->references('id')->on('impacts')->onDelete('set null');
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
        Schema::dropIfExists('files');
    }
}
