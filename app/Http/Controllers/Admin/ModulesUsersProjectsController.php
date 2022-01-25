<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mba;
use App\ModulesProjects;
use App\ModulesUsersProjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ModulesUsersProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modules_names = Mba::select('name')->get();
        $modules_projects = ModulesProjects::select('title')->get();
        return view('auth.modules_users_projects.view', compact('modules_names', 'modules_projects'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $modules_users_projects = ModulesUsersProjects::select('modules_users_projects.*', 'modules_projects.title AS project_name', 'mba.name AS module_name', 'users.FullName AS user_name', 'users.Email AS user_email', 'users.Mobile AS user_phone')
            ->leftJoin('modules_projects', 'modules_users_projects.project_id', '=', 'modules_projects.id')->leftJoin('mba', 'modules_users_projects.module_id', '=', 'mba.id')->leftJoin('users', 'modules_users_projects.user_id', '=', 'users.id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $modules_users_projects = $modules_users_projects->where('modules_users_projects.id', '=', "$id");
        }
        if (isset($data['module_name']) && !empty($data['module_name'])) {
            $module_name = $data['module_name'];
            $modules_users_projects = $modules_users_projects->where('mba.name', 'LIKE', "%$module_name%");
        }
        if (isset($data['project_name']) && !empty($data['project_name'])) {
            $project_name = $data['project_name'];
            $modules_users_projects = $modules_users_projects->where('modules_projects.title', 'LIKE', "%$project_name%");
        }
        if (isset($data['project_status']) && !empty($data['project_status'])) {
            $project_status = $data['project_status'];
            $modules_users_projects = $modules_users_projects->where('modules_users_projects.approved', "$project_status");
        }
        if (isset($data['user_name']) && !empty($data['user_name'])) {
            $user_name = $data['user_name'];
            $modules_users_projects = $modules_users_projects->where('users.FullName', 'LIKE', "%$user_name%");
        }
        if (isset($data['user_email']) && !empty($data['user_email'])) {
            $user_email = $data['user_email'];
            $modules_users_projects = $modules_users_projects->where('users.Email', 'LIKE', "%$user_email%");
        }
        if (isset($data['user_phone']) && !empty($data['user_phone'])) {
            $user_phone = $data['user_phone'];
            $modules_users_projects = $modules_users_projects->where('users.Mobile', 'LIKE', "%$user_phone%");
        }
        if (isset($data['file']) && !empty($data['file'])) {
            $file = $data['file'];
            $modules_users_projects = $modules_users_projects->where('modules_users_projects.file', 'LIKE', "%$file%");
        }
        if (isset($data['result']) && !empty($data['result'])) {
            $result = $data['result'];
            $modules_users_projects = $modules_users_projects->where('modules_users_projects.result', '=', $result);
        }
        if (isset($data['status_date_from']) && !empty($data['status_date_from']) && isset($data['status_date_to']) && !empty($data['status_date_to'])) {
            $status_date_from = $data['status_date_from'];
            $status_date_to = $data['status_date_to'];
            $modules_users_projects = $modules_users_projects->whereBetween('modules_users_projects.approved_date', [$status_date_from, $status_date_to]);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $modules_users_projects = $modules_users_projects->whereBetween('modules_users_projects.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }


        $iTotalRecords = $modules_users_projects->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'modules_users_projects.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'modules_users_projects.id';
                break;
            case 1:
                $columnName = 'mba.name';
                break;
            case 2:
                $columnName = 'modules_projects.title';
                break;
            /*case 3:
                $columnName = 'users.FullName';
                break;*/
            case 4:
                $columnName = 'users.Email';
                break;
          /*  case 5:
                $columnName = 'users.Mobile';
                break;*/
            case 5:
                $columnName = 'modules_users_projects.file';
                break;
            case 6:
                $columnName = 'modules_users_projects.result';
                break;
            case 7:
                $columnName = 'modules_users_projects.approved';
                break;
            /*case 7:
                $columnName = 'modules_users_projects.approved_date';
                break;*/
            case 8:
                $columnName = 'modules_users_projects.createdtime';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $modules_users_projects = $modules_users_projects->where(function ($q) use ($search) {
                $q->where('mba.name', 'LIKE', "%$search%")
                    ->orWhere('modules_projects.title', 'LIKE', "%$search%")
                    ->orWhere('users.FullName', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('users.Mobile', 'LIKE', "%$search%")
                    ->orWhere('modules_users_projects.approved', 'LIKE', "%$search%")
                    ->orWhere('modules_users_projects.file', 'LIKE', "%$search%")
                    ->orWhere('modules_users_projects.result', 'LIKE', "%$search%")
                    ->orWhere('modules_users_projects.id', '=', $search);
            });
        }

        $modules_users_projects = $modules_users_projects->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($modules_users_projects as $project) {
            $records["data"][] = [
                '<span '.(($project->has_another)?'class="has_another"':'').'>'.$project->id.'</span>',
                '<span '.(($project->has_another)?'class="has_another"':'').'>'.$project->module_name.'</span>',
                '<span '.(($project->has_another)?'class="has_another"':'').'>'.$project->project_name.'</span>',
                //'<span '.(($project->has_another)?'class="has_another"':'').'>'.$project->user_name.'</span>',
                '<span '.(($project->has_another)?'class="has_another"':'').'>'.$project->user_email.'</span>',
                //'<span '.(($project->has_another)?'class="has_another"':'').'>'.$project->user_phone.'</span>',
                //'<span '.(($project->has_another)?'class="has_another"':'').'>'.'<a href="' . assetURL($project->file) . '">' . $project->file . '</a>'.'</span>',
                '<span '.(($project->has_another)?'class="has_another"':'').'>'.'<a href="' . URL('admin/modules_users_projects/download_file/'.$project->id) . '">' . $project->file . '</a>'.'</span>',
                '<span '.(($project->has_another)?'class="has_another"':'').'>'.'<a href="javascript:;" class="editable-result editable-click" id="' . $project->id . '" data-original-title="result" data-type="text" data-pk="1"  data-value="' . $project->result . '">' . $project->result . '</a>'.'</span>',
                '<span '.(($project->has_another)?'class="has_another"':'').'>'.'<a href="javascript:;" class="editable-project editable-click" id="' . $project->id . '" data-original-title="Select status" data-type="select" data-pk="1"  data-value="' . $project->approved . '">' . $project->approved . '</a>'.'</span>',
                //$project->approved_date,
                '<span '.(($project->has_another)?'class="has_another"':'').'>'.$project->createdtime.'</span>',
                '<div><input type="file" class="uploadCorrection hidden" data-id="'.$project->id.'" id="uploadFile-'.$project->id.'"><button onclick="$(\'#uploadFile-'.$project->id.'\').trigger(\'click\')" class="btn btn-success">'.Lang::get('main.upload_correction').'</button></div>'.(($project->correction)?'<a target="_blank" href="'.assetURL($project->correction).'">'.$project->correction.'</a>':'')
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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function editProjectStatus($id, Request $request)
    {
        $data = $request->input();
        if (isset($data['name'], $data['value'])) {
            $project_id = $data['name'];
            $project_status = $data['value'];
            $project = ModulesUsersProjects::find($project_id);
            if (count($project)) {
                $project->approved = $project_status;
                $project->approved_by=Auth::user()->id;
                $project->approved_date = date("Y-m-d H:i:s");
                $project->save();
            }
        }
    }

    public function editProjectResult($id, Request $request)
    {
        $data = $request->input();
        if (isset($data['name'], $data['value'])) {
            $project_id = $data['name'];
            $project_result = $data['value'];
            $project = ModulesUsersProjects::find($project_id);
            if (count($project)) {
                $project->result = $project_result;
                $project->result_by=Auth::user()->id;
                $project->result_date=date('Y-m-d H:i:s');
                if(!DB::connection('mysql2')->table('modules_users_summary')->where('user_id',$project->user_id)->where('module_id',$project->module_id)->where('project','>',$project_result)->count()){
                    DB::connection('mysql2')->table('modules_users_summary')->where('user_id',$project->user_id)->where('module_id',$project->module_id)->update(['project' => $project_result]);
                }

                $project->save();
            }
        }
    }
    public function uploadCorrection(Request $request){
        $validator = Validator::make($request->all(),
            array(
                'project_id' => 'required',
                'file' => 'required|mimes:pdf,doc,docx,ppt,xlsx,xls|max:5120',
            ));
        if ($validator->fails()) {
            //return redirect()->back()->withErrors($validator->errors())->withInput();
            $messsage = '<div class="alert alert-danger"><ul>';
            foreach ($validator->errors()->all() as $m) {
                $messsage .= '<li>' . $m . '</li>';
            }
            $messsage .= '</ul></div>';
            return response()->json(['message' => $messsage, 'success' => false])->setCallback($request->input('callback'));
        } else {
            $project_id=$request->project_id;
            $project = ModulesUsersProjects::find($project_id);
            if(count($project)){
               /* if($project->correction){
                    if(file_exists(filePath().$project->correction)&&!empty($project->correction)){
                        unlink(filePath().$project->correction);
                    }
                }*/
                $file = $request->file('file');
                $fileName = uploadFileToE3melbusiness($file);
                $project->correction=$fileName;
                $project->correction_by=Auth::user()->id;
                $project->correction_date=date('Y-m-d H:i:s');
                $project->save();
                return response()->json(['message' => '<div class="alert alert-success">'.Lang::get('main.success_upload_correction').'</div>', 'success' => true])->setCallback($request->input('callback'));
            }else{
                return response()->json(['message' => '<div class="alert alert-danger">'.Lang::get('main.no_project_found').'</div>', 'success' => false])->setCallback($request->input('callback'));
            }

        }
    }
    public function downloadFile($id){
        $project = ModulesUsersProjects::find($id);
        if(count($project)){
            if(file_exists(filePath().$project->file)){
                return Response::download(filePath().$project->file,$project->file);
            }
        }
        return abort(404);
    }
}
