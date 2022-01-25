<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profiles extends Model
{
    //
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $softDelete = true;
    protected $table = 'admin_system_profiles';
}
