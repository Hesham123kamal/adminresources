<?php

namespace App\Http\Controllers\Admin;

use App\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class StateController extends Controller
{
    public function autoCompleteStates(Request $request)
    {
        if($request->get('query')){
            $query=$request->get('query');
            $data = State::where('arab_name','LIKE', "%$query%")->get();
            $output='<ul id="states-names" class="dropdown-menu"
                    style="display:block; position:relative">';
            foreach ($data as $row){
                $output.='<li><a href="#">'.$row->arab_name.'</a></li>';
            }
            $output.='</ul>';
            echo $output;
        }
    }

}
