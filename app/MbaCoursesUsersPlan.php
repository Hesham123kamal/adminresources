<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class MbaCoursesUsersPlan extends Model
{
    //
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table='mba_courses_user_plan';

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($model) {
            log_admin_action(Auth::user()->id,Auth::user()->username,'update',$model->getTable(),$model->id,$model->toJson());
        });
        static::created(function ($model) {
            log_admin_action(Auth::user()->id,Auth::user()->username,'create',$model->getTable(),$model->id,$model->toJson());
        });
        static::deleted(function ($model) {
            log_admin_action(Auth::user()->id,Auth::user()->username,'delete',$model->getTable(),$model->id,$model->toJson());
        });
    }

    public function mba()
    {
        return $this->belongsTo('App\Mba','	module_id');
    }
    public function course()
    {
        return $this->belongsTo('App\Courses','course_id');
    }
    public function user()
    {
        return $this->belongsTo('App\NormalUser','user_id');
    }
}

