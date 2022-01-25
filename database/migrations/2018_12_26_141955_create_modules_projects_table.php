<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('modules_projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('module_id')->default(0);
            $table->string('title')->nullable();
            $table->string('file')->nullable();
            $table->integer('active')->default(0);
            $table->integer('active_by')->default(0);
            $table->timestamp('active_date')->nullable();
            $table->integer('unactive_by')->default(0);
            $table->timestamp('unactive_date')->nullable();
            $table->integer('lastedit_by')->default(0);
            $table->timestamp('lastedit_date')->nullable();
            $table->integer('add_by')->default(0);
            $table->timestamp('add_date')->nullable();
            $table->integer('deleted_by')->default(0);
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
        Schema::connection('mysql2')->dropIfExists('modules_projects');
    }
}
