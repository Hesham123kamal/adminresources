<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MbaTemplatesController extends Controller
{
    //
    public function index(){
        return view('auth.send_mba_templates');
    }
}
