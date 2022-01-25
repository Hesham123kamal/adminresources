<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class InternationalDiplomasChargeTransactionSuspendLog extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table = 'international_diplomas_charge_transaction_suspend_log';

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
        return $this->belongsTo('App\InternationalDiplomas','diploma_id');
    }
    public function user()
    {
        return $this->belongsTo('App\NormalUser','user_id');
    }

}
