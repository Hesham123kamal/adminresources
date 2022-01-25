<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class RecruitmentJob extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table = 'recruitment_jobs';

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

    public function country(){
        return $this->belongsTo('App\Country','country_id');
    }
    public function city(){
        return $this->belongsTo('App\City','city_id');
    }
    public function state(){
        return $this->belongsTo('App\State','state_id');
    }

}
