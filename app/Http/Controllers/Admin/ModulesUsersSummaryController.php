<?php

namespace App\Http\Controllers\Admin;

use App\ModulesUsersSummary;
use App\Mba;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ModulesUsersSummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modules=Mba::pluck('name', 'id');
        return view('auth.modules_users_summary.view',compact('modules'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $modules_users_summaries = ModulesUsersSummary::leftjoin('mba','mba.id','=','modules_users_summary.module_id')
                                    ->leftjoin('modules_projects','modules_projects.id','=','modules_users_summary.project_id')
                                    ->leftjoin('users','users.id','=','modules_users_summary.user_id')
                                    ->select('modules_users_summary.*','users.Email as user_email','mba.name as module_name','modules_projects.title as project_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $modules_users_summaries = $modules_users_summaries->where('modules_users_summary.id', '=', "$id");
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $modules_users_summaries = $modules_users_summaries->where('users.Email', 'LIKE', "%$user%");
        }
        if (isset($data['module']) && !empty($data['module'])) {
            $module = $data['module'];
            $modules_users_summaries = $modules_users_summaries->where('modules_users_summary.module_id', '=', $module);
        }
        if (isset($data['project']) && !empty($data['project'])) {
            $project = $data['project'];
            $modules_users_summaries = $modules_users_summaries->where('modules_projects.title', 'LIKE', "%$project%");
        }
        if (isset($data['proj']) && !empty($data['proj'])) {
            $proj = $data['proj'];
            $modules_users_summaries = $modules_users_summaries->where('modules_users_summary.project', '=',  "$proj");
        }
        if (isset($data['exam']) && !empty($data['exam'])) {
            $exam = $data['exam'];
            $modules_users_summaries = $modules_users_summaries->where('modules_users_summary.exam', '=',  "$exam");
        }
        if (isset($data['progress']) && !empty($data['progress'])) {
            $progress = $data['progress'];
            $modules_users_summaries = $modules_users_summaries->where('modules_users_summary.progress', '=',  "$progress");
        }
        if (isset($data['project_upload']) && !empty($data['project_upload'])) {
            $project_upload = $data['project_upload'];
            $modules_users_summaries = $modules_users_summaries->where('modules_users_summary.project_upload', '=',  "$project_upload");
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $modules_users_summaries = $modules_users_summaries->whereBetween('modules_users_summary.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        if (isset($data['download_from']) && !empty($data['download_from']) && isset($data['download_to']) && !empty($data['download_to'])) {
            $download_from = $data['download_from'];
            $download_to = $data['download_to'];
            $modules_users_summaries = $modules_users_summaries->whereBetween('modules_users_summary.download_project_date', [$download_from .' 00:00:00', $download_to.' 23:59:59']);
        }

        $iTotalRecords = $modules_users_summaries->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'modules_users_summary.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'modules_users_summary.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'mba.name';
                break;
            case 3:
                $columnName = 'modules_projects.title';
                break;
            case 4:
                $columnName = 'modules_users_summary.project';
                break;
            case 5:
                $columnName = 'modules_users_summary.exam';
                break;
            case 6:
                $columnName = 'modules_users_summary.progress';
                break;
            case 7:
                $columnName = 'modules_users_summary.download_project_date';
                break;
            case 8:
                $columnName = 'modules_users_summary.project_upload';
                break;
            case 9:
                $columnName = 'modules_users_summary.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $modules_users_summaries = $modules_users_summaries->where(function ($q) use ($search) {
                $q->where('users.Email', 'LIKE', "%$search%")
                    ->orWhere('mba.name', 'LIKE', "%$search%")
                    ->orWhere('modules_projects.title', 'LIKE', "%$search%")
                    ->orWhere('modules_users_summary.id', '=', $search)
                    ->orWhere('modules_users_summary.project', '=', $search)
                    ->orWhere('modules_users_summary.exam', '=', $search)
                    ->orWhere('modules_users_summary.progress', '=', $search)
                    ->orWhere('modules_users_summary.project_upload', '=', $search);
            });
        }

        $modules_users_summaries = $modules_users_summaries->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($modules_users_summaries as $modules_users_summary) {
            $user_email = $modules_users_summary->user_email;
            $module_name = $modules_users_summary->module_name;
            $project_name = $modules_users_summary->project_name;
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $modules_users_summary->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            if(PerUser('modules_edit') && $module_name !=''){
                $module_name= '<a target="_blank" href="' . URL('admin/modules/' . $modules_users_summary->module_id . '/edit') . '">' . $module_name . '</a>';
            }
            if(PerUser('modules_projects_edit') && $project_name !=''){
                $project_name= '<a target="_blank" href="' . URL('admin/modules_projects/' . $modules_users_summary->project_id . '/edit') . '">' . $project_name . '</a>';
            }
            $records["data"][] = [
                $modules_users_summary->id,
                $user_email,
                $module_name,
                $project_name,
                $modules_users_summary->project,
                $modules_users_summary->exam,
                $modules_users_summary->progress,
                $modules_users_summary->download_project_date,
                $modules_users_summary->project_upload,
                $modules_users_summary->createtime,
                '<div class="btn-group text-center" id="single-order-' . $modules_users_summary->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('modules_users_summary_edit')) ? '<li>
                                            <a href="' . URL('admin/modules_users_summary/' . $modules_users_summary->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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
        $modules_users_summary = ModulesUsersSummary::findOrFail($id);
        return view('auth.modules_users_summary.edit', compact('modules_users_summary'));
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
        $modules_users_summary = ModulesUsersSummary::findOrFail($id);
        $rules = array(
            'project_upload' => 'required|numeric',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $modules_users_summary->project_upload = $data['project_upload'];
            if ($modules_users_summary->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.modules_users_summary'));
                return Redirect::to("admin/modules_users_summary/$modules_users_summary->id/edit");
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

    }

}
