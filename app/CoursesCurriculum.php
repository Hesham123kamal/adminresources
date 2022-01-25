<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CoursesCurriculum extends Model
{
    public $timestamps = false;
    use SoftDeletes;
    protected $softDelete = true;
    protected $connection = 'mysql2';
    protected $table = 'cources_curriculum';

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($model) {
            $action=$model->trashed()?'delete':'update';
            log_admin_action(Auth::user()->id,Auth::user()->username,$action,$model->getTable(),$model->id,$model->toJson());
            $date=date('Y-m-d H:i:s');
            $query="UPDATE courses SET modifiedtime='$date' WHERE id='$model->course_id'";
            DB::connection('mysql2')->unprepared($query);
        });
        static::created(function ($model) {
            log_admin_action(Auth::user()->id,Auth::user()->username,'create',$model->getTable(),$model->id,$model->toJson());
            $date=date('Y-m-d H:i:s');
            $query="UPDATE courses SET modifiedtime='$date' WHERE id='$model->course_id'";
            DB::connection('mysql2')->unprepared($query);
        });
        static::deleted(function ($model) {
            log_admin_action(Auth::user()->id,Auth::user()->username,'delete',$model->getTable(),$model->id,$model->toJson());
        });
    }

    public function section()
    {
        return $this->belongsTo('App\CoursesSections','section_id');
    }
    public function course()
    {
        return $this->belongsTo('App\Courses','course_id');
    }

}
