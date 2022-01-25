<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTrainingsQuestionsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('modules_trainings_questions_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('module_id')->default(0);
            $table->integer('training_id')->default(0);
            $table->integer('question_id')->default(0);
            $table->text('name_ar')->nullable();
            $table->text('name_en')->nullable();
            $table->integer('answer')->default(0);
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
        Schema::connection('mysql2')->dropIfExists('modules_trainings_questions_details');
    }
}
