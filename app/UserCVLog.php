<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class UserCVLog extends Model
{
    protected $table = 'users_cv_downloaded_log';

    public function user(){
        return $this->belongsTo('App\NormalUser','user_id');
    }

    public function cv(){
        return $this->belongsTo('App\UserCV','user_cv_id');
    }

}
