<?php

namespace App\Http\Controllers\Admin;

use App\DemoMedicalLog;
use App\NormalUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DemoMedicalLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.demo_medical_logs.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $demo_medical_logs = DemoMedicalLog::select('demo_medical_log.*','users.Email as user_email')
                                ->join('users','users.id','=','demo_medical_log.user_id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $demo_medical_logs = $demo_medical_logs->where('demo_medical_log.id', '=', "$id");
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $demo_medical_logs = $demo_medical_logs->where('users.Email', 'LIKE', "%$user%");
        }
        if (isset($data['type']) && !empty($data['type'])) {
            $type = $data['type'];
            $demo_medical_logs = $demo_medical_logs->where('demo_medical_log.type', '=', "$type");
        }
        if (isset($data['start_time_from']) && !empty($data['start_time_from']) && isset($data['start_time_to']) && !empty($data['start_time_to'])) {
            $start_time_from = $data['start_time_from'];
            $start_time_to = $data['start_time_to'];
            $demo_medical_logs = $demo_medical_logs->whereBetween('demo_medical_log.starttime', [$start_time_from .' 00:00:00', $start_time_to.' 23:59:59']);
        }
        if (isset($data['end_time_from']) && !empty($data['end_time_from']) && isset($data['end_time_to']) && !empty($data['end_time_to'])) {
            $end_time_from = $data['end_time_from'];
            $end_time_to = $data['end_time_to'];
            $demo_medical_logs = $demo_medical_logs->whereBetween('demo_medical_log.endtime', [$end_time_from .' 00:00:00', $end_time_to.' 23:59:59']);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $demo_medical_logs = $demo_medical_logs->whereBetween('demo_medical_log.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $demo_medical_logs->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'demo_medical_log.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'demo_medical_log.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'demo_medical_log.type';
                break;
            case 3:
                $columnName = 'demo_medical_log.starttime';
                break;
            case 4:
                $columnName = 'demo_medical_log.endtime';
                break;
            case 5:
                $columnName = 'demo_medical_log.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $demo_medical_logs = $demo_medical_logs->where(function ($q) use ($search) {
                $q->where('users.Email', 'LIKE', "%$search%")
                    ->orWhere('demo_medical_log.type', 'LIKE', "%$search%")
                    ->orWhere('demo_medical_log.id', '=', $search);
            });
        }

        $demo_medical_logs = $demo_medical_logs->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($demo_medical_logs as $demo_medical_log) {
            $type=$demo_medical_log->type=='7_days'?'7 Days':'2 Days';
            $user = $demo_medical_log->user_email;
            if(PerUser('normal_user_edit') && $user !=''){
                $user= '<a target="_blank" href="' . URL('admin/normal_user/' . $demo_medical_log->user_id . '/edit') . '">' . $user . '</a>';
            }
            $records["data"][] = [
                $demo_medical_log->id,
                $user,
                $type,
                $demo_medical_log->starttime,
                $demo_medical_log->endtime,
                $demo_medical_log->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $demo_medical_log->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('demo_medical_logs_edit')) ? '<li>
                                            <a href="' . URL('admin/demo_medical_logs/' . $demo_medical_log->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('demo_medical_logs_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $demo_medical_log->id . '" >
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
        return view('auth.demo_medical_logs.add');
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
            'type' => 'required|in:7_days,2_days',
            'start_date' => 'required|date_format:"Y-m-d"',
            'end_date' => 'required|date_format:"Y-m-d"'
        );
        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $demo_medical_log = new DemoMedicalLog();
            $demo_medical_log->type = $data['type'];
            $demo_medical_log->user_id = $user->id;
            $demo_medical_log->starttime=date('Y-m-d H:i:s', strtotime(date("H:i:s"), strtotime($data['start_date'])));
            $demo_medical_log->endtime=date('Y-m-d H:i:s', strtotime(date("H:i:s"), strtotime($data['end_date'])));
            $demo_medical_log->createdtime = date("Y-m-d H:i:s");
            $demo_medical_log->createdby =  Auth::user()->id;
            if ($demo_medical_log->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.demo_medical_log'));
                return Redirect::to('admin/demo_medical_logs/create');
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
        $demo_medical_log = DemoMedicalLog::findOrFail($id);
        $demo_medical_log->starttime = date("Y-m-d", strtotime($demo_medical_log->starttime));
        $demo_medical_log->endtime = date("Y-m-d", strtotime($demo_medical_log->endtime));
        $user=isset($demo_medical_log->user)?$demo_medical_log->user->Email:'';
        return view('auth.demo_medical_logs.edit', compact('demo_medical_log','user'));
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
        $demo_medical_log = DemoMedicalLog::findOrFail($id);
        $rules=array(
            'user' => 'required',
            'type' => 'required|in:7_days,2_days',
            'start_date' => 'required|date_format:"Y-m-d"',
            'end_date' => 'required|date_format:"Y-m-d"'
        );
        $user=NormalUser::where('Email', $data['user'])->first();
        if($user===null){
            $rules['user']= 'required|exists:mysql2.users,Email';
        }
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $demo_medical_log->type = $data['type'];
            $demo_medical_log->user_id = $user->id;
            $demo_medical_log->starttime=date('Y-m-d H:i:s', strtotime(date("H:i:s"), strtotime($data['start_date'])));
            $demo_medical_log->endtime=date('Y-m-d H:i:s', strtotime(date("H:i:s"), strtotime($data['end_date'])));
            if ($demo_medical_log->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.demo_medical_log'));
                return Redirect::to("admin/demo_medical_logs/$demo_medical_log->id/edit");
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
        $demo_medical_log = DemoMedicalLog::findOrFail($id);
        $demo_medical_log->delete();
    }

}
