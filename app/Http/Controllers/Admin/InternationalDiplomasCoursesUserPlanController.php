<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\InternationalDiplomsCoursesUsersPlan;
use App\InternationalDiplomas;
use App\NormalUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class InternationalDiplomasCoursesUserPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses=Courses::pluck('name','id')->toArray();
        $diplomas=InternationalDiplomas::pluck('name','id')->toArray();
        return view('auth.international_diplomas_courses_user_plan.view',compact('courses','diplomas'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $international_diplomas_courses_user_plan = InternationalDiplomsCoursesUsersPlan::leftjoin('users','users.id','=','international_diplomas_courses_user_plan.user_id')
                                ->leftjoin('courses','courses.id','=','international_diplomas_courses_user_plan.course_id')
                                ->leftjoin('international_diplomas','international_diplomas.id','=','international_diplomas_courses_user_plan.diploma_id')
                                ->select('international_diplomas_courses_user_plan.*','users.Email as user_email','courses.name as course_name','international_diplomas.name as diploma_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $international_diplomas_courses_user_plan = $international_diplomas_courses_user_plan->where('international_diplomas_courses_user_plan.id', '=', "$id");
        }
        if (isset($data['diploma']) && !empty($data['diploma'])) {
            $diploma = $data['diploma'];
            $international_diplomas_courses_user_plan = $international_diplomas_courses_user_plan->where('international_diplomas.name','LIKE', "%$diploma%");
        }
        if (isset($data['diploma_id']) && !empty($data['diploma_id'])) {
            $diploma_id = $data['diploma_id'];
            $international_diplomas_courses_user_plan = $international_diplomas_courses_user_plan->where('international_diplomas.id', $diploma_id);
        }
        if (isset($data['course']) && !empty($data['course'])) {
            $course = $data['course'];
            $international_diplomas_courses_user_plan = $international_diplomas_courses_user_plan->where('courses.id','=', $course);
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $international_diplomas_courses_user_plan = $international_diplomas_courses_user_plan->where('users.Email','LIKE', "%$user%");
        }
        if (isset($data['exam']) && !empty($data['exam'])) {
            $exam = $data['exam'];
            $international_diplomas_courses_user_plan = $international_diplomas_courses_user_plan->where('international_diplomas_courses_user_plan.exam', 'LIKE', "%$exam%");
        }
        if (isset($data['sort']) && !empty($data['sort'])) {
            $sort = $data['sort'];
            $international_diplomas_courses_user_plan = $international_diplomas_courses_user_plan->where('international_diplomas_courses_user_plan.sort', '=', $sort);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $international_diplomas_courses_user_plan = $international_diplomas_courses_user_plan->whereBetween('international_diplomas_courses_user_plan.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $international_diplomas_courses_user_plan->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'international_diplomas_courses_user_plan.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'international_diplomas_courses_user_plan.id';
                break;
            case 1:
                $columnName = 'international_diplomas_courses_user_plan.exam';
                break;
            case 2:
                $columnName = 'international_diplomas_courses_user_plan.createtime';
                break;
            case 3:
                $columnName = 'international_diplomas.name';
                break;
            case 4:
                $columnName = 'courses.name';
                break;
            case 5:
                $columnName = 'users.Email';
                break;
            case 6:
                $columnName = 'international_diplomas_courses_user_plan.sort';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $international_diplomas_courses_user_plan = $international_diplomas_courses_user_plan->where(function ($q) use ($search) {
                $q->where('international_diplomas_courses_user_plan.exam', 'LIKE', "%$search%")
                    ->orWhere('international_diplomas_courses_user_plan.id', '=', $search)
                    ->orWhere('international_diplomas.name', 'LIKE', "%$search%")
                    ->orWhere('courses.name', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('international_diplomas_courses_user_plan.sort', '=', $search);
            });
        }

        $international_diplomas_courses_user_plan = $international_diplomas_courses_user_plan->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($international_diplomas_courses_user_plan as $international_diplomas_courses_user_plan) {
            $diploma_name = $international_diplomas_courses_user_plan->diploma_name;
            $course_name = $international_diplomas_courses_user_plan->course_name;
            $user_email = $international_diplomas_courses_user_plan->user_email;
            if(PerUser('international_diplomas_edit') && $diploma_name !=''){
                $diploma_name= '<a target="_blank" href="' . URL('admin/international_diplomas/' . $international_diplomas_courses_user_plan->diploma_id . '/edit') . '">' . $diploma_name . '</a>';
            }
            if(PerUser('courses_edit') && $course_name !=''){
                $course_name= '<a target="_blank" href="' . URL('admin/courses/' . $international_diplomas_courses_user_plan->course_id . '/edit') . '">' . $course_name . '</a>';
            }
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $international_diplomas_courses_user_plan->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                $international_diplomas_courses_user_plan->id,
                $international_diplomas_courses_user_plan->exam,
                $international_diplomas_courses_user_plan->createtime,
                $diploma_name,
                $course_name,
                $user_email,
                $international_diplomas_courses_user_plan->sort,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $international_diplomas_courses_user_plan->id . '" type="checkbox" ' . ((!PerUser('international_diplomas_courses_user_plan_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('international_diplomas_courses_user_plan_publish')) ? 'class="changeStatues"' : '') . ' ' . (($international_diplomas_courses_user_plan->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $international_diplomas_courses_user_plan->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $international_diplomas_courses_user_plan->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('international_diplomas_courses_user_plan_edit')) ? '<li>
                                            <a href="' . URL('admin/international_diplomas_courses_user_plan/' . $international_diplomas_courses_user_plan->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('international_diplomas_courses_user_plan_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $international_diplomas_courses_user_plan->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('international_diplomas_courses_user_plan_copy')) ? '<li>
                                            <a href="'.URL('admin/international_diplomas_courses_user_plan/copy/'.$international_diplomas_courses_user_plan->id).'" >
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
        $diplomas = InternationalDiplomas::pluck('name', 'id');
        $courses = Courses::pluck('name', 'id');
        return view('auth.international_diplomas_courses_user_plan.add', compact('diplomas', 'courses'));
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
                'diploma' => 'required|exists:mysql2.international_diplomas,id',
                'course' => 'required|exists:mysql2.courses,id',
                'user' => 'required|exists:mysql2.users,Email',
                'sort' => 'required|integer',
                'exam' => 'required|in:not exam,pass,fail',
            ));
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            //$published = (isset($data['published'])) ? 'yes' : 'no';
            $international_diplomas_courses_user_plan = new InternationalDiplomsCoursesUsersPlan();
            $international_diplomas_courses_user_plan->diploma_id = $data['diploma'];
            $international_diplomas_courses_user_plan->course_id = $data['course'];
            $international_diplomas_courses_user_plan->user_id = NormalUser::where('Email', $data['user'])->first()->id;
            $international_diplomas_courses_user_plan->sort = $data['sort'];
            $international_diplomas_courses_user_plan->exam = $data['exam'];
            //$international_diplomas_courses_user_plan->published = $published;
            $international_diplomas_courses_user_plan->createtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $international_diplomas_courses_user_plan->published_by = Auth::user()->id;
//                $international_diplomas_courses_user_plan->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $international_diplomas_courses_user_plan->unpublished_by = Auth::user()->id;
//                $international_diplomas_courses_user_plan->unpublished_date = date("Y-m-d H:i:s");
//            }
            $international_diplomas_courses_user_plan->lastedit_by = Auth::user()->id;
            $international_diplomas_courses_user_plan->added_by = Auth::user()->id;
            $international_diplomas_courses_user_plan->lastedit_date = date("Y-m-d H:i:s");
            $international_diplomas_courses_user_plan->added_date = date("Y-m-d H:i:s");
            if ($international_diplomas_courses_user_plan->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.international_diplomas_courses_user_plan'));
                return Redirect::to('admin/international_diplomas_courses_user_plan/create');
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
        $international_diplomas_courses_user_plan = InternationalDiplomsCoursesUsersPlan::findOrFail($id);
        $diplomas = InternationalDiplomas::pluck('name', 'id');
        $courses = Courses::pluck('name', 'id');
        $user=$international_diplomas_courses_user_plan->user;
        $user=isset($international_diplomas_courses_user_plan->user)?$international_diplomas_courses_user_plan->user->Email:'';
        return view('auth.international_diplomas_courses_user_plan.edit', compact('international_diplomas_courses_user_plan','diplomas','courses','user'));
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
        $international_diplomas_courses_user_plan = InternationalDiplomsCoursesUsersPlan::findOrFail($id);
        $rules = array(
            'diploma' => 'required|exists:mysql2.international_diplomas,id',
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
            $international_diplomas_courses_user_plan->diploma_id = $data['diploma'];
            $international_diplomas_courses_user_plan->course_id = $data['course'];
            $international_diplomas_courses_user_plan->user_id = NormalUser::where('Email', $data['user'])->first()->id;
            $international_diplomas_courses_user_plan->sort = $data['sort'];
            $international_diplomas_courses_user_plan->exam = $data['exam'];
//            if ($published == 'yes' && $international_diplomas_courses_user_plan->published=='no') {
//                $international_diplomas_courses_user_plan->published_by = Auth::user()->id;
//                $international_diplomas_courses_user_plan->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $international_diplomas_courses_user_plan->published=='yes') {
//                $international_diplomas_courses_user_plan->unpublished_by = Auth::user()->id;
//                $international_diplomas_courses_user_plan->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $international_diplomas_courses_user_plan->published = $published;
            $international_diplomas_courses_user_plan->lastedit_by = Auth::user()->id;
            $international_diplomas_courses_user_plan->lastedit_date = date("Y-m-d H:i:s");
            if ($international_diplomas_courses_user_plan->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.international_diplomas_courses_user_plan'));
                return Redirect::to("admin/international_diplomas_courses_user_plan/$international_diplomas_courses_user_plan->id/edit");
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
        $international_diplomas_courses_user_plan = InternationalDiplomsCoursesUsersPlan::findOrFail($id);
        $international_diplomas_courses_user_plan->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $international_diplomas_courses_user_plan = InternationalDiplomsCoursesUsersPlan::findOrFail($id);
//            if ($published == 'no') {
//                $international_diplomas_courses_user_plan->published = 'no';
//                $international_diplomas_courses_user_plan->unpublished_by = Auth::user()->id;
//                $international_diplomas_courses_user_plan->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $international_diplomas_courses_user_plan->published = 'yes';
//                $international_diplomas_courses_user_plan->published_by = Auth::user()->id;
//                $international_diplomas_courses_user_plan->published_date = date("Y-m-d H:i:s");
//            }
//            $international_diplomas_courses_user_plan->save();
//        } else {
//            return redirect(404);
//        }
//    }
    public function copy($id)
    {
        $international_diplomas_courses_user_plan = InternationalDiplomsCoursesUsersPlan::findOrFail($id);
        $international_diplomas_courses_user_plan->createtime = date("Y-m-d H:i:s");
        $international_diplomas_courses_user_plan->replicate()->save();
        return Redirect::to('admin/international_diplomas_courses_user_plan/'.$international_diplomas_courses_user_plan->id.'/edit');
    }

}
