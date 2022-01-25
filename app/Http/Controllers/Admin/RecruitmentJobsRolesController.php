<?php

namespace App\Http\Controllers\Admin;

use App\RecruitmentJob;
use App\RecruitmentJobsRoles;
use App\RecruitmentJobRoles;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class RecruitmentJobsRolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles=RecruitmentJobRoles::pluck('name', 'id');
        return view('auth.recruitment_jobs_roles.view',compact('roles'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $roles = RecruitmentJobsRoles::leftjoin('recruitment_jobs','recruitment_jobs.id','=','recruitment_jobs_roles.recruitment_job_id')
            ->leftjoin('recruitment_job_roles','recruitment_job_roles.id','=','recruitment_jobs_roles.recruitment_job_role_id')
            ->select('recruitment_jobs_roles.*','recruitment_jobs.title as job_title','recruitment_job_roles.name as job_role_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $roles = $roles->where('recruitment_jobs_roles.id', '=', $id);
        }
        if (isset($data['job']) && !empty($data['job'])) {
            $job = $data['job'];
            $roles = $roles->where('recruitment_jobs.title', 'LIKE', "%$job%");
        }
        if (isset($data['role']) && !empty($data['role'])) {
            $role_id = $data['role'];
            $roles = $roles->where('recruitment_jobs_roles.recruitment_job_role_id', '=', $role_id);
        }

        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $roles = $roles->whereBetween('recruitment_jobs_roles.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $roles->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'recruitment_jobs_roles.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'recruitment_jobs_roles.id';
                break;
            case 1:
                $columnName = 'recruitment_jobs_roles.createtime';
                break;
            case 2:
                $columnName = 'recruitment_jobs.title';
                break;
            case 3:
                $columnName = 'recruitment_job_roles.name';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $roles = $roles->where(function ($q) use ($search) {
                $q->where('recruitment_jobs_roles.id', '=', $search)
                    ->orWhere('recruitment_jobs.title', 'LIKE', "%$search%")
                    ->orWhere('recruitment_job_roles.name', 'LIKE', "%$search%");
            });
        }

        $roles = $roles->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($roles as $role) {
            $job_role_name = $role->job_role_name;
            $job_title = $role->job_title;
            if(PerUser('recruitment_job_roles_edit') && $job_role_name !=''){
                $job_role_name= '<a target="_blank" href="' . URL('admin/recruitment_job_roles/' . $role->recruitment_job_role_id . '/edit') . '">' . $job_role_name . '</a>';
            }
            if(PerUser('recruitment_jobs') && $job_title !=''){
                $job_title= '<a target="_blank" href="' . URL('admin/recruitment_jobs/' . $role->recruitment_job_id . '/edit') . '">' . $job_title . '</a>';
            }
            $records["data"][] = [
                $role->id,
                $role->createtime,
                $job_title,
                $job_role_name,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $role->id . '" type="checkbox" ' . ((!PerUser('recruitment_jobs_roles_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('recruitment_jobs_roles_publish')) ? 'class="changeStatues"' : '') . ' ' . (($role->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $role->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $role->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('recruitment_jobs_roles_edit')) ? '<li>
                                            <a href="' . URL('admin/recruitment_jobs_roles/' . $role->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('recruitment_jobs_roles_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $role->id . '" >
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
        $jobs = RecruitmentJob::pluck('title', 'id');
        $roles_names = RecruitmentJobRoles::pluck('name', 'id');
        return view('auth.recruitment_jobs_roles.add',compact('jobs','roles_names'));
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
        $validator = Validator::make($request->all(),
            array(
                'job' =>'required|exists:mysql2.recruitment_jobs,id',
                'role' =>'required|exists:mysql2.recruitment_job_roles,id',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $role = new RecruitmentJobsRoles();
            $role->recruitment_job_id = $data['job'];
            $role->recruitment_job_role_id = $data['role'];
            $role->published = $published;
            $role->createtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $role->published_by = Auth::user()->id;
                $role->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $role->unpublished_by = Auth::user()->id;
                $role->unpublished_date = date("Y-m-d H:i:s");
            }
            $role->lastedit_by = Auth::user()->id;
            $role->added_by = Auth::user()->id;
            $role->lastedit_date = date("Y-m-d H:i:s");
            $role->added_date = date("Y-m-d H:i:s");
            if ($role->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.recruitment_jobs_role'));
                return Redirect::to('admin/recruitment_jobs_roles/create');
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
        $role = RecruitmentJobsRoles::findOrFail($id);
        $jobs = RecruitmentJob::pluck('title', 'id');
        $roles_names = RecruitmentJobRoles::pluck('name', 'id');
        return view('auth.recruitment_jobs_roles.edit', compact('role','jobs','roles_names'));
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
        $role = RecruitmentJobsRoles::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'job' =>'required|exists:mysql2.recruitment_jobs,id',
                'role' =>'required|exists:mysql2.recruitment_job_roles,id',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $role->recruitment_job_id =  $data['job'];
            $role->recruitment_job_role_id =  $data['role'];
            if ($published == 'yes' && $role->published=='no') {
                $role->published_by = Auth::user()->id;
                $role->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $role->published=='yes') {
                $role->unpublished_by = Auth::user()->id;
                $role->unpublished_date = date("Y-m-d H:i:s");
            }
            $role->published = $published;
            $role->lastedit_by = Auth::user()->id;
            $role->lastedit_date = date("Y-m-d H:i:s");
            if ($role->save()){
                Session::flash('success', Lang::get('main.update') . Lang::get('main.recruitment_jobs_role'));
                return Redirect::to("admin/recruitment_jobs_roles/$role->id/edit");
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
        $role = RecruitmentJobsRoles::findOrFail($id);
        $role->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $role = RecruitmentJobsRoles::findOrFail($id);
            if ($published == 'no') {
                $role->published = 'no';
                $role->unpublished_by = Auth::user()->id;
                $role->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $role->published = 'yes';
                $role->published_by = Auth::user()->id;
                $role->published_date = date("Y-m-d H:i:s");
            }
            $role->save();
        } else {
            return redirect(404);
        }
    }
}
