<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'sub_category_1',
                'sub_category_2',
                'sub_category_3',
                'sub_category_4',
                'sub_category_5',
                'permit'
            ]);

            $table->string('sys_value');
            $table->string('name');
            $table->string('name_kh')->nullable();
            $table->integer('level')->default(0);
            $table->integer('parent_id')->nullable();
            $table->boolean('is_last')->default(0);
        });

        // recreate columns as cannot change
        Schema::table('impacts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::table('raw_impacts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        // drop relationships
        Schema::table('impacts', function (Blueprint $table) {
            $table->json('categories');
        });

        Schema::table('raw_impacts', function (Blueprint $table) {
            $table->json('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('impacts', function (Blueprint $table) {
            $table->dropColumn('categories');
        });

        Schema::table('raw_impacts', function (Blueprint $table) {
            $table->dropColumn('categories');
        });

        Schema::table('impacts', function (Blueprint $table) {
            $table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });

        Schema::table('raw_impacts', function (Blueprint $table) {
            $table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'sys_value',
                'name',
                'name_kh',
                'level',
                'parent_id',
                'is_last'
            ]);

            $table->string('category');
            $table->string('sub_category_1')->nullable();
            $table->string('sub_category_2')->nullable();
            $table->string('sub_category_3')->nullable();
            $table->string('sub_category_4')->nullable();
            $table->string('sub_category_5')->nullable();
            $table->string('permit')->nullable();
        });
    }
}
