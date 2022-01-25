<?php

namespace App\Http\Controllers\Admin;

use App\RecruitmentJob;
use App\RecruitmentEmployees;
use App\RecruitmentEmployeeJobApply;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class RecruitmentEmployeeJobApplyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.recruitment_employee_job_apply.view');
    }

    function search(Request $request)
    {

        $data = $request->input();
        $recruits = RecruitmentEmployeeJobApply::leftjoin('recruitment_jobs', 'recruitment_jobs.id', '=', 'recruitment_employee_job_apply.job_id')
            ->leftjoin('recruitment_employees', 'recruitment_employees.id', '=', 'recruitment_employee_job_apply.employee_id')
            ->select('recruitment_employee_job_apply.*','recruitment_jobs.title','recruitment_employees.email');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $recruits = $recruits->where('recruitment_employee_job_apply.id', '=', $id);
        }
        if (isset($data['job']) && !empty($data['job'])) {
            $job = $data['job'];
            $recruits = $recruits->where('recruitment_jobs.title','LIKE', "%$job%");
        }
        if (isset($data['employee']) && !empty($data['employee'])) {
            $employee = $data['employee'];
            $recruits = $recruits->where('recruitment_employees.email','LIKE', "%$employee%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $recruits = $recruits->whereBetween('recruitment_employee_job_apply.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $recruits->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'recruitment_employee_job_apply.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'recruitment_employee_job_apply.id';
                break;
            case 1:
                $columnName = 'recruitment_jobs.title';
                break;
            case 2:
                $columnName = 'recruitment_employees.email';
                break;
            case 3:
                $columnName = 'recruitment_employee_job_apply.createtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $recruits = $recruits->where(function ($q) use ($search) {
                $q->where('recruitment_employee_job_apply.id', '=', $search)
                    ->orWhere('recruitment_jobs.title', 'LIKE', "%$search%")
                    ->orWhere('recruitment_employees.email', 'LIKE', "%$search%");
            });
        }

        $recruits = $recruits->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($recruits as $recruit) {
            $employee = $recruit->email;
            $job = $recruit->title;
            if(PerUser('recruitment_jobs_edit') && $job !=''){
                $job= '<a target="_blank" href="' . URL('admin/recruitment_jobs/' . $recruit->job_id . '/edit') . '">' . $job . '</a>';
            }
            if(PerUser('recruitment_employees_edit') && $employee !=''){
                $employee= '<a target="_blank" href="' . URL('admin/recruitment_employees/' . $recruit->employee_id . '/edit') . '">' . $employee . '</a>';
            }
            $records["data"][] = [
                $recruit->id,
                $job,
                $employee,
                $recruit->createtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $recruit->id . '" type="checkbox" ' . ((!PerUser('recruitment_employee_job_apply_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('recruitment_employee_job_apply_publish')) ? 'class="changeStatues"' : '') . ' ' . (($recruit->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $recruit->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $recruit->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('recruitment_employee_job_apply_edit')) ? '<li>
                                            <a href="' . URL('admin/recruitment_employee_job_apply/' . $recruit->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('recruitment_employee_job_apply_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $recruit->id . '" >
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
        $employees = RecruitmentEmployees::pluck('email', 'id');
        return view('auth.recruitment_employee_job_apply.add',compact('jobs','employees'));
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
                'employee' =>'required|exists:mysql2.recruitment_employees,id',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $recruit = new RecruitmentEmployeeJobApply();
            $recruit->job_id = $data['job'];
            $recruit->employee_id = $data['employee'];
            $recruit->published = $published;
            $recruit->createtime = date("Y-m-d H:i:s");
            if ($published == 'yes') {
                $recruit->published_by = Auth::user()->id;
                $recruit->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no') {
                $recruit->unpublished_by = Auth::user()->id;
                $recruit->unpublished_date = date("Y-m-d H:i:s");
            }
            $recruit->lastedit_by = Auth::user()->id;
            $recruit->added_by = Auth::user()->id;
            $recruit->lastedit_date = date("Y-m-d H:i:s");
            $recruit->added_date = date("Y-m-d H:i:s");
            if ($recruit->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.recruitment_employee_job_apply'));
                return Redirect::to('admin/recruitment_employee_job_apply/create');
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
        $recruit = RecruitmentEmployeeJobApply::findOrFail($id);
        $jobs = RecruitmentJob::pluck('title', 'id');
        $employees = RecruitmentEmployees::pluck('email', 'id');
        return view('auth.recruitment_employee_job_apply.edit', compact('recruit','jobs','employees'));
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
        $recruit = RecruitmentEmployeeJobApply::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'job' =>'required|exists:mysql2.recruitment_jobs,id',
                'employee' =>'required|exists:mysql2.recruitment_employees,id',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $recruit->job_id = $data['job'];
            $recruit->employee_id = $data['employee'];
            if ($published == 'yes' && $recruit->published=='no') {
                $recruit->published_by = Auth::user()->id;
                $recruit->published_date = date("Y-m-d H:i:s");
            }
            if ($published == 'no' && $recruit->published=='yes') {
                $recruit->unpublished_by = Auth::user()->id;
                $recruit->unpublished_date = date("Y-m-d H:i:s");
            }
            $recruit->published = $published;
            $recruit->lastedit_by = Auth::user()->id;
            $recruit->lastedit_date = date("Y-m-d H:i:s");
            if ($recruit->save()){
                Session::flash('success', Lang::get('main.update') . Lang::get('main.recruitment_employee_job_apply'));
                return Redirect::to("admin/recruitment_employee_job_apply/$recruit->id/edit");
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
        $recruit = RecruitmentEmployeeJobApply::findOrFail($id);
        $recruit->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $recruit = RecruitmentEmployeeJobApply::findOrFail($id);
            if ($published == 'no') {
                $recruit->published = 'no';
                $recruit->unpublished_by = Auth::user()->id;
                $recruit->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($published == 'yes') {
                $recruit->published = 'yes';
                $recruit->published_by = Auth::user()->id;
                $recruit->published_date = date("Y-m-d H:i:s");
            }
            $recruit->save();
        } else {
            return redirect(404);
        }
    }
}
