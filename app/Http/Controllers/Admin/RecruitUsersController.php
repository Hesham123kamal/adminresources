<?php

namespace App\Http\Controllers\Admin;

use App\Recruit;
use App\RecruitUsers;
use App\Company;
use App\Country;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class RecruitUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.recruit_users.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $users = RecruitUsers::select('recruit_users.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $users = $users->where('recruit_users.id', '=', $id);
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $users = $users->where('recruit_users.fullname', 'LIKE', "%$name%");
        }
        if (isset($data['mobile']) && !empty($data['mobile'])) {
            $mobile = $data['mobile'];
            $users = $users->where('recruit_users.mobile', 'LIKE', "%$mobile%");
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $users = $users->where('recruit_users.email', 'LIKE', "%$email%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $users = $users->whereBetween('recruit_users.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
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
        $columnName = 'recruit_users.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'recruit_users.id';
                break;
            case 1:
                $columnName = 'recruit_users.fullname';
                break;
            case 2:
                $columnName = 'recruit_users.mobile';
                break;
            case 3:
                $columnName = 'recruit_users.email';
                break;
            case 4:
                $columnName = 'recruit_users.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $users = $users->where(function ($q) use ($search) {
                $q->where('recruit_users.fullname','LIKE',"%$search%")
                    ->orWhere('recruit_users.mobile','LIKE',"%$search%")
                    ->orWhere('recruit_users.email','LIKE',"%$search%")
                    ->orWhere('recruit_users.id', '=', $search);
            });
        }
        $users = $users->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();
        foreach ($users as $user) {
            $user=makeDefaultImageGeneral($user,'image');
            $records["data"][] = [
                $user->id,
                $user->fullname,
                $user->mobile,
                $user->email,
                $user->createdtime,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img width="50%" src="' . assetURL($user->image) . '"/></a>',
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $user->id . '" type="checkbox" ' . ((!PerUser('recruit_users_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('recruit_users_publish')) ? 'class="changeStatues"' : '') . ' ' . (($user->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $user->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $user->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('recruit_users_edit')) ? '<li>
                                            <a href="' . URL('admin/recruit_users/' . $user->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('recruit_users_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $user->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('recruit_users_reset_password')) ? '<li>
                                            <a class="reset_password" data-id="' . $user->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.reset_password') . '
                                            </a>
                                        </li>' : '') . '


                                    </ul>
                                </div>',
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
        $recruits = Recruit::pluck( 'id');
        return view('auth.recruit_users.add',compact('countries','companies','recruits'));
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
            'email' => 'required|unique:mysql2.recruit_users,email',
            'mobile' => 'required|unique:mysql2.recruit_users,mobile',
            'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000',
            'type' => 'required|in:person,company',
            'country' => 'required|exists:mysql2.country,id',
            'type_of_subscribe' => 'required|in:default,annual,percourse',
            'user_type' => 'required|in:corporate,individual',
            'recruit_id' => 'required|exists:mysql2.recruit,id',
            'field' => 'required',
        );
        if( isset($data['type']) && $data['type']=='company'){
            $rules['company'] = 'required|exists:mysql2.companies,id';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $pic = $request->file('image');
            $picName = uploadFileToE3melbusiness($pic);
            $user = new RecruitUsers();
            $user->fullname = $data['name'];
            $user->email = $data['email'];
            $user->mobile = $data['mobile'];
            $user->country = Country::where('id', $data['country'])->first()->arab_name;
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
            $user->field = $data['field'];
            $user->recruit_id = $data['recruit_id'];
            $user->published = $published;
            $user->image = $picName;
            $user->createdtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $user->published_by = Auth::user()->id;
                $user->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $user->unpublished_by = Auth::user()->id;
                $user->unpublished_date = date("Y-m-d H:i:s");
            }
            $user->lastedit_by = Auth::user()->id;
            $user->added_by = Auth::user()->id;
            $user->lastedit_date = date("Y-m-d H:i:s");
            $user->added_date = date("Y-m-d H:i:s");
            if ($user->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.recruit_user'));
                return Redirect::to('admin/recruit_users/create');
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
        $user = RecruitUsers::findOrFail($id);
        $countries = Country::pluck('arab_name', 'id');
        $companies = Company::pluck('name', 'id');
        $recruits = Recruit::pluck( 'id');
        return view('auth.recruit_users.edit',compact('user','countries','companies','recruits'));
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
        $user = RecruitUsers::findOrFail($id);
        $rules=array(
            'name' => 'required',
            'email' => "required|unique:mysql2.recruit_users,email,$id,id",
            'mobile' => "required|unique:mysql2.recruit_users,mobile,$id,id",
            'type' => 'required|in:person,company',
            'country' => 'required|exists:mysql2.country,id',
            'type_of_subscribe' => 'required|in:default,annual,percourse',
            'user_type' => 'required|in:corporate,individual',
            'recruit_id' => 'required|exists:mysql2.recruit,id',
            'field' => 'required',
        );
        if ( $request->file('image')){
            $rules['image'] = 'mimes:jpeg,jpg,png,gif|required|max:5000';
        }
        if( isset($data['type']) && $data['type']=='company'){
            $rules['company'] = 'required|exists:mysql2.companies,id';
        }
        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $user->fullname = $data['name'];
            $user->email = $data['email'];
            $user->mobile = $data['mobile'];
            $user->country = Country::where('id', $data['country'])->first()->arab_name;
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
            $user->field = $data['field'];
            $user->recruit_id = $data['recruit_id'];
            if ( $request->file('image')){
                $pic = $request->file('image');
                $picName = uploadFileToE3melbusiness($pic);
                $user->image = $picName;
            }
            if ($published == 'yes' && $user->published=='no') {
                $user->published_by = Auth::user()->id;
                $user->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $user->published=='yes') {
                $user->unpublished_by = Auth::user()->id;
                $user->unpublished_date = date("Y-m-d H:i:s");
            }
            $user->published = $published;
            $user->lastedit_by = Auth::user()->id;
            $user->lastedit_date = date("Y-m-d H:i:s");
            if ($user->save()){
                Session::flash('success', Lang::get('main.update') . Lang::get('main.recruit_user'));
                return Redirect::to("admin/recruit_users/$user->id/edit");
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
        $user = RecruitUsers::findOrFail($id);
        $user->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $user = RecruitUsers::findOrFail($id);
            if ($published == 'no') {
                $user->published = 'no';
                $user->unpublished_by = Auth::user()->id;
                $user->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $user->published = 'yes';
                $user->published_by = Auth::user()->id;
                $user->published_date = date("Y-m-d H:i:s");
            }
            $user->save();
        } else {
            return redirect(404);
        }
    }
}
