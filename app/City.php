<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table = 'cities';

}
