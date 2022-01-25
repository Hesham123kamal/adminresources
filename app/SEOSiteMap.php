<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SEOSiteMap extends Model
{
    protected $connection = 'mysql2';
    protected $table = 'seo_sitemap';
}
