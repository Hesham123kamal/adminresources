<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoursesQuestions extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table = 'courses_questions';
}
