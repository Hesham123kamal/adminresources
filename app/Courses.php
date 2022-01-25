<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Courses extends Model
{

    public $timestamps = false;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    protected $connection = 'mysql2';
    protected $table = 'courses';

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($model) {
            $action=$model->trashed()?'delete':'update';
            log_admin_action(Auth::user()->id,Auth::user()->username,$action,$model->getTable(),$model->id,$model->toJson());
            if($model->sent==0){
                sendRequestData(['id'=>$model->id,'type'=>'courses']);
            }
            $query="";
            $courses_categories=CourseCategory::where('course_id',$model->id)->select('category_id','sup_category_id')->get();
            foreach ($courses_categories as $category){
                $query.="UPDATE categories SET courses_count=( select count(DISTINCT course_id) FROM courses_categories INNER JOIN courses ON courses.id=courses_categories.course_id WHERE courses_categories.category_id='$category->category_id'  AND courses.published='yes' AND show_on IN('courses','all') AND courses.deleted_at IS NULL) WHERE id='$category->category_id';";
                $query.="UPDATE sup_categories SET courses_count=( select count(DISTINCT course_id) FROM courses_categories INNER JOIN courses ON courses.id=courses_categories.course_id  WHERE courses_categories.sup_category_id='$category->sup_category_id'  AND courses.published='yes' AND show_on IN('courses','all') AND courses.deleted_at IS NULL) WHERE id='$category->sup_category_id';";
            }
            //dd($query);
            if($query) {
                DB::connection('mysql2')->unprepared($query);
            }
            $date=date('Y-m-d H:i:s');
            $query="UPDATE courses SET modifiedtime='$date' WHERE id='$model->id'";
            DB::connection('mysql2')->unprepared($query);
        });
        static::created(function ($model) {
            log_admin_action(Auth::user()->id,Auth::user()->username,'create',$model->getTable(),$model->id,$model->toJson());
            sendRequestData(['id'=>$model->id,'type'=>'courses']);
        });
        static::deleted(function ($model) {
            CoursesCurriculum::where('section_id',$model->id)->delete();
            log_admin_action(Auth::user()->id,Auth::user()->username,'delete',$model->getTable(),$model->id,$model->toJson());

        });
    }

//    public function Sections()
//    {
//        return $this->hasMany('App\CoursesSections', 'course_id');
//    }
}
