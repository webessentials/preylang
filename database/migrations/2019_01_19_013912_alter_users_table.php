<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->unique();
            $table->string('language_key')->default('en');
            $table->integer('villager_id')->unsigned()->nullable();
            $table->string('role')->nullable();
            $table->integer('user_group_id')->unsigned()->nullable();
            $table->boolean('active')->default(1);
            $table->softDeletes();

            $table->foreign('villager_id')->references('id')->on('villagers')->onDelete('set null');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'username', 'language_key', 'role', 'active'
            ]);
            $table->dropForeign(['villager_id']);
            $table->dropForeign(['user_group_id']);
            $table->string('name');
            $table->dropSoftDeletes();
        });
    }
}
