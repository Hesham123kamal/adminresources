<?php

namespace App\Http\Controllers\Admin;

use App\Profiles;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Builder\Use_;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.users.view');
    }
    public function search(Request $request)
    {
        $data = $request->input();
        $users = User::select('admin_system_users.*','admin_system_profiles.name as profile_name')->join('admin_system_profiles','admin_system_profiles.id','=','admin_system_users.profile_id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $users = $users->where('id', '=', $id);
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $users = $users->where('name', 'LIKE', "%$name%");
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $users = $users->where('email', 'LIKE', "%$email%");
        }
        if (isset($data['profile']) && !empty($data['profile'])) {
            $profile = $data['profile'];
            $users = $users->where('admin_system_profiles.name', 'LIKE', "%$profile%");
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
        $columnName = 'id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'id';
                break;
            case 1:
                $columnName = 'name';
                break;
            case 3:
                $columnName = 'email';
                break;
            case 4:
                $columnName = 'admin_system_profiles.name';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $users = $users->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%")
                    ->orWhere('admin_system_profiles.name', 'LIKE', "%$search%")
                    ->orWhere('id', '=', $search);
            });
        }

        $users = $users->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($users as $user) {
            $user=makeDefaultImage($user,'Users');
            $records["data"][] = [
                $user->id,
                $user->name,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img width="30%" src="' . asset($user->img_dir.$user->img) . '"/></a>',
                $user->email,
                $user->profile_name,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $user->id . '" type="checkbox" ' . ((!PerUser('users_active')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('users_active')) ? 'class="changeStatues"' : '') . ' ' . (($user->active==1) ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $user->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $user->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('users_edit')) ? '<li>
                                            <a href="' . URL('admin/users/' . $user->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('users_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $user->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
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
        //
        $profiles=Profiles::where('active',1)->get();
        $projects=User::where('active',1)->get();
        return view('auth.users.add',compact('profiles','projects'));

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
        $validator = Validator::make($request->all(),
            array(
                'profile_id'=>'required',
                'name'=>'required',
                'email'=>'required|unique:admin_system_users,email',
                'username'=>'required|unique:admin_system_users,username',
                'password'=>'required',
                'confirm_password'=>'required|same:password',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $active=(isset($data['active']))?1:0;
            $users=new User();
            $users->profile_id=$data['profile_id'];
            $users->name=$data['name'];
            $users->email=$data['email'];
            $users->username=$data['username'];
            $users->password=Hash::make($data['password']);
            if(Input::hasFile('image')){
                $validator = Validator::make($request->all(),array(
                    'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000'
                ));
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator->errors())->withInput();
                }else{
                    $file=$request->file('image');
                    $image=FileImage($file,'Users');
                    $users->img=$image['img'];
                    $users->img_dir=$image['img_dir'];
                }
            }
            $users->active=$active;
            if($active==1){
                $users->active_by=Auth::user()->id;
                $users->active_date=date("Y-m-d H:i:s");
            }
            if($active==0){
                $users->unactive_by=Auth::user()->id;
                $users->unactive_date=date("Y-m-d H:i:s");
            }
            $users->add_by=Auth::user()->id;
            $users->add_date=date("Y-m-d H:i:s");
            if($users->save()){
                Session::flash('success', Lang::get('main.insert').Lang::get('main.users'));
                return Redirect::to('admin/users/create');
            }
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
        return abort(404);
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
        $post=User::where('id','!=',Auth::user()->id)->where('id','!=',1)->find($id);
        if(count($post)){
            $post=makeDefaultImage($post,'Users');
            $profiles=Profiles::where('active',1)->get();
            $projects=User::where('active',1)->get();
            return view('auth.users.edit',compact('post','profiles','projects'));
        }else{
            return abort(404);
        }

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
        $data=$request->input();
        $users= User::find($id);
        if(count($users)){
            $validator = Validator::make($request->all(),
                array(
                    'name'=>'required',
                    'email'=>'required|unique:admin_system_users,email,'.$id,
                    'username'=>'required|unique:admin_system_users,username,'.$id,
                ));
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            }else {
                if(!empty($data['password'])){
                    $validator = Validator::make($request->all(),
                        array(
                            'password'=>'required|min:6',
                            'confirm_password'=>'required|same:password',
                        ));
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator->errors())->withInput();
                    }else {
                        $users->password=Hash::make($data['password']);
                    }
                }
                if(Input::hasFile('image')){
                    $validator = Validator::make($request->all(),array(
                        'image' => 'mimes:jpeg,jpg,png,gif|required|max:5000'
                    ));
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator->errors())->withInput();
                    }else{
                        if(file_exists(public_path().$users->img_dir.$users->img)&&!empty($users->img_dir)){
                            unlink(public_path().$users->img_dir.$users->img);
                        }
                        if(file_exists(public_path().$users->img_dir.'thumbnail/thumbnail_'.$users->img)&&!empty($users->img_dir)){
                            unlink(public_path().$users->img_dir.'thumbnail/thumbnail_'.$users->img);
                        }
                        $file=$request->file('image');
                        $image=FileImage($file,'Users');
                        $users->img=$image['img'];
                        $users->img_dir=$image['img_dir'];
                    }
                }
                $active=(isset($data['active']))?1:0;
                $users->profile_id=$data['profile_id'];
                $users->name=$data['name'];
                $users->email=$data['email'];
                $users->username=$data['username'];
                if($active==1&&$users->active==0){
                    $users->active_by=Auth::user()->id;
                    $users->active_date=date("Y-m-d H:i:s");
                }
                if($active==0&&$users->active==1){
                    $users->unactive_by=Auth::user()->id;
                    $users->unactive_date=date("Y-m-d H:i:s");
                }

                $users->active=$active;
                $users->lastedit_by=Auth::user()->id;
                $users->lastedit_date=date("Y-m-d H:i:s");
                if($users->save()){
                    Session::flash('success', Lang::get('main.update').Lang::get('main.users'));
                    return Redirect::to('admin/users/'.$id.'/edit');
                }
            }
        }else{
            return abort(404);
        }

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
        $users=User::find($id);
        if(count($users)){
            $users->delete();
//            if($users->delete()){
//                $users->deleted_by=Auth::user()->id;
//                $users->save();
//            }
        }
    }
    public function activation(Request $request){
        if($request->ajax()){
            $id=$request->input('id');
            $active=$request->input('active');
            $users=User::find($id);
            if ($active == 0) {
                $users->active = 0;
                $users->unactive_by = Auth::user()->id;
                $users->unactive_date = date("Y-m-d H:i:s");
            } elseif ($active == 1) {
                $users->active = 1;
                $users->active_by = Auth::user()->id;
                $users->active_date = date("Y-m-d H:i:s");
            }
            $users->save();
        }else{
            return redirect(404);
        }
    }
}
