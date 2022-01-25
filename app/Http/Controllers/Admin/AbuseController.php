<?php

namespace App\Http\Controllers\Admin;

use App\sessionUsers;
use App\NormalUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;
use DB;

class AbuseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.abuse.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $abuses = sessionUsers::join('users','users.id','=','session_users.user_id')
                    ->select('users.id','users.id as user_id','users.Email as user_email',DB::raw('COUNT(session_users.id) as abuses_count'))->groupBy('session_users.user_id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $abuses = $abuses->where('users.id', '=', $id);
        }
        if (isset($data['email']) && !empty($data['email'])) {
            $email = $data['email'];
            $abuses = $abuses->where('users.Email', 'LIKE', "%$email%");
        }
        if (isset($data['date_from']) && !empty($data['date_from']) && isset($data['date_to']) && !empty($data['date_to'])) {
            $date_from = $data['date_from'];
            $date_to = $data['date_to'];
            $abuses = $abuses->whereBetween('session_users.createdtime', [$date_from .' 00:00:00', $date_to.' 23:59:59']);
        }
        if (isset($data['count']) && !empty($data['count'])) {
            $abuses_count = $data['count'];
            $abuses = $abuses->havingRaw("COUNT(*)=$abuses_count"); //where('abuses_count', '=', "$abuses_count");
        }
        $iTotalRecords = $abuses->get()->count();
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
                $columnName = 'abuses_count';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $abuses = $abuses->where(function ($q) use ($search) {
                $q->where('users.Email', 'LIKE', "%$search%")
                    ->orWhere('users.id', '=', $search);
            });
        }

        $abuses = $abuses->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($abuses as $abuse) {
            $user_email = $abuse->user_email;
            if(PerUser('users_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $abuse->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $abuse->user_id,
                $user_email,
                $abuse->abuses_count,
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
