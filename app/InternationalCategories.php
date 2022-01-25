<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;


class InternationalCategories extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table = 'international_categories';
    use SoftDeletes;
    protected $softDelete = true;

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

}
