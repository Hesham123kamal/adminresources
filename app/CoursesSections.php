<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class CoursesSections extends Model
{
    public $timestamps = false;
    use SoftDeletes;
    protected $softDelete = true;
    protected $connection = 'mysql2';
    protected $table = 'courses_sections';

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($model) {
            $action=$model->trashed()?'delete':'update';
            log_admin_action(Auth::user()->id,Auth::user()->username,$action,$model->getTable(),$model->id,$model->toJson());
            if($model->sent==0){
                sendRequestData(['id'=>$model->id,'type'=>'courses_sections']);
            }
            $date=date('Y-m-d H:i:s');
            $query="UPDATE courses SET modifiedtime='$date' WHERE id='$model->course_id'";
            DB::connection('mysql2')->unprepared($query);
        });
        static::created(function ($model) {
            log_admin_action(Auth::user()->id,Auth::user()->username,'create',$model->getTable(),$model->id,$model->toJson());
            sendRequestData(['id'=>$model->id,'type'=>'courses_sections']);
            $date=date('Y-m-d H:i:s');
            $query="UPDATE courses SET modifiedtime='$date' WHERE id='$model->course_id'";
            DB::connection('mysql2')->unprepared($query);
        });
        static::deleted(function ($model) {
            log_admin_action(Auth::user()->id,Auth::user()->username,'delete',$model->getTable(),$model->id,$model->toJson());
        });
    }

    public function course()
    {
        return $this->belongsTo('App\Courses');
    }

}
