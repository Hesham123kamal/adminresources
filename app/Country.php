<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public $timestamps = false;
    protected $connection = 'mysql2';
    protected $table = 'country';

}
