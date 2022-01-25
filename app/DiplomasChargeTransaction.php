<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class DiplomasChargeTransaction extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table = 'diplomas_charge_transaction';

    public function user()
    {
        return $this->belongsTo('App\NormalUser','user_id');
    }
    /*
         *
         * retrieved : after a record has been retrieved.
         * creating : before a record has been created.
         * created : after a record has been created.
         * updating : before a record is updated.
         * updated : after a record has been updated.
         * saving : before a record is saved (either created or updated).
         * saved : after a record has been saved (either created or updated).
         * deleting : before a record is deleted or soft-deleted.
         * deleted : after a record has been deleted or soft-deleted.
         * restoring : before a soft-deleted record is going to be restored.
         * restored : after a soft-deleted record has been restored.
         *
         * */
    protected static function boot()
    {
        parent::boot();
        if(Auth::check()){
            static::updated(function ($model) {
                sendDiplomasChargeTransaction($model->id);
                updateAcademyChargeTransaction($model->user_id);
                log_admin_action(Auth::user()->id,Auth::user()->username,'update',$model->getTable(),$model->id,$model->toJson());
            });
            static::created(function ($model) {
                sendDiplomasChargeTransaction($model->id);
                updateAcademyChargeTransaction($model->user_id);
                log_admin_action(Auth::user()->id,Auth::user()->username,'create',$model->getTable(),$model->id,$model->toJson());
            });
            static::deleted(function ($model) {
                deleteChargeTransactionFromCRM($model->id,'diplomas transaction');
                updateAcademyChargeTransaction($model->user_id);
                log_admin_action(Auth::user()->id,Auth::user()->username,'delete',$model->getTable(),$model->id,$model->toJson());
            });
        }

    }
}
