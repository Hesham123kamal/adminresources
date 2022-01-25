<?php

namespace App\Http\Controllers\Admin;

use App\UsersCurriculumAnswers;
use App\NormalUser;
use App\Courses;
use App\CoursesCurriculum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UsersCurriculumAnswersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses=Courses::pluck('name','id')->toArray();
        return view('auth.users_curriculum_answers.view',compact('courses'));
    }

    function search(Request $request)
    {
        $data = $request->input();
        $answers=UsersCurriculumAnswers::leftjoin('users', 'users.id', '=', 'users_curriculum_answers.user_id')
            ->leftjoin('courses', 'courses.id', '=', 'users_curriculum_answers.course_id')
            ->select('users_curriculum_answers.*', 'users.Email','courses.name');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $answers = $answers->where('users_curriculum_answers.id', '=', $id);
        }
        if (isset($data['user']) && !empty($data['user'])) {
            $user = $data['user'];
            $answers = $answers->where('users.Email', 'LIKE', "%$user%");
        }
        if (isset($data['course']) && !empty($data['course'])) {
            $course = $data['course'];
            $answers = $answers->where('courses.id', '=', $course);
        }
        if (isset($data['curriculum_id']) && !empty($data['curriculum_id'])) {
            $curriculum_id = $data['curriculum_id'];
            $answers = $answers->where('users_curriculum_answers.curriculum_id', '=', $curriculum_id);
        }
        if (isset($data['right_answers']) && !empty($data['right_answers'])) {
            $right_answers = $data['right_answers'];
            $answers = $answers->where('users_curriculum_answers.right_answers', '=', $right_answers);
        }
        if (isset($data['wrong_answers']) && !empty($data['wrong_answers'])) {
            $wrong_answers = $data['wrong_answers'];
            $answers = $answers->where('users_curriculum_answers.wrong_answers', '=', $wrong_answers);
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $answers = $answers->whereBetween('users_curriculum_answers.createdtime', [$created_time_from .' 00:00:00', $created_time_to.' 23:59:59']);
        }

        $iTotalRecords = $answers->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'users_curriculum_answers.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'users_curriculum_answers.id';
                break;
            case 1:
                $columnName = 'users.Email';
                break;
            case 2:
                $columnName = 'courses.name';
                break;
            case 3:
                $columnName = 'users_curriculum_answers.curriculum_id';
                break;
            case 4:
                $columnName = 'users_curriculum_answers.right_answers';
                break;
            case 5:
                $columnName = 'users_curriculum_answers.wrong_answers';
                break;
            case 6:
                $columnName = 'users_curriculum_answers.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $answers = $answers->where(function ($q) use ($search) {
                $q->where('users_curriculum_answers.right_answers', 'LIKE', "%$search%")
                    ->orWhere('users_curriculum_answers.wrong_answers', 'LIKE', "%$search%")
                    ->orWhere('users.Email', 'LIKE', "%$search%")
                    ->orWhere('courses.name', 'LIKE', "%$search%")
                    ->orWhere('users_curriculum_answers.curriculum_id', '=', $search)
                    ->orWhere('users_curriculum_answers.id', '=', $search);
            });
        }

        $answers = $answers->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($answers as $answer) {
            $user = $answer->Email;
            $course = $answer->name;
            if(PerUser('normal_user_edit') && $user !=''){
                $user= '<a target="_blank" href="' . URL('admin/normal_user/' . $answer->user_id . '/edit') . '">' . $user . '</a>';
            }
            if(PerUser('courses_edit') && $course !=''){
                $course= '<a target="_blank" href="' . URL('admin/courses/' . $answer->course_id . '/edit') . '">' . $course . '</a>';
            }
            $records["data"][] = [
                $answer->id,
                $user,
                $course,
                $answer->curriculum_id,
                $answer->right_answers,
                $answer->wrong_answers,
                $answer->createdtime,
//                '<td class="text-center">
//                                <div class="checkbox-nice checkbox-inline">
//                                    <input data-id="' . $answer->id . '" type="checkbox" ' . ((!PerUser('users_curriculum_answers_publish')) ? 'disabled="disabled"' : '') . ' ' . ((PerUser('users_curriculum_answers_publish')) ? 'class="changeStatues"' : '') . ' ' . (($answer->published == "yes") ? 'checked="checked"' : '') . '  id="checkbox-{{ $post->id }}">
//                                    <label for="checkbox-' . $answer->id . '">
//                                    </label>
//                                </div>
//                            </td>',
                '<div class="btn-group text-center" id="single-order-' . $answer->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('users_curriculum_answers_edit')) ? '<li>
                                            <a href="' . URL('admin/users_curriculum_answers/' . $answer->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('users_curriculum_answers_delete')) ? '<li>
                                            <a class="delete_this" data-id="' . $answer->id . '" >
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
        $courses=Courses::pluck('name', 'id');
        $curriculums_ids=CoursesCurriculum::pluck('id');
        return view('auth.users_curriculum_answers.add',compact('courses','curriculums_ids'));
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
        $rules=array(
            'user' => 'required|exists:mysql2.users,Email',
            'course' => 'required|exists:mysql2.courses,id',
            'curriculum_id' => 'required|exists:mysql2.cources_curriculum,id',
            'session_user_id' => 'nullable|exists:mysql2.session_users,id',
            'curriculum_type' => 'required|in:exam,training',
            'right_answers' => 'required|numeric',
            'wrong_answers' => 'required|numeric',
            'duration_time' => 'required',
        );
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
//            $published = (isset($data['published'])) ? 'yes' : 'no';
            $session_user_id = (isset($data['session_user_id'])) ? $data['session_user_id'] : 0;
            $answer = new UsersCurriculumAnswers();
            $answer->user_id = $user_id;
            $answer->course_id = $data['course'];
            $answer->curriculum_id = $data['curriculum_id'];
            $answer->session_user_id = $session_user_id;
            $answer->right_answers = $data['right_answers'];
            $answer->wrong_answers = $data['wrong_answers'];
            $answer->quetions_numbers = $data['right_answers'] + $data['wrong_answers'];
            $answer->duration_time = $data['duration_time'];
//            $answer->published = $published;
            $answer->createdtime = date("Y-m-d H:i:s");
//            if ($published == 'yes') {
//                $answer->published_by = Auth::user()->id;
//                $answer->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no') {
//                $answer->unpublished_by = Auth::user()->id;
//                $answer->unpublished_date = date("Y-m-d H:i:s");
//            }
            $answer->lastedit_by = Auth::user()->id;
            $answer->added_by = Auth::user()->id;
            $answer->lastedit_date = date("Y-m-d H:i:s");
            $answer->added_date = date("Y-m-d H:i:s");
            if ($answer->save()) {
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.users_curriculum_answers'));
                return Redirect::to('admin/users_curriculum_answers/create');
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
        $answer=UsersCurriculumAnswers::findOrFail($id);
        $courses=Courses::pluck('name', 'id');
        $user=isset($answer->user)?$answer->user->Email:'';
        $curriculums_ids=CoursesCurriculum::pluck('id');
        return view('auth.users_curriculum_answers.edit',compact('answer','courses','curriculums_ids','user'));
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
        $answer = UsersCurriculumAnswers::findOrFail($id);
        $rules=array(
            'user' => 'required|exists:mysql2.users,Email',
            'course' => 'required|exists:mysql2.courses,id',
            'curriculum_id' => 'required|exists:mysql2.cources_curriculum,id',
            'session_user_id' => 'nullable|exists:mysql2.session_users,id',
            'curriculum_type' => 'required|in:exam,training',
            'right_answers' => 'required|numeric',
            'wrong_answers' => 'required|numeric',
            'duration_time' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $user_id=NormalUser::where('Email', $data['user'])->first()->id;
//            $published = (isset($data['published'])) ? 'yes' : 'no';
            $session_user_id = (isset($data['session_user_id'])) ? $data['session_user_id'] : 0;
            $answer->user_id = $user_id;
            $answer->course_id = $data['course'];
            $answer->curriculum_id = $data['curriculum_id'];
            $answer->session_user_id = $session_user_id;
            $answer->right_answers = $data['right_answers'];
            $answer->wrong_answers = $data['wrong_answers'];
            $answer->quetions_numbers = $data['right_answers'] + $data['wrong_answers'];
            $answer->duration_time = $data['duration_time'];
//            if ($published == 'yes' && $answer->published=='no') {
//                $answer->published_by = Auth::user()->id;
//                $answer->published_date = date("Y-m-d H:i:s");
//            }
//            if ($published == 'no' && $answer->published=='yes') {
//                $answer->unpublished_by = Auth::user()->id;
//                $answer->unpublished_date = date("Y-m-d H:i:s");
//            }
//            $answer->published = $published;
            $answer->lastedit_by = Auth::user()->id;
            $answer->lastedit_date = date("Y-m-d H:i:s");
            if ($answer->save()) {
                Session::flash('success', Lang::get('main.update') . Lang::get('main.users_curriculum_answers'));
                return Redirect::to("admin/users_curriculum_answers/$answer->id/edit");
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
        $answer = UsersCurriculumAnswers::findOrFail($id);
        $answer->delete();
    }

//    public function publish(Request $request)
//    {
//        if ($request->ajax()) {
//            $id = $request->input('id');
//            $published = $request->input('published');
//            $answer = UsersCurriculumAnswers::findOrFail($id);
//            if ($published == 'no') {
//                $answer->published = 'no';
//                $answer->unpublished_by = Auth::user()->id;
//                $answer->unpublished_date = date("Y-m-d H:i:s");
//            } elseif ($published == 'yes') {
//                $answer->published = 'yes';
//                $answer->published_by = Auth::user()->id;
//                $answer->published_date = date("Y-m-d H:i:s");
//            }
//            $answer->save();
//        } else {
//            return redirect(404);
//        }
//    }

}
