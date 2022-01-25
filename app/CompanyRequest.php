<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class CompanyRequest extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table = 'companies_requests';

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($model) {
            $action=$model->deleted?'delete':'update';
            log_admin_action(Auth::user()->id,Auth::user()->username,$action,$model->getTable(),$model->id,$model->toJson());
        });
        static::created(function ($model) {
            log_admin_action(Auth::user()->id,Auth::user()->username,'create',$model->getTable(),$model->id,$model->toJson());
        });
        static::deleted(function ($model) {
            log_admin_action(Auth::user()->id,Auth::user()->username,'delete',$model->getTable(),$model->id,$model->toJson());
        });
    }

    public function user()
    {
        return $this->belongsTo('App\NormalUser','user_id');
    }

    public function company()
    {
        return $this->belongsTo('App\Company','company_id');
    }


}
