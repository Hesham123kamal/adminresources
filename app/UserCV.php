<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class UserCV extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table = 'users_cv';

    public function user()
    {
        return $this->belongsTo('App\NormalUser','user_id');
    }

    public function cv_logs()
    {
        return $this->hasMany('App\UserCVLog','user_cv_id');
    }
}
