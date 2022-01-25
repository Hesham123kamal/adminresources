<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class PromotionCodeUsed extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table = 'promotion_code_used';



}
