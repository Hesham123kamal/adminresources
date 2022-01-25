<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class ChargeTransaction extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table = 'charge_transaction';

    public function user()
    {
        return $this->belongsTo('App\NormalUser','user_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::updated(function ($model) {
            sendChargeTransaction($model->id);
            updateAcademyChargeTransaction($model->user_id);
            log_admin_action(Auth::user()->id,Auth::user()->username,'update',$model->getTable(),$model->id,$model->toJson());
        });
        static::created(function ($model) {
            sendChargeTransaction($model->id);
            updateAcademyChargeTransaction($model->user_id);
            log_admin_action(Auth::user()->id,Auth::user()->username,'create',$model->getTable(),$model->id,$model->toJson());
        });
        static::deleted(function ($model) {
            deleteChargeTransactionFromCRM($model->id,'charge transaction');
            updateAcademyChargeTransaction($model->user_id);
            log_admin_action(Auth::user()->id,Auth::user()->username,'delete',$model->getTable(),$model->id,$model->toJson());
        });
    }

}
