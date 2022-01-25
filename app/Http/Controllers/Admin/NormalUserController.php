<?php

namespace App\Http\Controllers\Admin;

use App\NormalUser;
use App\Company;
use App\Country;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class NormalUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies = Company::pluck('name', 'id');
        return view('auth.normal_user.view',compact('companies'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $users = NormalUser::leftjoin('companies','companies.id','=','users.company_id')
           ->select('users.*','companies.id AS company_id','companies.name as company_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $users = $users->where('users.id', '=', $id);
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $users = $users->where('users.FullName', 'LIKE', "%$name%");
        }
        if (isset($data['mobile']) && !empty($data['mobile'])) {
            $mobile = $data['mobile'];
            $users = $users->where('users.Mobile', 'LIKE', "%$mobile%");
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $users = $users->where('users.Email', 'LIKE', "%$email%");
        }
        if (isset($data['suspend'])) {
            $suspend = $data['suspend'];
            $users = $users->where('users.suspend', '=', $suspend);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $users = $users->whereBetween('users.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }
        if (isset($data['exp_date_from']) && !empty($data['exp_date_from']) && isset($data['exp_date_to']) && !empty($data['exp_date_to'])) {
            $exp_date_from = $data['exp_date_from'];
            $exp_date_to = $data['exp_date_to'];
            $users = $users->whereBetween('users.PaymentExpirationDate', [$exp_date_from .' 00:00:00', $exp_date_to.' 23:59:59']);
        }
        if (isset($data['demo_date_from']) && !empty($data['demo_date_from']) && isset($data['demo_date_to']) && !empty($data['demo_date_to'])) {
            $demo_date_from = $data['demo_date_from'];
            $demo_date_to = $data['demo_date_to'];
            $users = $users->whereBetween('users.DemoExpirationDate', [$demo_date_from .' 00:00:00', $demo_date_to.' 23:59:59']);
        }
        if (isset($data['suspend_date_from']) && !empty($data['suspend_date_from']) && isset($data['suspend_date_to']) && !empty($data['suspend_date_to'])) {
            $suspend_date_from = $data['suspend_date_from'];
            $suspend_date_to = $data['suspend_date_to'];
            $users = $users->whereBetween('users.suspend_date', [$suspend_date_from .' 00:00:00', $suspend_date_to.' 23:59:59']);
        }
        if (isset($data['last_login_date_from']) && !empty($data['last_login_date_from']) && isset($data['last_login_date_to']) && !empty($data['last_login_date_to'])) {
            $last_login_date_from = $data['last_login_date_from'];
            $last_login_date_to = $data['last_login_date_to'];
            $users = $users->whereBetween('users.last_login_date', [$last_login_date_from .' 00:00:00', $last_login_date_to.' 23:59:59']);
        }
        if (isset($data['company']) && !empty($data['company'])) {
            $company = $data['company'];
            $users = $users->where('companies.id', '=', "$company");
        }

        $iTotalRecords = $users->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'users.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'users.id';
                break;
            case 1:
                $columnName = 'users.FullName';
                break;
            case 2:
                $columnName = 'users.Mobile';
                break;
            case 3:
                $columnName = 'users.Email';
                break;
            case 4:
                $columnName = 'users.PaymentExpirationDate';
                break;
            case 5:
                $columnName = 'users.DemoExpirationDate';
                break;
            case 6:
                $columnName = 'users.suspend';
                break;
            case 7:
                $columnName = 'users.suspend_date';
                break;
            case 8:
                $columnName = 'users.last_login_date';
                break;
            case 9:
                $columnName = 'users.createdtime';
                break;
            case 10:
                $columnName = 'companies.name';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $users = $users->where(function ($q) use ($search) {
                $q->where('users.FullName','LIKE',"%$search%")
                    ->orWhere('users.Mobile','LIKE',"%$search%")
                    ->orWhere('users.Email','LIKE',"%$search%")
                    ->orWhere('companies.name','LIKE',"%$search%")
                    ->orWhere('users.id', '=', $search);
                });
        }
        $users = $users->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();
        foreach ($users as $user) {
            $user=makeDefaultImageGeneral($user,'image');
            $company = $user->company_name;
            if(PerUser('company_edit') && $company !=''){
                $company= '<a target="_blank" href="' . URL('admin/company/' . $user->company_id . '/edit') . '">' . $company . '</a>';
            }
            $records["data"][]=[
                $user->id,
                $user->FullName,
                $user->Mobile,
                $user->Email,
                $user->PaymentExpirationDate,
                $user->DemoExpirationDate,
                $user->suspend,
                $user->suspend_date,
                $user->last_login_date,
                $user->createdtime,
                $company,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img width="100%" src="' . assetURL($user->image) . '"/></a>'



//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $user->id . '" type="checkbox" ' . ((!PerUser('normal_user_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('normal_user_publish')) ? 'class="changeStatues"' : '') . ' ' . (($user->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $user->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                  ,'<div class="btn-group text-center" id="single-order-' . $user->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('normal_user_edit')) ? '<li>
                                            <a href="' . URL('admin/normal_user/' . $user->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('normal_user_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $user->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('normal_user_reset_password')) ? '<li>
                                            <a class="reset_password" data-id="' . $user->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.reset_password') . '
                                            </a>
                                        </li>' : '') . '


                                    </ul>
                                </div>'
            ];
        }
        if (isset($data["customActionType"]) && $data["customActionType"] == "group_action") {
            $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
            $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        $records['postData'] = $data;
        //return response()->json($data)->setCallback($request->input('callback'));
        return response()->json($records)->setCallback($request->input('callback'));

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countries = Country::pluck('arab_name', 'id');
        $companies = Company::pluck('name', 'id');
        return view('auth.normal_user.add',compact('countries','companies'));
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
        $rules=array(
            'name' => 'required',
            'email' => 'required|unique:mysql2.users,Email',
            'mobile' => 'required|unique:mysql2.users,Mobile',
            'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
            'type' => 'required|in:person,company',
            'country' => 'required|exists:mysql2.country,id',
            'type_of_subscribe' => 'required|in:default,annual,percourse,diplomas',
            'user_type' => 'required|in:corporate,individual',
        );
        if( isset($data['type']) && $data['type']=='company'){
            $rules['company'] = 'required|exists:mysql2.companies,id';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('image');
            $picName = uploadFileToE3melbusiness($pic);
            $user = new NormalUser();
            $user->FullName = $data['name'];
            $user->Email = $data['email'];
            $user->Mobile = $data['mobile'];
            $user->password = $data['mobile'];
            $user->country = $data['country'];
            $user->type = $data['type'];
            if($data['type']=='company'){
                $user->company_id = $data['company'];
            }
            $user->type_of_subscribe = $data['type_of_subscribe'];
            $user->user_type = $data['user_type'];
            $user->facebook = $data['facebook'];
            $user->linkedin = $data['linkedin'];
            $user->twitter = $data['twitter'];
            $user->google = $data['google'];
            $user->PaymentExpirationDate = $data['payment_expiration_date'];
            $user->DemoExpirationDate = $data['demo_expiration_date'];
            //$user->published = $published;
            $user->image = $picName;
            $user->createdtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $user->published_by = Auth::user()->id;
//                $user->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $user->unpublished_by = Auth::user()->id;
//                $user->unpublished_date = date("Y-m-d H:i:s");
//            }
            $user->lastedit_by = Auth::user()->id;
            $user->added_by = Auth::user()->id;
            $user->lastedit_date = date("Y-m-d H:i:s");
            $user->added_date = date("Y-m-d H:i:s");
            if ($user->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.normal_user'));
                return Redirect::to('admin/normal_user/create');
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
        $user = NormalUser::findOrFail($id);
        $countries = Country::pluck('arab_name', 'id');
        $companies = Company::pluck('name', 'id');
        $user=makeDefaultImageGeneral($user,'image');
        $user->DemoMedicalExpirationDate = date("Y-m-d", strtotime($user->DemoMedicalExpirationDate));
        return view('auth.normal_user.edit',compact('user','countries','companies'));
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
        $data = $request->input();
        $user = NormalUser::findOrFail($id);
        $rules=array(
            'name' => 'required',
            'email' => "required|unique:mysql2.users,Email,$id,id",
            'mobile' => "required|unique:mysql2.users,Mobile,$id,id",
            'type' => 'required|in:person,company',
            'country' => 'required|exists:mysql2.country,id',
            'type_of_subscribe' => 'required|in:default,annual,percourse,diplomas',
            'user_type' => 'required|in:corporate,individual',
        );
        if ( $request->file('image')){
            $rules['image'] = 'mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        if( isset($data['type']) && $data['type']=='company'){
            $rules['company'] = 'required|exists:mysql2.companies,id';
        }
        if(PerUser('normal_user_show_password')){
            $rules['password'] = 'required';
        }
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $user->FullName = $data['name'];
            $user->Email = $data['email'];
            $user->Mobile = $data['mobile'];
            $user->country = $data['country'];
            $user->type = $data['type'];
            $user->type_of_subscribe = $data['type_of_subscribe'];
            $user->user_type = $data['user_type'];
            if(PerUser('normal_user_show_password')) {
                $user->Password = $data['password'];
            }

            if($data['type']=='company'){
                $user->company_id = $data['company'];
            }
            elseif($data['type']=='person'){
                $user->company_id = 0;
            }
            if(PerUser('normal_user_edit_sponserid')) {
                $user->sponsorId = $data['sponsorId'];
            }

            $user->facebook = $data['facebook'];
            $user->linkedin = $data['linkedin'];
            $user->twitter = $data['twitter'];
            $user->google = $data['google'];
            $user->PaymentExpirationDate = $data['payment_expiration_date'];
            $user->DemoExpirationDate = $data['demo_expiration_date'];
            $user->DemoMedicalExpirationDate=date('Y-m-d H:i:s', strtotime(date("H:i:s"), strtotime( $data['demo_medical_expiration_date'])));
            if ( $request->file('image')){
                $pic = $request->file('image');
                $picName = uploadFileToE3melbusiness($pic);
                $user->image = $picName;
            }
//            if ($published == 'yes' && $user->published=='no') {
//                $user->published_by = Auth::user()->id;
//                $user->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $user->published=='yes') {
//                $user->unpublished_by = Auth::user()->id;
//                $user->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $user->published = $published;
            $user->lastedit_by = Auth::user()->id;
            $user->lastedit_date = date("Y-m-d H:i:s");
            if ($user->save()){
                Session::flash('success', Lang::get('main.update') . Lang::get('main.normal_user'));
                return Redirect::to("admin/normal_user/$user->id/edit");
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = NormalUser::findOrFail($id);
        $user->deleted_at=date("Y-m-d H:i:s");
        $user->save();
        //$user->delete();
    }

    public function resetPassword(Request $request)
    {
        $id = $request->input('id')!==null ? $request->input('id') : null;
        $user = NormalUser::findOrFail($id);
        $user->Password=$user->Mobile;
        $user->save();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $user = NormalUser::findOrFail($id);
//            if ($published == 'no') {
//                $user->published = 'no';
//                $user->unpublished_by = Auth::user()->id;
//                $user->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $user->published = 'yes';
//                $user->published_by = Auth::user()->id;
//                $user->published_date = date("Y-m-d H:i:s");
//            }
//            $user->save();
//        } else {
//            return redirect(404);
//        }
//    }

    public function autoCompleteUsers(Request $request)
    {
        if($request->get('query')){
            $query=$request->get('query');
            $data = NormalUser::where('Email','LIKE', "%$query%")->take(10)->get();
            $output='<ul id="users-emails" class="dropdown-menu"
                    style="display:block; position:relative">';
            foreach ($data as $row){
                $output.='<li><a href="#">'.$row->Email.'</a></li>';
            }
            $output.='</ul>';
            echo $output;
        }
    }
}
