<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CoursesQuestionsFiles extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table = 'courses_questions_files';
}
