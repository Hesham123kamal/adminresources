<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class FeedbackPopup extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table = 'feedback_rating';

    public function User(){
        return $this->belongsTo('App\NormalUser','user_id');
    }
}
