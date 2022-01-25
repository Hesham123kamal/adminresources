<?php

namespace App\Http\Controllers\Admin;

use App\PromotionCode;
use App\Http\Controllers\Controller;
use App\SubscriptionPrices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PromotionCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.promotion_code.view');
    }

    function search(Request $request)
    {

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('auth.promotion_code.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->input();
        $validator = Validator::make($request->all(),
            array(
                'type' => 'required|in:diplomas,life_time,mba',
                'promotion_code' => 'required|string|min:6|unique:mysql2.promotion_code,code',
                'discount' => 'required|numeric|min:1|max:60',
            ));
        if ($validator->fails()) {
//            return redirect()->back()->withErrors($validator->errors())->withInput();
            $message='<div class="alert alert-danger"><ul>';
            foreach ($validator->errors()->all() as $m){
                $message.='<li>'.$m.'</li>';
            }
            $message.='</ul></div>';
            return response()->json(['message'=>$message,'success'=>false])->setCallback($request->input('callback'));
        } else {
            $date=date("Y-m-d H:i:s");
            $code = new PromotionCode();
            $code->type=$data['type'];
            $code->code=$data['promotion_code'];
            $code->discount=$data['discount'];
            $code->period=0;
            $code->expired_date= date('Y-m-d', strtotime("+7 day", strtotime($date)));
            $code->user_id= 0;
            $code->charge_email= '';
            $code->charge_from= 'website';
            $code->charge_date= '0000-00-00 00:00:00';
            $code->take= 0;
            $code->agent_id= 0;
            $code->agent_username= '';
            $code->to_manager= '';
            $code->sales_manager= '';
            if ($code->save()) {
//                Session::flash('success', Lang::get('main.insert') . Lang::get('main.promotion_code')
//                .'<br> '.Lang::get('main.promotion_code_will_expire_after_1_week')
//                .'<br> '.'Code:<span id="code">'.$code->code.'</span><button data-clipboard-target="#code" id="copyCode" class="btn blue btn-outline btn-circle btn-sm"><i class="fa fa-clone"></i></button>');
//                return Redirect::to('admin/promotion_code/create');
                $msg='<div class="alert alert-success">'.Lang::get('main.insert') . Lang::get('main.promotion_code')
                    .'<br> '.Lang::get('main.promotion_code_will_expire_after_1_week')
                    .'<br> '.'Code:<span id="code">'.$code->code.'</span><button type="button" data-clipboard-target="#code" id="copyCode" class="btn blue btn-outline btn-circle btn-sm"><i class="fa fa-clone"></i></button></div>';
                return response()->json(['message'=>$msg,'success'=>true])->setCallback($request->input('callback'));

            }
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }

    public function getPriceByType(Request $request){
        $price='';
        $subscription_prices=SubscriptionPrices::first();
        if($subscription_prices){
            $type=$request->input('type');
            if($type=='diplomas'){
                $price=$subscription_prices->diplomas_prices;
            }elseif ($type=='life_time'){
                $price=$subscription_prices->lifetime_new;

            }elseif ($type=='mba'){
                $price=$subscription_prices->mba;

            }
        }
        return $price;
    }

    public function generateCode(Request $request){
        return generateRandomString(6,'all',false);
    }

}
