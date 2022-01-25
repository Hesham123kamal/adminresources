<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_system_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('profile_id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('img_dir')->nullable();
            $table->string('img')->nullable();
            $table->integer('view_all_projects')->default(0);
            $table->longText('custom_views_projects')->nullable();
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
            $table->rememberToken();
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
        Schema::dropIfExists('admin_system_users');
    }
}
