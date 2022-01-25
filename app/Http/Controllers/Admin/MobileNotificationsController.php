<?php

namespace App\Http\Controllers\Admin;

use App\MobileNotifications;
use App\MobileUsersNotifications;
use App\NormalUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MobileNotificationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.mobile_notifications.add');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('auth.mobile_notifications.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $data=$request->input();
        $rules=array(
            'title'=>'required',
            'body'=>'required',
        );
        if(!isset($data['to_all'])){
            $rules['send_to']='required';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $tokens=[];
            $mobile_notification=new MobileNotifications();
            $mobile_notification->title=$data['title'];
            $mobile_notification->body=$data['body'];
            $mobile_notification->add_by=Auth::user()->id;
            $mobile_notification->add_date=date('Y-m-d H:i:s');
            $mobile_notification->save();
            if(isset($data['to_all'])){
                $count=NormalUser::where('platform_token','!=','')->whereNotNull('platform_token')->count();
                //dd($count);
                $y=0;
                for ($x=0;$x<=$count;$x+=1000){
                    $y++;
                    $users = NormalUser::skip($x)->take(1000)->where('platform_token','!=','')->whereNotNull('platform_token')->get();
                    foreach ($users as $user) {
                        if (count($user) && $user->platform_token) {
                            $mobile_user_notification = new MobileUsersNotifications();
                            $mobile_user_notification->notification_id = $mobile_notification->id;
                            $mobile_user_notification->user_id = $user->id;
                            $mobile_user_notification->token = $user->platform_token;
                            $mobile_user_notification->add_by = Auth::user()->id;
                            $mobile_user_notification->add_date = date('Y-m-d H:i:s');
                            $mobile_user_notification->save();
                            $tokens[] = $user->platform_token;
                        }
                    }
                }
                //dd($count,$y);

            }
            else {
                foreach ($data['send_to'] as $sendTo) {
                    $user = NormalUser::find($sendTo);
                    if (count($user) && $user->platform_token) {
                        $mobile_user_notification = new MobileUsersNotifications();
                        $mobile_user_notification->notification_id = $mobile_notification->id;
                        $mobile_user_notification->user_id = $sendTo;
                        $mobile_user_notification->token = $user->platform_token;
                        $mobile_user_notification->add_by = Auth::user()->id;
                        $mobile_user_notification->add_date = date('Y-m-d H:i:s');
                        $mobile_user_notification->save();
                        $tokens[] = $user->platform_token;
                    }

                }
            }
            if(count($tokens)){
                foreach (array_chunk($tokens,1000) as $t){
                    //dd($t);
                    fcm()
                        ->to($t) // $recipients must an array
                        ->data([
                            'title' =>$data['title'],
                            'body' =>$data['body'],
                            'sound'=>'default',
                        ])
                        ->notification([
                            'title' =>$data['title'],
                            'body' =>$data['body'],
                            'sound'=>'default',
                        ])
                        ->send();
                }

            }
            Session::flash('success', Lang::get('main.insert').Lang::get('main.mobile_notifications'));
            return Redirect::to('admin/mobile_notifications');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function users(Request $request)
    {
        if($request->get('q')){
            $term=$request->get('q');
            $data = NormalUser::select('id','Email as text')->where('Email','LIKE', "%$term%")->take(50)->get();
            return response()->json($data);
        }
    }
}
