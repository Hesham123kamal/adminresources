<?php

namespace App\Http\Middleware;

use App\Profiles;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

class API
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        if($request->API_TOKEN=='LRD2LF2uJLc8thaxZCA6nxLTByyux2MAP3UJDhHZv7HKCLvzzWZbWs4WmFjv3A2Bt9CZdrcjmeaR7esUPxUB6CxAEaqbtjzENkUxnFUQTbX6cGcBgvXdMFs7QEdXZu65'){
            return $next($request);
        }
        return response()->json(['success'=>false,'message'=>'Missing Token']);
    }
}
