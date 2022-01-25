<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class DiplomaCertificate extends Model
{
    public $timestamps = false;
    protected $dates = ['deleted_at'];
    protected $connection = 'mysql2';
    protected $table = 'diploma_certificates';

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

    public function diploma()
    {
        return $this->belongsTo('App\Diplomas');
    }

}
