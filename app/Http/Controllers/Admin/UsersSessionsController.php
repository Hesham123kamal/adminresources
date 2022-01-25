<?php

namespace App\Http\Controllers\Admin;

use App\SessionUsers;
use App\Http\Controllers\Controller;
use App\NormalUser;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class UsersSessionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.users_sessions.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $users_sessions = SessionUsers::leftjoin('users','users.id','=','session_users.user_id')
            ->select('session_users.*','users.Email as user_email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $users_sessions = $users_sessions->where('session_users.id', '=', $id);
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $users_sessions = $users_sessions->where('users.Email','LIKE', "%$user%");
        }
        if (isset($data['ip_address']) && !empty($data['ip_address'])) {
            $ip_address = $data['ip_address'];
            $users_sessions = $users_sessions->where('session_users.ip_address','LIKE', "%$ip_address%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $users_sessions = $users_sessions->whereBetween('session_users.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }
        if (isset($data['deleted_time_from']) && !empty($data['deleted_time_from']) && isset($data['deleted_time_to']) && !empty($data['deleted_time_to'])) {
            $deleted_time_from = $data['deleted_time_from'];
            $deleted_time_to = $data['deleted_time_to'];
            $users_sessions = $users_sessions->whereBetween('session_users.deleted_at', [$deleted_time_from .' 00:00:00', $deleted_time_to.' 23:59:59']);
        }

        $iTotalRecords = $users_sessions->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'session_users.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'session_users.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'session_users.ip_address';
                break;
            case 3:
                $columnName = 'session_users.createdtime';
                break;
            case 4:
                $columnName = 'session_users.deleted_at';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $users_sessions = $users_sessions->where(function ($q) use ($search) {
                $q->where('session_users.id', '=', $search)
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('session_users.ip_address', 'LIKE', "%$search%");
            });
        }

        $users_sessions = $users_sessions->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($users_sessions as $users_session) {
            $user_email = $users_session->user_email;
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $users_session->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $users_session->id,
                $user_email,
                $users_session->ip_address,
                $users_session->createdtime,
                $users_session->deleted_at,
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
