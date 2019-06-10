<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExportLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('export_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('search_option');
            $table->string('token');
            $table->string('file_name');
            $table->string('file_type');
            $table->string('user_email');
            $table->boolean('generated')->default(0);
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
        Schema::dropIfExists('export_logs');
    }
}
