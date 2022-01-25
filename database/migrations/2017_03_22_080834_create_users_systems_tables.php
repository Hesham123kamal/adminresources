<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersSystemsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_system_users_systems', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->enum('backend_lang',['en','ar']);
            $table->enum('backend_color',['default','darkblue','blue','grey','light','light2']);
            $table->enum('backend_layout',['fluid','boxed']);
            $table->enum('backend_header',['default','fixed']);
            $table->enum('backend_top_menu_dropdown',['light','dark']);
            $table->enum('backend_sidebar_menu_mode',['default','fixed']);
            $table->enum('backend_sidebar_menu_sub_show',['accordion','hover']);
            $table->enum('backend_sidebar_menu_style',['default','light']);
            $table->enum('backend_sidebar_menu_position',['left','right']);
            $table->enum('backend_footer',['default','fixed']);
            $table->integer('lastedit_by')->nullable();
            $table->timestamp('lastedit_date')->nullable();
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
        Schema::dropIfExists('admin_system_users_systems');
    }
}
