<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\NormalUser;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class BlockedUsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.blocked_users.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $users = NormalUser::select('users.*')->whereNotNull('block_date_to');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $users = $users->where('users.id', '=', $id);
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $users = $users->where('users.Email','LIKE', "%$email%");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $users = $users->where('users.FullName','LIKE', "%$name%");
        }
        if (isset($data['phone']) && !empty($data['phone'])) {
            $phone = $data['phone'];
            $users = $users->where('users.Mobile','LIKE', "%$phone%");
        }
        if (isset($data['block_count']) && !empty($data['block_count'])) {
            $block_count = $data['block_count'];
            $users = $users->where('users.block_count','=', "$block_count");
        }
        if (isset($data['block_date_from']) && !empty($data['block_date_from']) && isset($data['block_date_to']) && !empty($data['block_date_to'])) {
            $block_date_from = $data['block_date_from'];
            $block_date_to = $data['block_date_to'];
            $users = $users->whereBetween('users.block_date_to', [$block_date_from .' 00:00:00', $block_date_to.' 23:59:59']);
        }
        if (isset($data['last_login_date_from']) && !empty($data['last_login_date_from']) && isset($data['last_login_date_to']) && !empty($data['last_login_date_to'])) {
            $last_login_date_from = $data['last_login_date_from'];
            $last_login_date_to = $data['last_login_date_to'];
            $users = $users->whereBetween('users.last_login_date', [$last_login_date_from .' 00:00:00', $last_login_date_to.' 23:59:59']);
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
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'users.FullName';
                break;
            case 3:
                $columnName = 'users.Mobile';
                break;
            case 4:
                $columnName = 'users.block_count';
                break;
            case 5:
                $columnName = 'users.block_date_to';
                break;
            case 6:
                $columnName = 'users.last_login_date';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $users = $users->where(function ($q) use ($search) {
                $q->where('users.id', '=', $search)
                    ->orWhere('users.block_count', '=', $search)
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('users.FullName', 'LIKE', "%$search%")
                    ->orWhere('users.Mobile', 'LIKE', "%$search%")
                    ->orWhere('users.block_count', '=', $search);
            });
        }

        $users = $users->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($users as $user) {
            $user_email = $user->Email;
            $user_name = $user->FullName;
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $user->id . '/edit') . '">' . $user_email . '</a>';
            }
            if(PerUser('normal_user_edit') && $user_name !=''){
                $user_name= '<a target="_blank" href="' . URL('admin/normal_user/' . $user->id . '/edit') . '">' . $user_name . '</a>';
            }
            $records["data"][] = [
                $user->id,
                $user_email,
                $user_name,
                $user->Mobile,
                $user->block_count,
                $user->block_date_to,
                $user->last_login_date,
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
