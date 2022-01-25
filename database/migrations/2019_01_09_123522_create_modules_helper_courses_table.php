<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesHelperCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('modules_helper_courses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('module_id')->default(0);
            $table->integer('course_id')->default(0);
            $table->integer('published')->default(0);
            $table->integer('published_by')->default(0);
            $table->timestamp('published_date')->nullable();
            $table->integer('unpublished_by')->default(0);
            $table->timestamp('unpublished_date')->nullable();
            $table->integer('lastedit_by')->default(0);
            $table->timestamp('lastedit_date')->nullable();
            $table->integer('add_by')->default(0);
            $table->timestamp('add_date')->nullable();
            $table->integer('deleted_by')->default(0);
            $table->timestamp('deleted_date')->nullable();
            $table->softDeletes();
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
        Schema::connection('mysql2')->dropIfExists('modules_helper_courses');
    }
}
