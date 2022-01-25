<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class RecruitmentJobsRoles extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table = 'recruitment_jobs_roles';

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

    public function role(){
        return $this->belongsTo('App\RecruitmentJobRoles','recruitment_job_role_id');
    }
    public function job(){
        return $this->belongsTo('App\RecruitmentJob','recruitment_job_id');
    }


}
