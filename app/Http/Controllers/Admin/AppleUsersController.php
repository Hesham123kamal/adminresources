<?php

namespace App\Http\Controllers\Admin;

use App\NormalUser;
use App\AppleUsers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class AppleUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.apple_users.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $apple_users = AppleUsers::leftjoin('users','users.id','=','apple_users.user_id')
            ->select('apple_users.*','users.Email as user_email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $apple_users = $apple_users->where('apple_users.id', '=', $id);
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $apple_users = $apple_users->where('users.Email', '=', $user);
        }
        if (isset($data['code']) && !empty($data['code'])) {
            $code = $data['code'];
            $apple_users = $apple_users->where('apple_users.apple_charge_transaction_code', 'LIKE', "%$code%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $apple_users = $apple_users   ->whereBetween('apple_users.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $apple_users->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'apple_users.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'apple_users.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'apple_users.apple_charge_transaction_code';
                break;
            case 3:
                $columnName = 'apple_users.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $apple_users = $apple_users->where(function ($q) use ($search) {
                $q->where('apple_users.id', '=', $search)
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('apple_users.apple_charge_transaction_code', 'LIKE', "%$search%");
            });
        }

        $apple_users = $apple_users->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($apple_users as $apple_user) {
            $user_email = $apple_user->user_email;
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $apple_user->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $apple_user->id,
                $user_email,
                $apple_user->apple_charge_transaction_code,
                $apple_user->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $apple_user->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('apple_users_edit')) ? '<li>
                                            <a href="' . URL('admin/apple_users/' . $apple_user->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('apple_users_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $apple_user->id . '" >
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
        return view('auth.apple_users.add');
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
            'user' => 'required',
            'apple_charge_transaction_code' => 'required',
        );
        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $apple_user= new AppleUsers();
            $apple_user->apple_charge_transaction_code = $data['apple_charge_transaction_code'];
            $apple_user->user_id = $user->id;
            $apple_user->createdtime = date("Y-m-d H:i:s");
            if ($apple_user->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.apple_users'));
                return Redirect::to('admin/apple_users/create');
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
        $apple_user = AppleUsers::findOrFail($id);
        $user=NormalUser::where('id', $apple_user->user_id)->first();
        $user=($user!==null)?$user->Email:'';
        return view('auth.apple_users.edit', compact('apple_user','user'));
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
        $apple_user = AppleUsers::findOrFail($id);
        $rules=array(
            'apple_charge_transaction_code' => 'required',
        );

        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $apple_user->user_id = $user->id;
            $apple_user->apple_charge_transaction_code = $data['apple_charge_transaction_code'];
            if ($apple_user->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.apple_users'));
                return Redirect::to("admin/apple_users/$apple_user->id/edit");
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
        $apple_user = AppleUsers::findOrFail($id);
        $apple_user->delete();
    }

}
