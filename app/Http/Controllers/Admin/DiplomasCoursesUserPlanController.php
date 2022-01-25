<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\DiplomsCoursesUsersPlan;
use App\Diplomas;
use App\NormalUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DiplomasCoursesUserPlanController extends Controller
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
        return view('auth.diplomas_courses_user_plan.view',compact('courses','diplomas'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $diplomas_courses_user_plan = DiplomsCoursesUsersPlan::leftjoin('users','users.id','=','diplomas_courses_user_plan.user_id')
                                ->leftjoin('courses','courses.id','=','diplomas_courses_user_plan.course_id')
                                ->leftjoin('diplomas','diplomas.id','=','diplomas_courses_user_plan.diploma_id')
                                ->select('diplomas_courses_user_plan.*','users.Email as user_email','courses.name as course_name','diplomas.name as diploma_name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $diplomas_courses_user_plan = $diplomas_courses_user_plan->where('diplomas_courses_user_plan.id', '=', "$id");
        }
        if (isset($data['diploma']) && !empty($data['diploma'])) {
            $diploma = $data['diploma'];
            $diplomas_courses_user_plan = $diplomas_courses_user_plan->where('diplomas.name','LIKE', "%$diploma%");
        }
        if (isset($data['diploma_id']) && !empty($data['diploma_id'])) {
            $diploma_id = $data['diploma_id'];
            $diplomas_courses_user_plan = $diplomas_courses_user_plan->where('diplomas.id', $diploma_id);
        }
        if (isset($data['course']) && !empty($data['course'])) {
            $course = $data['course'];
            $diplomas_courses_user_plan = $diplomas_courses_user_plan->where('courses.id','=', $course);
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $diplomas_courses_user_plan = $diplomas_courses_user_plan->where('users.Email','LIKE', "%$user%");
        }
        if (isset($data['exam']) && !empty($data['exam'])) {
            $exam = $data['exam'];
            $diplomas_courses_user_plan = $diplomas_courses_user_plan->where('diplomas_courses_user_plan.exam', 'LIKE', "%$exam%");
        }
        if (isset($data['sort']) && !empty($data['sort'])) {
            $sort = $data['sort'];
            $diplomas_courses_user_plan = $diplomas_courses_user_plan->where('diplomas_courses_user_plan.sort', '=', $sort);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $diplomas_courses_user_plan = $diplomas_courses_user_plan->whereBetween('diplomas_courses_user_plan.createtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $diplomas_courses_user_plan->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'diplomas_courses_user_plan.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'diplomas_courses_user_plan.id';
                break;
            case 1:
                $columnName = 'diplomas_courses_user_plan.exam';
                break;
            case 2:
                $columnName = 'diplomas_courses_user_plan.createtime';
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
                $columnName = 'diplomas_courses_user_plan.sort';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $diplomas_courses_user_plan = $diplomas_courses_user_plan->where(function ($q) use ($search) {
                $q->where('diplomas_courses_user_plan.exam', 'LIKE', "%$search%")
                    ->orWhere('diplomas_courses_user_plan.id', '=', $search)
                    ->orWhere('diplomas.name', 'LIKE', "%$search%")
                    ->orWhere('courses.name', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('diplomas_courses_user_plan.sort', '=', $search);
            });
        }

        $diplomas_courses_user_plan = $diplomas_courses_user_plan->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();
        foreach ($diplomas_courses_user_plan as $diplomas_courses_user_plan) {
            $diploma_name = $diplomas_courses_user_plan->diploma_name;
            $course_name = $diplomas_courses_user_plan->course_name;
            $user_email = $diplomas_courses_user_plan->user_email;
            if(PerUser('diplomas_edit') && $diploma_name !=''){
                $diploma_name= '<a target="_blank" href="' . URL('admin/diplomas/' . $diplomas_courses_user_plan->diploma_id . '/edit') . '">' . $diploma_name . '</a>';
            }
            if(PerUser('courses_edit') && $course_name !=''){
                $course_name= '<a target="_blank" href="' . URL('admin/courses/' . $diplomas_courses_user_plan->course_id . '/edit') . '">' . $course_name . '</a>';
            }
            if(PerUser('normal_user_edit') && $user_email !=''){
                $user_email= '<a target="_blank" href="' . URL('admin/normal_user/' . $diplomas_courses_user_plan->user_id . '/edit') . '">' . $user_email . '</a>';
            }
            $records["data"][] = [
                '<td><input type="checkbox" class="sub_chk" data-id="'.$diplomas_courses_user_plan->id.'"></td>',
                $diplomas_courses_user_plan->id,
                $diplomas_courses_user_plan->exam,
                $diplomas_courses_user_plan->createtime,
                $diploma_name,
                $course_name,
                $user_email,
                $diplomas_courses_user_plan->sort,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $diplomas_courses_user_plan->id . '" type="checkbox" ' . ((!PerUser('diplomas_courses_user_plan_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('diplomas_courses_user_plan_publish')) ? 'class="changeStatues"' : '') . ' ' . (($diplomas_courses_user_plan->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $diplomas_courses_user_plan->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $diplomas_courses_user_plan->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('diplomas_courses_user_plan_edit')) ? '<li>
                                            <a href="' . URL('admin/diplomas_courses_user_plan/' . $diplomas_courses_user_plan->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('diplomas_courses_user_plan_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $diplomas_courses_user_plan->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete') . '
                                            </a>
                                        </li>' : '') . '
                                        ' . ((PerUser('diplomas_courses_user_plan_delete_all')) ? '<li>
                                            <a class="delete_all_this" data-id="' . $diplomas_courses_user_plan->id . '" >
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.delete_all') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('diplomas_courses_user_plan_copy')) ? '<li>
                                            <a href="'.URL('admin/diplomas_courses_user_plan/copy/'.$diplomas_courses_user_plan->id).'" >
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
        return view('auth.diplomas_courses_user_plan.add', compact('diplomas', 'courses'));
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
            $diplomas_courses_user_plan = new DiplomsCoursesUsersPlan();
            $diplomas_courses_user_plan->diploma_id = $data['diploma'];
            $diplomas_courses_user_plan->course_id = $data['course'];
            $diplomas_courses_user_plan->user_id = NormalUser::where('Email', $data['user'])->first()->id;
            $diplomas_courses_user_plan->sort = $data['sort'];
            $diplomas_courses_user_plan->exam = $data['exam'];
            //$diplomas_courses_user_plan->published = $published;
            $diplomas_courses_user_plan->createtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $diplomas_courses_user_plan->published_by = Auth::user()->id;
//                $diplomas_courses_user_plan->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $diplomas_courses_user_plan->unpublished_by = Auth::user()->id;
//                $diplomas_courses_user_plan->unpublished_date = date("Y-m-d H:i:s");
//            }
            $diplomas_courses_user_plan->lastedit_by = Auth::user()->id;
            $diplomas_courses_user_plan->added_by = Auth::user()->id;
            $diplomas_courses_user_plan->lastedit_date = date("Y-m-d H:i:s");
            $diplomas_courses_user_plan->added_date = date("Y-m-d H:i:s");
            if ($diplomas_courses_user_plan->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.diplomas_courses_user_plan'));
                return Redirect::to('admin/diplomas_courses_user_plan/create');
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
        $diplomas_courses_user_plan = DiplomsCoursesUsersPlan::findOrFail($id);
        $diplomas = Diplomas::pluck('name', 'id');
        $courses = Courses::pluck('name', 'id');
        $user=$diplomas_courses_user_plan->user;
        $user=isset($diplomas_courses_user_plan->user)?$diplomas_courses_user_plan->user->Email:'';
        return view('auth.diplomas_courses_user_plan.edit', compact('diplomas_courses_user_plan','diplomas','courses','user'));
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
        $diplomas_courses_user_plan = DiplomsCoursesUsersPlan::findOrFail($id);
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
            $diplomas_courses_user_plan->diploma_id = $data['diploma'];
            $diplomas_courses_user_plan->course_id = $data['course'];
            $diplomas_courses_user_plan->user_id = NormalUser::where('Email', $data['user'])->first()->id;
            $diplomas_courses_user_plan->sort = $data['sort'];
            $diplomas_courses_user_plan->exam = $data['exam'];
//            if ($published == 'yes' && $diplomas_courses_user_plan->published=='no') {
//                $diplomas_courses_user_plan->published_by = Auth::user()->id;
//                $diplomas_courses_user_plan->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $diplomas_courses_user_plan->published=='yes') {
//                $diplomas_courses_user_plan->unpublished_by = Auth::user()->id;
//                $diplomas_courses_user_plan->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $diplomas_courses_user_plan->published = $published;
            $diplomas_courses_user_plan->lastedit_by = Auth::user()->id;
            $diplomas_courses_user_plan->lastedit_date = date("Y-m-d H:i:s");
            if ($diplomas_courses_user_plan->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.diplomas_courses_user_plan'));
                return Redirect::to("admin/diplomas_courses_user_plan/$diplomas_courses_user_plan->id/edit");
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
        $diplomas_courses_user_plan = DiplomsCoursesUsersPlan::findOrFail($id);
        $diplomas_courses_user_plan->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $diplomas_courses_user_plan = DiplomsCoursesUsersPlan::findOrFail($id);
//            if ($published == 'no') {
//                $diplomas_courses_user_plan->published = 'no';
//                $diplomas_courses_user_plan->unpublished_by = Auth::user()->id;
//                $diplomas_courses_user_plan->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $diplomas_courses_user_plan->published = 'yes';
//                $diplomas_courses_user_plan->published_by = Auth::user()->id;
//                $diplomas_courses_user_plan->published_date = date("Y-m-d H:i:s");
//            }
//            $diplomas_courses_user_plan->save();
//        } else {
//            return redirect(404);
//        }
//    }
    public function copy($id)
    {
        $diplomas_courses_user_plan = DiplomsCoursesUsersPlan::findOrFail($id);
        $diplomas_courses_user_plan->createtime = date("Y-m-d H:i:s");
        $diplomas_courses_user_plan->replicate()->save();
        return Redirect::to('admin/diplomas_courses_user_plan/'.$diplomas_courses_user_plan->id.'/edit');
    }

    public function delete_all(Request $request)
    {
        if ($request->ajax()) {
            $id = $request->input('id');
            $diplomas_courses_user_plan = DiplomsCoursesUsersPlan::findOrFail($id);
            DiplomsCoursesUsersPlan::where('diploma_id',$diplomas_courses_user_plan->diploma_id)
                ->where('user_id',$diplomas_courses_user_plan->user_id)->delete();

        } else {
            return redirect(404);
        }
    }

    public function delete_selected(Request $request)
    {

            $ids = $request->input('ids');
            DiplomsCoursesUsersPlan::whereIn('id',explode(",",$ids))->delete();

    }


}
