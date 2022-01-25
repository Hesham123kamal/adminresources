<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Articles extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'articles';

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
    public function tag()
    {
        return $this->belongsToMany('App\Tags', 'tags_related','src_id','tag_id')
            ->where('type','=','articles');
    }
    public function section()
    {
        return $this->belongsToMany('App\InitiativeSections', 'initiative_articles_sections','article_id','section_id');
    }
}
