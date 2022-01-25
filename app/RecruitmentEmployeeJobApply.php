<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class RecruitmentEmployeeJobApply extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table = 'recruitment_employee_job_apply';

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

}
