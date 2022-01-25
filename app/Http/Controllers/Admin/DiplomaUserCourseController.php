<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\DiplomaUserCourse;
use App\Diplomas;
use App\NormalUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DiplomaUserCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses=Courses::pluck('name','id')->toArray();
        $diplomas=Diplomas::pluck('name','id')->toArray();
        return view('auth.diploma_user_courses.view',compact('courses','diplomas'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $diploma_user_courses = DiplomaUserCourse::leftjoin('users','users.id','=','diplomas_users_courses.user_id')
                                ->leftjoin('courses','courses.id','=','diplomas_users_courses.course_id')
                                ->leftjoin('diplomas','diplomas.id','=','diplomas_users_courses.diploma_id')
                                ->select('diplomas_users_courses.*','users.Email as user_email','courses.name as course_name','diplomas.name as diploma_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $diploma_user_courses = $diploma_user_courses->where('diplomas_users_courses.id', '=', "$id");
        }
        if (isset($data['diploma']) && !empty($data['diploma'])) {
            $diploma = $data['diploma'];
            $diploma_user_courses = $diploma_user_courses->where('diplomas.name','LIKE', "%$diploma%");
        }
        if (isset($data['diploma_id']) && !empty($data['diploma_id'])) {
            $diploma_id = $data['diploma_id'];
            $diploma_user_courses = $diploma_user_courses->where('diplomas.id', $diploma_id);
        }
        if (isset($data['course']) && !empty($data['course'])) {
            $course = $data['course'];
            $diploma_user_courses = $diploma_user_courses->where('courses.id','=', $course);
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $diploma_user_courses = $diploma_user_courses->where('users.Email','LIKE', "%$user%");
        }
        if (isset($data['exam']) && !empty($data['exam'])) {
            $exam = $data['exam'];
            $diploma_user_courses = $diploma_user_courses->where('diplomas_users_courses.exam', 'LIKE', "%$exam%");
        }
        if (isset($data['sort']) && !empty($data['sort'])) {
            $sort = $data['sort'];
            $diploma_user_courses = $diploma_user_courses->where('diplomas_users_courses.sort', '=', $sort);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $diploma_user_courses = $diploma_user_courses->whereBetween('diplomas_users_courses.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $diploma_user_courses->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'diplomas_users_courses.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'diplomas_users_courses.id';
                break;
            case 1:
                $columnName = 'diplomas_users_courses.exam';
                break;
            case 2:
                $columnName = 'diplomas_users_courses.createtime';
                break;
            case 3:
                $columnName = 'diplomas.name';
                break;
            case 4:
                $columnName = 'courses.name';
                break;
            case 5:
                $columnName = 'users.Email';
                break;
            case 6:
                $columnName = 'diplomas_users_courses.sort';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $diploma_user_courses = $diploma_user_courses->where(function ($q) use ($search) {
                $q->where('diplomas_users_courses.exam', 'LIKE', "%$search%")
                    ->orWhere('diplomas_users_courses.id', '=', $search)
                    ->orWhere('diplomas.name', 'LIKE', "%$search%")
                    ->orWhere('courses.name', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('diplomas_users_courses.sort', '=', $search);
            });
        }

        $diploma_user_courses = $diploma_user_courses->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($diploma_user_courses as $diploma_user_course) {
            $diploma_name = $diploma_user_course->diploma_name;
            $course_name = $diploma_user_course->course_name;
            $user_email = $diploma_user_course->user_email;
            if(PerUser('diplomas_edit') && $diploma_name !=''){
                $diploma_name= '<a target="_blank" href="' . URL('admin/diplomas/' . $diploma_user_course->diploma_id . '/edit') . '">' . $diploma_name . '</a>';
            }
            if(PerUser('courses_edit') && $course_name !=''){
                $course_name= '<a target="_blank" href="' . URL('admin/courses/' . $diploma_user_course->course_id . '/edit') . '">' . $course_name . '</a>';
            }
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $diploma_user_course->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $diploma_user_course->id,
                $diploma_user_course->exam,
                $diploma_user_course->createtime,
                $diploma_name,
                $course_name,
                $user_email,
                $diploma_user_course->sort,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $diploma_user_course->id . '" type="checkbox" ' . ((!PerUser('diploma_user_courses_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('diploma_user_courses_publish')) ? 'class="changeStatues"' : '') . ' ' . (($diploma_user_course->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $diploma_user_course->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $diploma_user_course->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('diploma_user_courses_edit')) ? '<li>
                                            <a href="' . URL('admin/diploma_user_courses/' . $diploma_user_course->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('diploma_user_courses_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $diploma_user_course->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '
                                        ' . ((PerUser('diploma_user_courses_delete_all')) ? '<li>
                                            <a class="delete_all_this" data-id="' . $diploma_user_course->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete_all') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('diploma_user_courses_copy')) ? '<li>
                                            <a href="'.URL('admin/diploma_user_courses/copy/'.$diploma_user_course->id).'" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.copy') . '
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
        $diplomas = Diplomas::pluck('name', 'id');
        $courses = Courses::pluck('name', 'id');
        return view('auth.diploma_user_courses.add', compact('diplomas', 'courses'));
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
                'diploma' => 'required|exists:mysql2.diplomas,id',
                'course' => 'required|exists:mysql2.courses,id',
                'user' => 'required|exists:mysql2.users,Email',
                'sort' => 'required|integer',
                'exam' => 'required|in:not exam,pass,fail',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $diploma_user_course = new DiplomaUserCourse();
            $diploma_user_course->diploma_id = $data['diploma'];
            $diploma_user_course->course_id = $data['course'];
            $diploma_user_course->user_id = NormalUser::where('Email', $data['user'])->first()->id;
            $diploma_user_course->sort = $data['sort'];
            $diploma_user_course->exam = $data['exam'];
            //$diploma_user_course->published = $published;
            $diploma_user_course->createtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $diploma_user_course->published_by = Auth::user()->id;
//                $diploma_user_course->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $diploma_user_course->unpublished_by = Auth::user()->id;
//                $diploma_user_course->unpublished_date = date("Y-m-d H:i:s");
//            }
            $diploma_user_course->lastedit_by = Auth::user()->id;
            $diploma_user_course->added_by = Auth::user()->id;
            $diploma_user_course->lastedit_date = date("Y-m-d H:i:s");
            $diploma_user_course->added_date = date("Y-m-d H:i:s");
            if ($diploma_user_course->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.diploma_user_course'));
                return Redirect::to('admin/diploma_user_courses/create');
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
        $diploma_user_course = DiplomaUserCourse::findOrFail($id);
        $diplomas = Diplomas::pluck('name', 'id');
        $courses = Courses::pluck('name', 'id');
        $user=$diploma_user_course->user;
        $user=isset($diploma_user_course->user)?$diploma_user_course->user->Email:'';
        return view('auth.diploma_user_courses.edit', compact('diploma_user_course','diplomas','courses','user'));
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
        $diploma_user_course = DiplomaUserCourse::findOrFail($id);
        $rules = array(
            'diploma' => 'required|exists:mysql2.diplomas,id',
            'course' => 'required|exists:mysql2.courses,id',
            'user' => 'required|exists:mysql2.users,Email',
            'sort' => 'required|integer',
            'exam' => 'required|in:not exam,pass,fail',
        );

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $diploma_user_course->diploma_id = $data['diploma'];
            $diploma_user_course->course_id = $data['course'];
            $diploma_user_course->user_id = NormalUser::where('Email', $data['user'])->first()->id;
            $diploma_user_course->sort = $data['sort'];
            $diploma_user_course->exam = $data['exam'];
//            if ($published == 'yes' && $diploma_user_course->published=='no') {
//                $diploma_user_course->published_by = Auth::user()->id;
//                $diploma_user_course->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $diploma_user_course->published=='yes') {
//                $diploma_user_course->unpublished_by = Auth::user()->id;
//                $diploma_user_course->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $diploma_user_course->published = $published;
            $diploma_user_course->lastedit_by = Auth::user()->id;
            $diploma_user_course->lastedit_date = date("Y-m-d H:i:s");
            if ($diploma_user_course->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.diploma_user_course'));
                return Redirect::to("admin/diploma_user_courses/$diploma_user_course->id/edit");
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
        $diploma_user_course = DiplomaUserCourse::findOrFail($id);
        $diploma_user_course->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $diploma_user_course = DiplomaUserCourse::findOrFail($id);
//            if ($published == 'no') {
//                $diploma_user_course->published = 'no';
//                $diploma_user_course->unpublished_by = Auth::user()->id;
//                $diploma_user_course->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $diploma_user_course->published = 'yes';
//                $diploma_user_course->published_by = Auth::user()->id;
//                $diploma_user_course->published_date = date("Y-m-d H:i:s");
//            }
//            $diploma_user_course->save();
//        } else {
//            return redirect(404);
//        }
//    }
    public function copy($id)
    {
        $diploma_user_course = DiplomaUserCourse::findOrFail($id);
        $diploma_user_course->createtime = date("Y-m-d H:i:s");
        $diploma_user_course->replicate()->save();
        return Redirect::to('admin/diploma_user_courses/'.$diploma_user_course->id.'/edit');
    }
    public function delete_all(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $diploma_user_courses= DiplomaUserCourse::findOrFail($id);
            DiplomaUserCourse::where('diploma_id',$diploma_user_courses->diploma_id)
                ->where('user_id',$diploma_user_courses->user_id)->delete();

        } else {
            return redirect(404);
        }
    }

}
