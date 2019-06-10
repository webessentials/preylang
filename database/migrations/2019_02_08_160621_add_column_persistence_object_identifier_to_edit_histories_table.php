<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnPersistenceObjectIdentifierToEditHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('edit_histories', function (Blueprint $table) {
            $table->string('persistence_object_identifier', 40)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('edit_histories', function (Blueprint $table) {
            $table->dropColumn('persistence_object_identifier');
        });
    }
}
