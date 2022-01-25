<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mba;
use App\ModulesProjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class ModulesProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modules_names = Mba::select('id','name')->get();
        $modules_projects = ModulesProjects::select('title')->get();
        return view('auth.modules_projects.view', compact('modules_names', 'modules_projects'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $modules_projects = ModulesProjects::select('modules_projects.*', 'mba.name AS module_name')
            ->leftJoin('mba', 'mba.id', '=', 'modules_projects.module_id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $modules_projects = $modules_projects->where('modules_projects.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $modules_projects = $modules_projects->where('mba.name', 'LIKE', "%$name%");
        }
        if (isset($data['module_id']) && !empty($data['module_id'])) {
            $module_id = $data['module_id'];
            $modules_projects = $modules_projects->where('mba.id', 'LIKE', $module_id);
        }
        if (isset($data['title']) && !empty($data['title'])) {
            $title = $data['title'];
            $modules_projects = $modules_projects->where('modules_projects.title', 'LIKE', "%$title%");
        }
        if (isset($data['file']) && !empty($data['file'])) {
            $file = $data['file'];
            $modules_projects = $modules_projects->where('modules_projects.file', 'LIKE', "%$file%");
        }


        $iTotalRecords = $modules_projects->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'modules_projects.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'modules_projects.id';
                break;
            case 1:
                $columnName = 'mba.name';
                break;
            case 2:
                $columnName = 'modules_projects.title';
                break;
            case 3:
                $columnName = 'modules_projects.file';
                break;
            case 4:
                $columnName = 'modules_projects.active';
                break;
        }
            $search = $data['search']['value'];
            if ($search) {
                $modules_projects = $modules_projects->where(function ($q) use ($search) {
                    $q->where('mba.name', 'LIKE', "%$search%")
                        ->orWhere('modules_projects.title', 'LIKE', "%$search%")
                        ->orWhere('modules_projects.file', 'LIKE', "%$search%")
                        ->orWhere('modules_projects.id', '=', $search);
                });
            }

        $modules_projects = $modules_projects->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($modules_projects as $project) {
            $records["data"][] = [
                $project->id,
                $project->module_name,
                $project->title,
                '<a href="' . assetURL($project->file) . '">' . $project->file . '</a>',
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $project->id . '" type="checkbox" ' . ((!PerUser('users_active')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('users_active')) ? 'class="changeStatues"' : '') . ' ' . (($project->active) ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $project->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $project->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('modules_projects_edit')) ? '<li>
                                            <a href="' . URL('admin/modules_projects/' . $project->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('modules_projects_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $project->id . '" >
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
        $mba = Mba::select('mba.*')->get();
        return view('auth.modules_projects.add', compact('mba'));
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
                'title' => 'required',
                'file' => 'required|mimes:doc,docx,xlsx,xls',
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
            $active = (isset($data['active'])) ? 1 : 0;
            $file = $request->file('file');
            $fileName = uploadFileToE3melbusiness($file);
            $modules_projects = new ModulesProjects();
            $modules_projects->module_id = $data['name'];
            $modules_projects->title = $data['title'];
            $modules_projects->file = $fileName;
            $modules_projects->active = $active;
            if ($active == 1) {
                $modules_projects->active_by = Auth::user()->id;
                $modules_projects->active_date = date("Y-m-d H:i:s");
            }
            if ($active == 0) {
                $modules_projects->unactive_by = Auth::user()->id;
                $modules_projects->unactive_date = date("Y-m-d H:i:s");
            }
            $modules_projects->added_by = Auth::user()->id;
            $modules_projects->added_date = date("Y-m-d H:i:s");
            $modules_projects->lastedit_by = Auth::user()->id;
            $modules_projects->lastedit_date = date("Y-m-d H:i:s");
            if ($modules_projects->save()) {
                $messsage = '<div class="alert alert-success"><ul>';
                $messsage .= '<li>' .  Lang::get('main.insert') . Lang::get('main.module_project') . '</li>';
                $messsage .= '</ul></div>';
                return response()->json(['message'=>$messsage,'success'=>true]);
                //Session::flash('success', Lang::get('main.insert') . Lang::get('main.modules_projects'));
                //return Redirect::to('admin/modules_projects/create');
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
        $module_project = ModulesProjects::find($id);
        $single_mba = Mba::find($module_project->module_id);
        if (count($module_project)) {
            $mba = Mba::select('mba.*')->get();
            return view('auth.modules_projects.edit', compact('single_mba', 'module_project', 'mba'));
        } else {
            return abort(404);
        }
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
        $module_project = ModulesProjects::find($id);
        $rules=array(
            'name' => 'required',
            'title' => 'required',
        );
        if ( $request->file('file')) {
            $rules['file'] = 'required|mimes:doc,docx,xlsx,xls';
            }

        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            //return redirect()->back()->withErrors($validator->errors())->withInput();
            $messsage = '<div class="alert alert-danger"><ul>';
            foreach ($validator->errors()->all() as $m) {
                $messsage .= '<li>' . $m . '</li>';
            }
            $messsage .= '</ul></div>';
            return response()->json(['message' => $messsage, 'success' => false])->setCallback($request->input('callback'));
        } else {
            if ( $request->file('file')){
                $file = $request->file('file');
                $fileName = uploadFileToE3melbusiness($file);
                $module_project->file = $fileName;
            }
            $active = (isset($data['active'])) ? 1 : 0;
            $module_project->module_id = $data['name'];
            $module_project->title = $data['title'];
            $module_project->active = $active;
            if ($active == 1) {
                $module_project->active_by = Auth::user()->id;
                $module_project->active_date = date("Y-m-d H:i:s");
            }
            if ($active == 0) {
                $module_project->unactive_by = Auth::user()->id;
                $module_project->unactive_date = date("Y-m-d H:i:s");
            }
            $module_project->lastedit_by = Auth::user()->id;
            $module_project->lastedit_date = date("Y-m-d H:i:s");
            if ($module_project->save()) {
                $messsage = '<div class="alert alert-success"><ul>';
                $messsage .= '<li>' . Lang::get('main.update') . Lang::get('main.modules_projects') . '</li>';
                $messsage .= '</ul></div>';
                return response()->json(['message'=>$messsage,'success'=>true]);
                //Session::flash('success', Lang::get('main.update') . Lang::get('main.modules_projects'));
                //return Redirect::to("admin/modules_projects/$module_project->id/edit");
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
        $module_project = ModulesProjects::find($id);
        if (count($module_project)) {
            $module_project->delete();
        }
    }

    public function activation(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $active = $request->input('active');
            $module_project = ModulesProjects::find($id);
            if ($active == 0) {
                $module_project->active = 0;
                $module_project->unactive_by = Auth::user()->id;
                $module_project->unactive_date = date("Y-m-d H:i:s");
            } elseif ($active == 1) {
                $module_project->active = 1;
                $module_project->active_by = Auth::user()->id;
                $module_project->active_date = date("Y-m-d H:i:s");
            }
            $module_project->save();
        } else {
            return redirect(404);
        }
    }
}
