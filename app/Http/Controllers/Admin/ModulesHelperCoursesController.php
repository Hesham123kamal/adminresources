<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\Mba;
use App\ModulesHelperCourses;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ModulesHelperCoursesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = Courses::select('courses.*')->get();
        return view('auth.modules_helper_courses.view',compact('courses'));
    }

    function search(Request $request)
    {

        $data = $request->input();
        $modules_helper_courses = ModulesHelperCourses::select('modules_helper_courses.*','courses.name AS course_name')
            ->leftJoin('courses', 'courses.id', '=', 'modules_helper_courses.course_id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $modules_helper_courses = $modules_helper_courses->where('modules_helper_courses.id', '=', "$id");
        }
        if (isset($data['course_name']) && !empty($data['course_name'])) {
            $course_name = $data['course_name'];
            $modules_helper_courses = $modules_helper_courses->where('courses.name', 'LIKE', "%$course_name%");
        }


        $iTotalRecords = $modules_helper_courses->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'modules_helper_courses.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'modules_helper_courses.id';
                break;
            case 1:
                $columnName = 'courses.name';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $modules_helper_courses = $modules_helper_courses->where(function ($q) use ($search) {
                $q->where('courses.name', 'LIKE', "%$search%")
                    ->orWhere('modules_helper_courses.id', '=', $search);
            });
        }

        $modules_helper_courses = $modules_helper_courses->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($modules_helper_courses as $module_helper_course) {
            $course = $module_helper_course->course_name;
            if(PerUser('courses_edit') && $course !=''){
                $course= '<a target="_blank" href="' . URL('admin/courses/' . $module_helper_course->course_id . '/edit') . '">' . $course . '</a>';
            }
            $records["data"][] = [
                $module_helper_course->id,
                $course,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $module_helper_course->id . '" type="checkbox" ' . ((!PerUser('users_active')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('users_active')) ? 'class="changeStatues"' : '') . ' ' . (($module_helper_course->published=="yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
                                    <label for="checkbox-' . $module_helper_course->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $module_helper_course->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('modules_helper_courses_edit')) ? '<li>
                                            <a href="' . URL('admin/modules_helper_courses/' . $module_helper_course->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('modules_helper_courses_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $module_helper_course->id . '" >
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
        $courses = Courses::select('courses.*')->get();
        return view('auth.modules_helper_courses.add',compact('modules_names','courses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->input();
        $validator = Validator::make($request->all(),
            array(
                'course_id' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $active = (isset($data['active'])) ? 'yes' : 'no';
            $modules_helper_courses = new ModulesHelperCourses();
            $modules_helper_courses->course_id = $data['course_id'];
            $modules_helper_courses->published = $active;
            if ($active == 'yes') {
                $modules_helper_courses->published_by = Auth::user()->id;
                $modules_helper_courses->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no') {
                $modules_helper_courses->unpublished_by = Auth::user()->id;
                $modules_helper_courses->unpublished_date = date("Y-m-d H:i:s");
            }
            $modules_helper_courses->added_by = Auth::user()->id;
            $modules_helper_courses->added_date = date("Y-m-d H:i:s");
            $modules_helper_courses->lastedit_by = Auth::user()->id;
            $modules_helper_courses->lastedit_date = date("Y-m-d H:i:s");
            if ($modules_helper_courses->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.modules_helper_courses'));
                return Redirect::to('admin/modules_helper_courses/create');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $module_helper_course = ModulesHelperCourses::find($id);
        $courses = Courses::get();
//        dd($modules_names);
        return view('auth.modules_helper_courses.edit',compact('courses','module_helper_course'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->input();
        $module_helper_course = ModulesHelperCourses::find($id);
        $validator = Validator::make($request->all(),
            array(
                'course_id' => 'required',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $active = (isset($data['active'])) ? 'yes' : 'no';
            $module_helper_course->course_id = $data['course_id'];
            if ($active == 'yes' && $module_helper_course->published=='no') {
                $module_helper_course->published_by = Auth::user()->id;
                $module_helper_course->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no' && $module_helper_course->published=='yes') {
                $module_helper_course->unpublished_by = Auth::user()->id;
                $module_helper_course->unpublished_date = date("Y-m-d H:i:s");
            }
            $module_helper_course->published = $active;
            $module_helper_course->lastedit_by = Auth::user()->id;
            $module_helper_course->lastedit_date = date("Y-m-d H:i:s");
            if ($module_helper_course->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.modules_helper_courses'));
                return Redirect::to("admin/modules_helper_courses/$module_helper_course->id/edit");
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $module_helper_course = ModulesHelperCourses::find($id);
        if (count($module_helper_course)) {
            $module_helper_course->delete();
            $module_helper_course->deleted_by = Auth::user()->id;
            $module_helper_course->deleted_at = date("Y-m-d H:i:s");
        }
    }

    public function activation(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $active = $request->input('active');
            $module_helper_course = ModulesHelperCourses::find($id);
            if ($active == 'no') {
                $module_helper_course->published = 'no';
                $module_helper_course->unpublished_by = Auth::user()->id;
                $module_helper_course->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($active == 'yes') {
                $module_helper_course->published = 'yes';
                $module_helper_course->published_by = Auth::user()->id;
                $module_helper_course->published_date = date("Y-m-d H:i:s");
            }
            $module_helper_course->save();
        } else {
            return redirect(404);
        }
    }
}
