<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\NormalUser;
use App\Tags;
use App\UserCV;
use App\UserCVLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
class UsersCVsController extends Controller
{

    public function index(){
        return view('auth.users_cvs.view');
    }

    function search(Request $request){
        $data = $request->input();
        $users_cvs = UserCV::select('users_cv.*','users.FullName')->join('users','users.id','=','users_cv.user_id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $users_cvs = $users_cvs->where('users_cv.id', '=', "$id");
        }
        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $id = $data['user_id'];
            $users_cvs = $users_cvs->where('users_cv.user_id', '=', "$id");
        }
        if (isset($data['FullName']) && !empty($data['FullName'])) {
            $FullName = $data['FullName'];
            $users_cvs = $users_cvs->where('users.FullName', 'LIKE', "%$FullName%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $users_cvs = $users_cvs->whereBetween('users_cv.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $users_cvs->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'users_cv.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'users_cv.id';
                break;
            case 1:
                $columnName = 'users_cv.user_id';
                break;
            case 2:
                $columnName = 'users_cv.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $users_cvs = $users_cvs->where(function ($q) use ($search) {
                $q->Where('users_cv.id', '=', $search);
            });
        }
        $users_cvs = $users_cvs->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)->get();

        foreach ($users_cvs as $users_cv) {
            $last_download_log = UserCVLog::where('user_cv_id',$users_cv->id)->where('user_id',Auth()->user()->id)->max('created_at');
            $cv_url = URL('admin/cv_download/'.$users_cv->id);
            $records["data"][] = [
                $users_cv->id,
                $users_cv->FullName,
                $last_download_log,
                $users_cv->createtime,
                '<div class="btn-group text-center" id="single-order-' . $users_cv->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('users_cvs_download')) ? '<li>
                                            <a href='.$cv_url.' class="download_this" target="_blank" data-id="' . $users_cv->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.download') . '
                                            </a>
                                        </li>' : '') . '
                                    </ul>
                                </div>',
            ];
        }
        if (isset($data["customActionType"]) && $data["customActionType"] == "group_action") {
            $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
        }
        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;
        $records['postData'] = $data;
        //return response()->json($data)->setCallback($request->input('callback'));
        return response()->json($records)->setCallback($request->input('callback'));

    }

    public function cv_download($id){
        if(isset($id) && !empty($id)){
            $user_cv = UserCV::with('user')->find($id);
            if(!empty($user_cv) && !empty($user_cv->cv)){
                $log_cv = new UserCVLog();
                $log_cv->user_id = Auth::user()->id;
                $log_cv->user_cv_id = Auth::user()->id;
//                $file = public_path(). $user_cv->cv;
                $file = filePath().'cv/'.$user_cv->cv;
                if ($log_cv->save()) {
                    $headers = array('Content-Type: '.mime_content_type($file));
                    return response()->download($file, $user_cv->cv, $headers);
                }
            }
        }
        return false;
    }
}
