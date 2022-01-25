<?php

namespace App\Http\Controllers\Admin;

use App\ChargeTransaction;
use App\NormalUser;
use App\Employee;
use App\Http\Controllers\Controller;
use App\UsersSuspend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserssuspendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.users_suspend');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $users = UsersSuspend::leftjoin('users','users.id','=','users_suspend.user_id')
            ->select('users_suspend.*','users.Email as user_email','users.FullName as user_name','users.Mobile as user_mobile');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $users = $users->where('users_suspend.id', '=', $id);
        }
        if (isset($data['name']) && !empty($data['user'])) {
            $user = $data['user'];
            $users = $users->where('users.FullName','LIKE', "%$user%");
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $users = $users->where('users.Email','LIKE', "%$email%");
        }
        if (isset($data['mobile']) && !empty($data['mobile'])) {
            $mobile = $data['mobile'];
            $users = $users->where('users.Mobile','LIKE', "%$mobile%");
        }
        if (isset($data['suspend']) && !empty($data['suspend'])) {
            $suspend = $data['suspend'];
            $users = $users->where('users_suspend.type','LIKE', "$suspend");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $users = $users->whereBetween('users_suspend.suspendtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
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
        $columnName = 'users_suspend.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'users_suspend.id';
                break;
            case 1:
                $columnName = 'users.FullName';
                break;
            case 2:
                $columnName = 'users.Email';
                break;
            case 3:
                $columnName = 'users.Mobile';
                break;
            case 4:
                $columnName = 'users_suspend.type';
                break;
            case 5:
                $columnName = 'users_suspend.suspendtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $users = $users->where(function ($q) use ($search) {
                $q->where('users.FullName', 'LIKE', "%$search%")
                    ->orWhere('users.Mobile', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('users_suspend.type', '=', $search)
                    ->orWhere('users_suspend.id', '=', $search);
            });
        }

        $users = $users->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($users as $user) {
            $user_email = $user->user_email;
            if(PerUser('users_suspend') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $user->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $user->id,
                $user->user_name,
                $user_email,
                $user->user_mobile,
                $user->type,
                $user->suspendtime,
                '',

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


}
