<?php

namespace App\Http\Controllers\Admin;

use App\UsersSuspendLiteversion;
use App\NormalUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UsersSuspendLiteversionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.users_suspend_liteversion.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $users_suspends = UsersSuspendLiteversion::select('users_suspend_lite_version.*','users.Email as user_email')
                                ->join('users','users.id','=','users_suspend_lite_version.user_id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $users_suspends = $users_suspends->where('users_suspend_lite_version.id', '=', "$id");
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $users_suspends = $users_suspends->where('users.Email', 'LIKE', "%$user%");
        }
        if (isset($data['type']) && !empty($data['type'])) {
            $type = $data['type'];
            $users_suspends = $users_suspends->where('users_suspend_lite_version.type', '=', "$type");
        }
        if (isset($data['suspendtime_from']) && !empty($data['suspendtime_from']) && isset($data['suspendtime_to']) && !empty($data['suspendtime_to'])) {
            $suspendtime_from = $data['suspendtime_from'];
            $suspendtime_to = $data['suspendtime_to'];
            $users_suspends = $users_suspends->whereBetween('users_suspend_lite_version.suspendtime', [$suspendtime_from .' 00:00:00', $suspendtime_to.' 23:59:59']);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $users_suspends = $users_suspends->whereBetween('users_suspend_lite_version.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $users_suspends->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'users_suspend_lite_version.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'users_suspend_lite_version.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'users_suspend_lite_version.type';
                break;
            case 3:
                $columnName = 'users_suspend_lite_version.suspendtime';
                break;
            case 4:
                $columnName = 'users_suspend_lite_version.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $users_suspends = $users_suspends->where(function ($q) use ($search) {
                $q->where('users.Email', 'LIKE', "%$search%")
                    ->orWhere('users_suspend_lite_version.type', 'LIKE', "%$search%")
                    ->orWhere('users_suspend_lite_version.id', '=', $search);
            });
        }

        $users_suspends = $users_suspends->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($users_suspends as $users_suspend) {
            $user = $users_suspend->user_email;
            if(PerUser('normal_user_edit') && $user !=''){
                $user= '<a target="_blank" href="' . URL('admin/normal_user/' . $users_suspend->user_id . '/edit') . '">' . $user . '</a>';
            }
            $records["data"][] = [
                $users_suspend->id,
                $user,
                $users_suspend->type,
                $users_suspend->suspendtime,
                $users_suspend->createtime,
                '<div class="btn-group text-center" id="single-order-' . $users_suspend->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('users_suspend_liteversion_edit')) ? '<li>
                                            <a href="' . URL('admin/users_suspend_liteversion/' . $users_suspend->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('users_suspend_liteversion_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $users_suspend->id . '" >
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
        return view('auth.users_suspend_liteversion.add');
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
            'type' => 'required|in:suspend,unsuspend',
            'suspendtime' => 'required|date_format:"Y-m-d"',
        );
        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $users_suspend = new UsersSuspendLiteversion();
            $users_suspend->type = $data['type'];
            $users_suspend->user_id = $user->id;
            $users_suspend->suspendtime=date('Y-m-d H:i:s', strtotime(date("H:i:s"), strtotime($data['suspendtime'])));
            $users_suspend->createtime = date("Y-m-d H:i:s");
            if ($users_suspend->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.users_suspend_liteversion'));
                return Redirect::to('admin/users_suspend_liteversion/create');
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
        $users_suspend = UsersSuspendLiteversion::findOrFail($id);
        $users_suspend->suspendtime = date("Y-m-d", strtotime($users_suspend->suspendtime));
        $user=isset($users_suspend->user)?$users_suspend->user->Email:'';
        return view('auth.users_suspend_liteversion.edit', compact('users_suspend','user'));
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
        $users_suspend = UsersSuspendLiteversion::findOrFail($id);
        $rules=array(
            'user' => 'required',
            'type' => 'required|in:suspend,unsuspend',
            'suspendtime' => 'required|date_format:"Y-m-d"',
        );
        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $users_suspend->type = $data['type'];
            $users_suspend->user_id = $user->id;
            $users_suspend->suspendtime=date('Y-m-d H:i:s', strtotime(date("H:i:s"), strtotime($data['suspendtime'])));

            if ($users_suspend->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.users_suspend_liteversion'));
                return Redirect::to("admin/users_suspend_liteversion/$users_suspend->id/edit");
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
        $users_suspend = UsersSuspendLiteversion::findOrFail($id);
        $users_suspend->delete();
    }

}
