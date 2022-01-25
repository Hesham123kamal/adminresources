<?php

namespace App\Http\Controllers\Admin;

use App\RecruitmentJobRoles;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class  RecruitmentJobRolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('auth.recruitment_job_roles.view');
    }

    function search(Request $request)
    {
        $data = $request->input();
        $roles = RecruitmentJobRoles::select('recruitment_job_roles.*');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $roles = $roles->where('id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $roles = $roles->where('name', 'LIKE', "%$name%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $roles = $roles->whereBetween('createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
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
        $columnName = 'id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'id';
                break;
            case 1:
                $columnName = 'name';
                break;
            case 4:
                $columnName = 'createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $roles = $roles->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('id', '=', $search);
            });
        }

        $roles = $roles->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($roles as $role) {
            $records["data"][] = [
                $role->id,
                $role->name,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $role->id . '" type="checkbox" ' . ((!PerUser('recruitment_job_roles_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('recruitment_job_roles_publish')) ? 'class="changeStatues"' : '') . ' ' . (($role->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $role->id . '">
                                    </label>
                                </div>
                            </td>',
                $role->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $role->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('recruitment_job_roles_edit')) ? '<li>
                                            <a href="' . URL('admin/recruitment_job_roles/' . $role->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('recruitment_job_roles_delete')) ? '<li>
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
        return view('auth.recruitment_job_roles.add');
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
                'name' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $role = new RecruitmentJobRoles();
            $role->name = $data['name'];
            $role->published = $published;
            $role->createdtime = date("Y-m-d H:i:s");
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
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.recruitment_job_role'));
                return Redirect::to('admin/recruitment_job_roles/create');
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
        $role = RecruitmentJobRoles::findOrFail($id);
        return view('auth.recruitment_job_roles.edit', compact('role'));
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
        $role = RecruitmentJobRoles::findOrFail($id);
        $validator = Validator::make($request->all(),array(
            'name' => 'required',
        ));

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }else {
            $published = (isset($data['published'])) ? 'yes' : 'no';
            $role->name = $data['name'];
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
            if ($role->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.recruitment_job_role'));
                return Redirect::to("admin/recruitment_job_roles/$role->id/edit");
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
        $role = RecruitmentJobRoles::findOrFail($id);
        $role->delete();
    }

    public function publish(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $published = $request->input('published');
            $role = RecruitmentJobRoles::findOrFail($id);
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
