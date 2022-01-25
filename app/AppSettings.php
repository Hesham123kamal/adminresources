<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppSettings extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'app_settings';
}
