<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;



class InitiativeVideos extends Model
{

    public $timestamps = false;
    use SoftDeletes;
    protected $softDelete = true;
    protected $connection = 'mysql2';
    protected $table = 'initiative_videos';

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($model) {
            $action=$model->trashed()?'delete':'update';
            log_admin_action(Auth::user()->id,Auth::user()->username,$action,$model->getTable(),$model->id,$model->toJson());
        });
        static::created(function ($model) {
            log_admin_action(Auth::user()->id,Auth::user()->username,'create',$model->getTable(),$model->id,$model->toJson());
        });
        static::deleted(function ($model) {
            log_admin_action(Auth::user()->id,Auth::user()->username,'delete',$model->getTable(),$model->id,$model->toJson());
        });
    }

//    public function Sections()
//    {
//        return $this->hasMany('App\CoursesSections', 'course_id');
//    }
}
