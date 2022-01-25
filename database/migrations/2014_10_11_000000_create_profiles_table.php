<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_system_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->longText('permissions');
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
        Schema::dropIfExists('admin_system_profiles');
    }
}
