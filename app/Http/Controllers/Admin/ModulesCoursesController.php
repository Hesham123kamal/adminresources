<?php

namespace App\Http\Controllers\Admin;


use App\Courses;
use App\Http\Controllers\Controller;
use App\Mba;
use App\ModulesCourses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ModulesCoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modules = Mba::select('mba.*')->get();
        $courses = Courses::select('courses.*')->get();
        return view('auth.modules_courses.view', compact('modules','courses'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $modules_courses = ModulesCourses::select('mba_module_courses.*','courses.image', 'courses.name AS related_course_name', 'mba.name AS module_name')
            ->leftJoin('courses', 'related_course', '=', 'courses.id')
            ->leftJoin('mba', 'module_id', '=', 'mba.id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $modules_courses = $modules_courses->where('mba_module_courses.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $modules_courses = $modules_courses->where('mba_module_courses.name', 'LIKE', "%$name%");
        }
        if (isset($data['module_name']) && !empty($data['module_name'])) {
            $module_name = $data['module_name'];
            $modules_courses = $modules_courses->where('mba.name', 'LIKE', "%$module_name%");
        }
        if (isset($data['pic']) && !empty($data['pic'])) {
            $pic = $data['pic'];
            $modules_courses = $modules_courses->where('courses.image', 'LIKE', "%$pic%");
        }
        if (isset($data['related_course']) && !empty($data['related_course'])) {
            $related_course_name = $data['related_course'];
            $modules_courses = $modules_courses->where('courses.name', 'LIKE', "%$related_course_name%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $modules_courses = $modules_courses->whereBetween('mba_module_courses.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }


        $iTotalRecords = $modules_courses->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'mba_module_courses.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'mba_module_courses.id';
                break;
            case 1:
                $columnName = 'mba_module_courses.name';
                break;
            case 2:
                $columnName = 'mba.name';
                break;
            case 3:
                $columnName = 'courses.image';
                break;
            case 4:
                $columnName = 'courses.name';
                break;
            case 5:
                $columnName = 'mba_module_courses.sort';
                break;
            case 6:
                $columnName = 'mba_module_courses.createdtime';
                break;
            case 7:
                $columnName = 'mba_module_courses.published';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $modules_courses = $modules_courses->where(function ($q) use ($search) {
                $q->where('mba_module_courses.name', 'LIKE', "%$search%")
                    ->orWhere('mba.name', 'LIKE', "%$search%")
                    ->orWhere('courses.image', 'LIKE', "%$search%")
                    ->orWhere('courses.name', 'LIKE', "%$search%")
                    ->orWhere('mba_module_courses.name', 'LIKE', "%$search%")
                    ->orWhere('mba_module_courses.description', 'LIKE', "%$search%")
                    ->orWhere('mba_module_courses.id', '=', $search);
            });
        }

        $modules_courses = $modules_courses->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($modules_courses as $module_course) {
            $module_course=makeDefaultImageGeneral($module_course,'image');
            $related_course_name = $module_course->related_course_name;
            if(PerUser('courses_edit') && $related_course_name !=''){
                $related_course_name= '<a target="_blank" href="' . URL('admin/courses/' . $module_course->related_course . '/edit') . '">' . $related_course_name . '</a>';
            }
            $records["data"][] = [
                $module_course->id,
                $module_course->name,
                $module_course->module_name,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img  style="width:100%;"  src="'.assetURL($module_course->image).'"></a>',
                $related_course_name,
                $module_course->sort,
                $module_course->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $module_course->id . '" type="checkbox" ' . ((!PerUser('users_active')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('users_active')) ? 'class="changeStatues"' : '') . ' ' . (($module_course->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-'.$module_course->id.'">
                                    <label for="checkbox-' . $module_course->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $module_course->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('modules_courses_edit')) ? '<li>
                                            <a href="' . URL('admin/modules_courses/' . $module_course->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('modules_courses_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $module_course->id . '" >
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
        $modules = Mba::select('mba.*')->get();
        $courses = Courses::select('courses.*')->get();
        return view('auth.modules_courses.add', compact('modules', 'courses'));
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
                'module_name' => 'required',
               /* 'description' => 'required',
                'pic' => 'required|mimes:jpeg,jpg,gif,bmp,png',*/
                'related_course' => 'required',
                'sort' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $active = (isset($data['active'])) ? 1 : 0;
            $modules_courses = new ModulesCourses();
            if ($request->hasFile('pic')) {
                $pic = $request->file('pic');
                $picName = uploadFileToE3melbusiness($pic);
                $modules_courses->image = $picName;
            }
            $modules_courses->name = $data['name'];
            $modules_courses->module_id = $data['module_name'];
            $modules_courses->related_course = $data['related_course'];
            $modules_courses->description = $request->description;
            $modules_courses->duetime = $request->duetime;
            $modules_courses->sort = $request->sort;
            $modules_courses->published = $active;
            if ($active == 'yes') {
                $modules_courses->published_by = Auth::user()->id;
                $modules_courses->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no') {
                $modules_courses->unpublished_by = Auth::user()->id;
                $modules_courses->unpublished_date = date("Y-m-d H:i:s");
            }
            $modules_courses->added_by = Auth::user()->id;
            $modules_courses->added_date = date("Y-m-d H:i:s");
            $modules_courses->lastedit_by = Auth::user()->id;
            $modules_courses->lastedit_date = date("Y-m-d H:i:s");
            if ($modules_courses->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.modules_courses'));
                return Redirect::to('admin/modules_courses/create');
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
        $modules = Mba::select('mba.*')->get();
        $courses = Courses::select('courses.*')->get();
        $module_course = ModulesCourses::find($id);
        return view('auth.modules_courses.edit', compact('modules', 'courses','module_course'));
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
        $module_course = ModulesCourses::find($id);
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'module_name' => 'required',
               /* 'description' => 'required',*/
                'related_course' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            if ($request->hasFile('pic')) {
                $pic = $request->file('pic');
                $picName = uploadFileToE3melbusiness($pic);
                $module_course->image = $picName;
            }
            $active = (isset($data['active'])) ? 1 : 0;
            $module_course->name = $data['name'];
            $module_course->module_id = $data['module_name'];
            $module_course->description = $request->description;
            $module_course->duetime = $request->duetime;
            $module_course->related_course = $data['related_course'];
            $module_course->sort = $data['sort'];
            if ($active == 'yes' && $module_course->published=='no') {
                $module_course->published_by = Auth::user()->id;
                $module_course->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no' && $module_course->published=='yes') {
                $module_course->unpublished_by = Auth::user()->id;
                $module_course->unpublished_date = date("Y-m-d H:i:s");
            }
            $module_course->published = $active;
            $module_course->lastedit_by = Auth::user()->id;
            $module_course->lastedit_date = date("Y-m-d H:i:s");
            if ($module_course->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.modules_courses'));
                return Redirect::to("admin/modules_courses/$module_course->id/edit");
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
        $module_course = ModulesCourses::find($id);
        if (count($module_course)) {
            $module_course->delete();
            $module_course->deleted_by = Auth::user()->id;
            $module_course->deleted_at = date("Y-m-d H:i:s");
        }
    }

    public function activation(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $active = $request->input('active');
            $module_course = ModulesCourses::find($id);
            if ($active == 'no') {
                $module_course->published = 'no';
                $module_course->unpublished_by = Auth::user()->id;
                $module_course->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($active == 'yes') {
                $module_course->published = 'yes';
                $module_course->published_by = Auth::user()->id;
                $module_course->published_date = date("Y-m-d H:i:s");
            }
            $module_course->save();
        } else {
            return redirect(404);
        }
    }
}
