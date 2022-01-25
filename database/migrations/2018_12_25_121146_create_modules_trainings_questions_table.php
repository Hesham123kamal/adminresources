<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTrainingsQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql2')->create('modules_trainings_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('module_id')->default(0);
            $table->integer('training_id')->default(0);
            $table->enum('difficulty_type',['easy','normal','hard'])->nullable();
            $table->enum('type',['chose_multiple','chose_single','true_false'])->nullable();
            $table->text('name_ar')->nullable();
            $table->text('name_en')->nullable();
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
        Schema::connection('mysql2')->dropIfExists('modules_trainings_questions');
    }
}
