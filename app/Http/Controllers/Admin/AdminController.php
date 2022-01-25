<?php

namespace App\Http\Controllers\Admin;

use App\Diplomas;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use App\Http\Requests\SEOSiteMapRequest;
use App\UsersSystems;
use App\AppSettings;
use App\SEOSiteMap;
use App\SubscriptionPrices;
use App\User;

class AdminController extends Controller
{
    //
    public function __construct() {
        $this->middleware('auth');
    }

    public function home() {
        return view('auth.home');
    }

    public function profile() {
        return view('auth.users.profile');
    }

    public function profilePost(Request $request) {
        $data = $request->input();
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'email' => 'required|unique:admin_system_users,email,' . Auth::id(),
                'username' => 'required|unique:admin_system_users,username,' . Auth::id(),
                'old_password' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            if (Hash::check($request->input('old_password'), Auth::user()->password)) {
                $user = User::find(Auth::user()->id);
                if ($request->hasFile('image')) {
                    $rules = array('image' => 'required|image|mimes:jpeg,gif,bmp,png');
                    $validator = Validator::make(array('image' => $request->file('image')), $rules);
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator->errors())->withInput();
                    }
                    if (file_exists(public_path() . $user->img_dir . $user->img)) {
                        unlink(public_path() . $user->img_dir . $user->img);
                    }
                    if (file_exists(public_path() . $user->img_dir . 'thumbnail/thumbnail_' . $user->img)) {
                        unlink(public_path() . $user->img_dir . 'thumbnail/thumbnail_' . $user->img);
                    }
                    $file = $request->file('image');
                    $image = FileImage($file, 'Users');
                    $user->img = $image['img'];
                    $user->img_dir = $image['img_dir'];
                }
                $user->name = $request->input('name');
                $user->username = $request->input('username');
                $user->email = $request->input('email');
                if (!empty($request->input('new_password'))) {
                    $user->password = Hash::make($request->input('new_password'));
                }
                if ($user->save()) {
                    Session::flash('success', Lang::get('main.updated'));
                    return Redirect::to('admin/profile');
                }
            } else {
                Session::flash('error', Lang::get('main.error_user_password'));
                return Redirect::to('admin/profile');
            }
        }
    }

    public function system() {
        $system = UsersSystems::where('user_id', Auth::user()->id)->first();
        if (!count((array)$system)) {
            $system = new UsersSystems();
            $system->user_id = Auth::user()->id;
            $system->save();
        }
        return view('auth.settings.system', compact('system'));
    }

    public function systemPost(Request $request) {
        $system = UsersSystems::where('user_id', Auth::user()->id)->first();
        if (!count($system)) {
            $system = new UsersSystems();
            $system->user_id = Auth::user()->id;
            $system->save();
        }

        $data = $request->input();;
        $validator = Validator::make($request->all(),
            array(
                'backend_lang' => 'required',
                'backend_color' => 'required',
                'backend_layout' => 'required',
                'backend_header' => 'required',
                'backend_top_menu_dropdown' => 'required',
                'backend_sidebar_menu_mode' => 'required',
                /*'backend_sidebar_menu_sub_show'=>'required',*/
                'backend_sidebar_menu_style' => 'required',
                'backend_sidebar_menu_position' => 'required',
                'backend_footer' => 'required',

            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $system->backend_lang = $data['backend_lang'];
            $system->backend_color = $data['backend_color'];
            $system->backend_layout = $data['backend_layout'];
            $system->backend_header = $data['backend_header'];
            $system->backend_top_menu_dropdown = $data['backend_top_menu_dropdown'];
            $system->backend_sidebar_menu_mode = $data['backend_sidebar_menu_mode'];
            /*$system->backend_sidebar_menu_sub_show=$data['backend_sidebar_menu_sub_show'];*/
            $system->backend_sidebar_menu_style = $data['backend_sidebar_menu_style'];
            $system->backend_sidebar_menu_position = $data['backend_sidebar_menu_position'];
            $system->backend_footer = $data['backend_footer'];
            $system->lastedit_by = Auth::user()->id;
            $system->lastedit_date = date('Y-m-d H:i:s');
            if ($system->save()) {
                Session::flash('success', Lang::get('main.updated'));
                return Redirect::to('admin/system');
            }
        }
    }

    public function AppSettings() {
        $app_settings = AppSettings::find(1);
        return view('auth.app_settings.edit', compact('app_settings'));
    }

    public function AppSettingsEdit(Request $request) {
        $data = $request->input();
        $app_settings = AppSettings::find(1);
        $validator = Validator::make($request->all(),
            array(
                'easy_question_percentage' => 'required|max:3',
                'normal_question_percentage' => 'required|max:3',
                'hard_question_percentage' => 'required|max:3',
                'dollar_rate' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $max = 100;
            $app_settings->easy_question_percentage = $data['easy_question_percentage'];
            $app_settings->normal_question_percentage = $data['normal_question_percentage'];
            $app_settings->hard_question_percentage = $data['hard_question_percentage'];
            $app_settings->dollar_rate = $data['dollar_rate'];
            if (($data['easy_question_percentage'] + $data['normal_question_percentage'] + $data['hard_question_percentage']) != $max) {
                $message = 'The three inputs must be equal 100%';
                return redirect()->back()->withErrors($message)->withInput();
            } else {
                if ($app_settings->save()) {
                    Session::flash('success', Lang::get('main.updated'));
                    return Redirect::to('admin/app_settings');
                }
            }
        }
    }

    public function SEOStieMap() {
        $seo_sitemap = SEOSiteMap::all();
        return view('auth.seo_sitemap.seo_sitemap', compact('seo_sitemap'));
    }

    public function SEOStieMapEdit(SEOSiteMapRequest $request) {
        foreach ($request->input('priority') as $key => $value) {
            SEOSiteMap::where('name', $key)->update(['priority' => $value]);
        }

        return response()->json('success', 200);
    }

    public function subscriptionPrices() {
        $subscription_prices = SubscriptionPrices::find(1);
        $diplomas= Diplomas::select('id','name','egy_price','ksa_price')->get();
        return view('auth.subscription_prices.edit', compact('subscription_prices','diplomas'));
    }

    public function subscriptionPricesEdit(Request $request) {
        $data = $request->input();
        $subscription_prices = SubscriptionPrices::find(1);
        $validator = Validator::make($request->all(),
            array(
                'diplomas_prices' => 'required|numeric',
                'lifetime_new' => 'required|numeric',
                'lifetime_renew' => 'required|numeric',
                'mba' => 'required|numeric',
                'diploma_price.*' => 'required|numeric',
            ),
            array(
                'diploma_price.*.required'=>'The diplomas prices are required',
                'diploma_price.*.numeric'=>'The diploma price must be a number',
            )
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $subscription_prices->diplomas_prices = $data['diplomas_prices'];
            $subscription_prices->lifetime_new = $data['lifetime_new'];
            $subscription_prices->lifetime_renew = $data['lifetime_renew'];
            $subscription_prices->mba = $data['mba'];
            if ($subscription_prices->save()) {
                foreach ($data['diploma_price'] as $id=>$price){
                    $diploma=Diplomas::find($id);
                    if($diploma) {
                        $diploma->ksa_price = $price;
                        $diploma->save();
                    }
                }
                Session::flash('success', Lang::get('main.updated'));
                return Redirect::to('admin/subscription_prices');
            }

        }
    }
}
