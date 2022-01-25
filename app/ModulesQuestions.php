<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;


class ModulesQuestions extends Model
{
    //
    use SoftDeletes;
    protected $connection = 'mysql2';
    protected $table = 'modules_questions';
    protected $dates = ['deleted_at'];

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

    public function ModulesQuestionsDetails(){
        if($this->type=='true_false'){
            return $this->hasOne('App\ModulesQuestionsDetails','question_id')->orderBy('id','ASC');
        }else{
            return $this->hasMany('App\ModulesQuestionsDetails','question_id')->orderBy('id','ASC');
        }
    }
}
