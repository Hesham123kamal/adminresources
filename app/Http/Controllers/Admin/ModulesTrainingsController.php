<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\Http\Controllers\Controller;
use App\Modules;
use App\ModulesTrainings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ModulesTrainingsController extends Controller

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $modules = Modules::get();
        $courses = Courses::get();
        return view('auth.modules_trainings.view', compact('modules', 'courses'));

    }

    public function getModulesTrainingsAJAX(Request $request)
    {
        $data = $request->input();
        $modules_trainings = ModulesTrainings::select('modules_trainings.*', 'mba.name AS module_name','courses.name AS course_name')->leftJoin('mba', 'mba.id', '=', 'modules_trainings.module_id')->leftJoin('courses', 'courses.id', '=', 'modules_trainings.course_id');
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $modules_trainings = $modules_trainings->where('modules_trainings.name', 'LIKE', "%$name%");
        }
        if (isset($data['module_id']) && !empty($data['module_id'])) {
            $module_id = $data['module_id'];
            $modules_trainings = $modules_trainings->where('modules_trainings.module_id', $module_id);
        }
        if (isset($data['course_name']) && !empty($data['course_name'])) {
            $course_name = $data['course_name'];
            $modules_trainings = $modules_trainings->where('courses.name', 'LIKE', "%$course_name%");
        }
        $iTotalRecords = $modules_trainings->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'modules_trainings.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'modules_trainings.id';
                break;
            case 1:
                $columnName = 'modules_trainings.name';
                break;
            case 2:
                $columnName = 'mba.name';
                break;
            case 3:
                $columnName = 'courses.name';
                break;
            case 4:
                $columnName = 'modules_trainings.active';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $modules_trainings = $modules_trainings->where(function ($q) use ($search) {
                $q->where('modules_trainings.name', 'LIKE', "%$search%")
                    ->orWhere('mba.name', 'LIKE', "%$search%")
                    ->orWhere('courses.name', 'LIKE', "%$search%");
            });
        }
        $modules_trainings = $modules_trainings->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();
        foreach ($modules_trainings as $question) {
            $records["data"][] = [
                $question->id,
                $question->name,
                $question->module_name,
                $question->course_name,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $question->id . '" type="checkbox" ' . ((!PerUser('users_active')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('users_active')) ? 'class="changeStatues"' : '') . ' ' . (($question->active) ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $question->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((Peruser('modules_trainings_edit')) ? '<li>
                                            <a href="' . URL('admin/modules_trainings/' . $question->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . ' 
                                            </a>
                                        </li>' : '') . '
                                    ' . ((Peruser('modules_trainings_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $question->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . ' 
                                            </a>
                                        </li>' : '') . '
                                        
                                        
                                    </ul>
                                </div>'
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
        $modules = Modules::get();
        $courses = Courses::get();
        return view('auth.modules_trainings.add', compact('modules', 'courses'));

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
        $data = $request->input();
        $valid = array(
            'module_id' => 'required',
            'course_id' => 'required',
            'name' => 'required',
            'questions_numbers' => 'required',
            'question_time' => 'required|numeric',
        );
        if (PerUser('modules_trainings_url')) {
            $valid['url'] = 'required|unique:mysql2.modules_trainings,url';
        }
        $validator = Validator::make($request->all(), $valid);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $active = (isset($data['active'])) ? 1 : 0;
            $modules_trainings = new ModulesTrainings();
            $modules_trainings->module_id = $data['module_id'];
            $modules_trainings->course_id = $data['course_id'];
            $modules_trainings->name = $data['name'];
            $modules_trainings->questions_numbers = $data['questions_numbers'];
            $modules_trainings->question_time = $data['question_time'];
            $modules_trainings->active = $active;
            if ($active == 1) {
                $modules_trainings->active_by = Auth::user()->id;
                $modules_trainings->active_date = date("Y-m-d H:i:s");
            }
            if ($active == 0) {
                $modules_trainings->unactive_by = Auth::user()->id;
                $modules_trainings->unactive_date = date("Y-m-d H:i:s");
            }
            $modules_trainings->added_by = Auth::user()->id;
            $modules_trainings->added_date = date("Y-m-d H:i:s");
            $modules_trainings->lastedit_by = Auth::user()->id;
            $modules_trainings->lastedit_date = date("Y-m-d H:i:s");
            if ($modules_trainings->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.modules_trainings'));
                return Redirect::to('admin/modules_trainings/create');
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
        return abort(404);
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
        $post = ModulesTrainings::find($id);
        if (count($post)) {
            $modules = Modules::get();
            $courses = Courses::get();
            return view('auth.modules_trainings.edit', compact('post', 'modules', 'courses'));
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
        //
        $data = $request->input();
        $modules_trainings = ModulesTrainings::find($id);
        if (count($modules_trainings)) {
            $valid = array(
                'module_id' => 'required',
                'course_id' => 'required',
                'name' => 'required',
                'questions_numbers' => 'required',
                'question_time' => 'required|numeric',
            );
            $validator = Validator::make($request->all(), $valid);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors())->withInput();
            } else {
                $active = (isset($data['active'])) ? 1 : 0;
                $modules_trainings->module_id = $data['module_id'];
                $modules_trainings->course_id = $data['course_id'];
                $modules_trainings->name = $data['name'];
                $modules_trainings->questions_numbers = $data['questions_numbers'];
                $modules_trainings->question_time = $data['question_time'];
                if ($active == 1 && $modules_trainings->active == 0) {
                    $modules_trainings->active_by = Auth::user()->id;
                    $modules_trainings->active_date = date("Y-m-d H:i:s");
                }
                if ($active == 0 && $modules_trainings->active == 1) {
                    $modules_trainings->unactive_by = Auth::user()->id;
                    $modules_trainings->unactive_date = date("Y-m-d H:i:s");
                }

                $modules_trainings->active = $active;
                $modules_trainings->lastedit_by = Auth::user()->id;
                $modules_trainings->lastedit_date = date("Y-m-d H:i:s");
                if ($modules_trainings->save()) {
                    Session::flash('success', Lang::get('main.update') . Lang::get('main.modules_trainings'));
                    return Redirect::to('admin/modules_trainings/' . $id . '/edit');
                }
            }
        } else {
            return abort(404);
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
        //
        $modules_trainings = ModulesTrainings::find($id);
        if (count($modules_trainings)) {
            $modules_trainings->deleted_at = date('Y-m-d H:i:s');
            $modules_trainings->save();
            if ($modules_trainings->delete()) {

            }
        }
    }

    public function activation(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $active = $request->input('active');
            $modules_trainings = ModulesTrainings::find($id);
            if ($active == 0) {
                $modules_trainings->active = 0;
                $modules_trainings->unactive_by = Auth::user()->id;
                $modules_trainings->unactive_date = date("Y-m-d H:i:s");
            } elseif ($active == 1) {
                $modules_trainings->active = 1;
                $modules_trainings->active_by = Auth::user()->id;
                $modules_trainings->active_date = date("Y-m-d H:i:s");
            }
            $modules_trainings->save();
        } else {
            return redirect(404);
        }
    }
}
