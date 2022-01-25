<?php

namespace App\Http\Middleware;

use App\Profiles;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

class PermissionsAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    private $_adminRoute='admin';

    public function getRoute(){
        return Route::current()->getName();
    }

    public function handle($request, Closure $next)
    {
        if(auth()->check()){
            if (isset($_SERVER['SCRIPT_URI']) && strpos($_SERVER['SCRIPT_URI'], 'index.php/') !== false) {
                return redirect(str_replace('index.php/','',$_SERVER['SCRIPT_URI']));
            }
            $permissions = Auth::user()->Profile->permissions;
            $permissions=json_decode($permissions);
            if(count((array)$permissions)){
                $route=$this->getRoute();
                //print_r($permissions);
                //echo$route;
                if(isset($permissions->$route)&&$permissions->$route||($request->path()=='admin')){
                    $system=userSystem();
                    App::setLocale($system->backend_lang);
                    $request -> attributes->add(['UserPermissionsData' => $permissions,'UserSystem'=>$system]);
                    return $next($request);
                }else{
                    return abort(505);
                }
            }

        }else{
            return Redirect::to($this->_adminRoute.'/login');
        }
    }
}
