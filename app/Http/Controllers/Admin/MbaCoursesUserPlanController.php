<?php

namespace App\Http\Controllers\Admin;


use App\Courses;
use App\Http\Controllers\Controller;
use App\Mba;
use App\NormalUser;
use App\MbaCoursesUsersPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class MbaCoursesUserPlanController extends Controller
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
        return view('auth.mba_courses_user_plan.view', compact('modules','courses'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $mba_courses_user_plan = MbaCoursesUsersPlan::select('mba_courses_user_plan.*','courses.image', 'courses.name AS related_course_name', 'mba.name AS module_name', 'users.Email AS user_email')
            ->leftJoin('courses', 'related_course', '=', 'courses.id')
            ->leftJoin('users', 'user_id', '=', 'users.id')
            ->leftJoin('mba', 'module_id', '=', 'mba.id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $mba_courses_user_plan = $mba_courses_user_plan->where('mba_courses_user_plan.id', '=', "$id");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $mba_courses_user_plan = $mba_courses_user_plan->where('mba_courses_user_plan.name', 'LIKE', "%$name%");
        }
        if (isset($data['module_name']) && !empty($data['module_name'])) {
            $module_name = $data['module_name'];
            $mba_courses_user_plan = $mba_courses_user_plan->where('mba.name', 'LIKE', "%$module_name%");
        }
        if (isset($data['pic']) && !empty($data['pic'])) {
            $pic = $data['pic'];
            $mba_courses_user_plan = $mba_courses_user_plan->where('courses.image', 'LIKE', "%$pic%");
        }
        if (isset($data['related_course']) && !empty($data['related_course'])) {
            $related_course_name = $data['related_course'];
            $mba_courses_user_plan = $mba_courses_user_plan->where('courses.name', 'LIKE', "%$related_course_name%");
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $mba_courses_user_plan = $mba_courses_user_plan->where('users.Email', 'LIKE', "%$user%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $mba_courses_user_plan = $mba_courses_user_plan->whereBetween('mba_courses_user_plan.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }


        $iTotalRecords = $mba_courses_user_plan->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'mba_courses_user_plan.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'mba_courses_user_plan.id';
                break;
            case 1:
                $columnName = 'mba_courses_user_plan.name';
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
                $columnName = 'mba_courses_user_plan.sort';
                break;
            case 6:
                $columnName = 'users.Email';
                break;
            case 7:
                $columnName = 'mba_courses_user_plan.createdtime';
                break;
            case 8:
                $columnName = 'mba_courses_user_plan.published';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $mba_courses_user_plan = $mba_courses_user_plan->where(function ($q) use ($search) {
                $q->where('mba_courses_user_plan.name', 'LIKE', "%$search%")
                    ->orWhere('mba.name', 'LIKE', "%$search%")
                    ->orWhere('courses.image', 'LIKE', "%$search%")
                    ->orWhere('courses.name', 'LIKE', "%$search%")
                    ->orWhere('mba_courses_user_plan.name', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('mba_courses_user_plan.description', 'LIKE', "%$search%")
                    ->orWhere('mba_courses_user_plan.id', '=', $search);
            });
        }

        $mba_courses_user_plan = $mba_courses_user_plan->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($mba_courses_user_plan as $mba_courses_user_plan) {
//            $mba_courses_user_plan=makeDefaultImageGeneral($mba_courses_user_plan,'image');
            $related_course_name = $mba_courses_user_plan->related_course_name;
            $user_email = $mba_courses_user_plan->user_email;
            if(PerUser('courses_edit') && $related_course_name !=''){
                $related_course_name= '<a target="_blank" href="' . URL('admin/courses/' . $mba_courses_user_plan->related_course . '/edit') . '">' . $related_course_name . '</a>';
            }
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $mba_courses_user_plan->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $mba_courses_user_plan->id,
                $mba_courses_user_plan->name,
                $mba_courses_user_plan->module_name,
                '<a class="image-link" href="#image-modal" data-toggle="modal"><img  style="width:100%;"  src="'.assetURL($mba_courses_user_plan->image).'"></a>',
                $related_course_name,
                $mba_courses_user_plan->sort,
                $user_email,
                $mba_courses_user_plan->createdtime,
                '<td class="text-center">
                                <div class="checkbox-nice checkbox-inline">
                                    <input data-id="' . $mba_courses_user_plan->id . '" type="checkbox" ' . ((!PerUser('mba_courses_user_plan_active')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('mba_courses_user_plan_active')) ? 'class="changeStatues"' : '') . ' ' . (($mba_courses_user_plan->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-'.$mba_courses_user_plan->id.'">
                                    <label for="checkbox-' . $mba_courses_user_plan->id . '">
                                    </label>
                                </div>
                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $mba_courses_user_plan->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('mba_courses_user_plan_edit')) ? '<li>
                                            <a href="' . URL('admin/mba_courses_user_plan/' . $mba_courses_user_plan->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('mba_courses_user_plan_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $mba_courses_user_plan->id . '" >
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
        return view('auth.mba_courses_user_plan.add', compact('modules', 'courses'));
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
                /* 'description' => 'required',*/
                 'pic' => 'required|mimes:jpeg,jpg,gif,bmp,png',
                'related_course' => 'required',
                'sort' => 'required',
                'user' => 'required|exists:mysql2.users,Email',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $active = (isset($data['active'])) ? 1 : 0;
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
            $mba_courses_user_plan = new MbaCoursesUsersPlan();
            $pic = $request->file('pic');
            $picName = uploadFileToE3melbusiness($pic);
            $mba_courses_user_plan->image = $picName;
            $mba_courses_user_plan->name = $data['name'];
            $mba_courses_user_plan->user_id = $user_id;
            $mba_courses_user_plan->module_id = $data['module_name'];
            $mba_courses_user_plan->related_course = $data['related_course'];
            $mba_courses_user_plan->description = $request->description;
            $mba_courses_user_plan->duetime = $request->duetime;
            $mba_courses_user_plan->sort = $request->sort;
            $mba_courses_user_plan->published = $active;
            if ($active == 'yes') {
                $mba_courses_user_plan->published_by = Auth::user()->id;
                $mba_courses_user_plan->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no') {
                $mba_courses_user_plan->unpublished_by = Auth::user()->id;
                $mba_courses_user_plan->unpublished_date = date("Y-m-d H:i:s");
            }
            $mba_courses_user_plan->added_by = Auth::user()->id;
            $mba_courses_user_plan->added_date = date("Y-m-d H:i:s");
            $mba_courses_user_plan->lastedit_by = Auth::user()->id;
            $mba_courses_user_plan->lastedit_date = date("Y-m-d H:i:s");
            if ($mba_courses_user_plan->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.mba_courses_user_plan'));
                return Redirect::to('admin/mba_courses_user_plan/create');
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
        $module_course  = MbaCoursesUsersPlan::findOrFail($id);
        $modules = Mba::select('mba.*')->get();
        $courses = Courses::select('courses.*')->get();
        $user=isset($module_course->user)?$module_course->user->Email:'';
        return view('auth.mba_courses_user_plan.edit', compact('modules', 'courses','module_course','user'));
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
        $mba_courses_user_plan = MbaCoursesUsersPlan::findOrFail($id);
        $validator = Validator::make($request->all(),
            array(
                'name' => 'required',
                'module_name' => 'required',
                /* 'description' => 'required',*/
                'related_course' => 'required',
                'user' => 'required|exists:mysql2.users,Email',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            if ($request->hasFile('pic')) {
                $pic = $request->file('pic');
                $picName = uploadFileToE3melbusiness($pic);
                $mba_courses_user_plan->image = $picName;
            }
            $active = (isset($data['active'])) ? 1 : 0;
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
            $mba_courses_user_plan->user_id = $user_id;
            $mba_courses_user_plan->name = $data['name'];
            $mba_courses_user_plan->module_id = $data['module_name'];
            $mba_courses_user_plan->description = $request->description;
            $mba_courses_user_plan->duetime = $request->duetime;
            $mba_courses_user_plan->related_course = $data['related_course'];
            $mba_courses_user_plan->sort = $data['sort'];
            if ($active == 'yes' && $mba_courses_user_plan->published=='no') {
                $mba_courses_user_plan->published_by = Auth::user()->id;
                $mba_courses_user_plan->published_date = date("Y-m-d H:i:s");
            }
            if ($active == 'no' && $mba_courses_user_plan->published=='yes') {
                $mba_courses_user_plan->unpublished_by = Auth::user()->id;
                $mba_courses_user_plan->unpublished_date = date("Y-m-d H:i:s");
            }
            $mba_courses_user_plan->published = $active;
            $mba_courses_user_plan->lastedit_by = Auth::user()->id;
            $mba_courses_user_plan->lastedit_date = date("Y-m-d H:i:s");
            if ($mba_courses_user_plan->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.mba_courses_user_plan'));
                return Redirect::to("admin/mba_courses_user_plan/$mba_courses_user_plan->id/edit");
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
        $mba_courses_user_plan = MbaCoursesUsersPlan::find($id);
        if (count($mba_courses_user_plan)) {
            $mba_courses_user_plan->delete();
            $mba_courses_user_plan->deleted_by = Auth::user()->id;
            $mba_courses_user_plan->deleted_at = date("Y-m-d H:i:s");
        }
    }

    public function activation(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $active = $request->input('active');
            $mba_courses_user_plan = MbaCoursesUsersPlan::find($id);
            if ($active == 'no') {
                $mba_courses_user_plan->published = 'no';
                $mba_courses_user_plan->unpublished_by = Auth::user()->id;
                $mba_courses_user_plan->unpublished_date = date("Y-m-d H:i:s");
            } elseif ($active == 'yes') {
                $mba_courses_user_plan->published = 'yes';
                $mba_courses_user_plan->published_by = Auth::user()->id;
                $mba_courses_user_plan->published_date = date("Y-m-d H:i:s");
            }
            $mba_courses_user_plan->save();
        } else {
            return redirect(404);
        }
    }
}
