<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\CoursesQuestions;
use App\CoursesQuestionsFiles;
use App\CoursesSections;
use App\Http\Controllers\Controller;
use App\NormalUser;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class CoursesQuestionsAndAnswersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = Courses::select('id','name')->get();
        $courses_sections = CoursesSections::select('id','name')->get();
        return view('auth.courses_questions_and_answers.view',compact('courses','courses_sections'));
    }


    function search(Request $request)
    {
        $data = $request->input();
        $courses_questions = CoursesQuestions::select('courses_questions.*','courses.name AS course_name','courses_sections.name AS section_name','users.FullName AS user_name')
            ->leftJoin('courses', 'courses.id', '=', 'courses_questions.course_id')
            ->leftJoin('courses_sections', 'courses_sections.id', '=', 'courses_questions.section_id')
            ->leftJoin('users', 'users.id', '=', 'courses_questions.user_id');
        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $courses_questions = $courses_questions->where('courses_questions.id', '=', $id);
        }
        if (isset($data['course_id']) && !empty($data['course_id'])) {
            $course_id = $data['course_id'];
            $courses_questions = $courses_questions->where('courses.id', 'LIKE', "%$course_id%");
        }
        if (isset($data['section_id']) && !empty($data['section_id'])) {
            $section_id = $data['section_id'];
            $courses_questions = $courses_questions->where('courses_sections.id', 'LIKE', "%$section_id%");
        }
        if (isset($data['user_id']) && !empty($data['user_id'])) {
            $user_id = $data['user_id'];
            $courses_questions = $courses_questions->where('users.FullName', 'LIKE', "%$user_id%");
        }
        if (isset($data['question']) && !empty($data['question'])) {
            $question = $data['question'];
            $courses_questions = $courses_questions->where('courses_questions.question', 'LIKE', "%$question%");
        }
        if (isset($data['answer']) && !empty($data['answer'])) {
            $answer = $data['answer'];
            $courses_questions = $courses_questions->where('courses_questions.answer', 'LIKE', "%$answer%");
        }
        if (isset($data['created_time_from']) && !empty($data['created_time_from']) && isset($data['created_time_to']) && !empty($data['created_time_to'])) {
            $created_time_from = $data['created_time_from'];
            $created_time_to = $data['created_time_to'];
            $courses_questions = $courses_questions->whereBetween('courses_questions.createdtime', [$created_time_from . ' 00:00:00', $created_time_to . ' 23:59:59']);
        }

        $iTotalRecords = $courses_questions->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'courses_questions.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'courses_questions.id';
                break;
            case 1:
                $columnName = 'courses.name';
                break;
            case 2:
                $columnName = 'courses_sections.name';
                break;
            case 3:
                $columnName = 'users.FullName';
                break;
            case 4:
                $columnName = 'courses_questions.question';
                break;
            case 5:
                $columnName = 'courses_questions.answer';
                break;
            case 6:
                $columnName = 'courses_questions.createdtime';
                break;
        }

        $search = $data['search']['value'];
        if ($search) {
            $courses_questions = $courses_questions->where(function ($q) use ($search) {
                $q->where('courses_questions.id', '=', $search)
                    ->orWhere('courses.name', 'Like', "%$search%")
                    ->orWhere('courses_sections.name', 'Like', "%$search%")
                    ->orWhere('users.FullName', 'Like', "%$search%")
                    ->orWhere('courses_questions.answer', 'Like', "%$search%")
                    ->orWhere('courses_questions.question', 'Like', "%$search%");
            });
        }

        $courses_questions = $courses_questions->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();

        foreach ($courses_questions as $question) {
            $records["data"][] = [
                $question->id,
                $question->course_name,
                $question->section_name,
                $question->user_name,
                $question->question,
                $question->answer,
                $question->createdtime,
                '<div class="btn-group text-center" id="single-order-' . $question->id . '">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('courses_QandA_edit')) ? '<li>
                                            <a href="' . URL('admin/courses_QandA/' . $question->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . '
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
        //
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
        $question = CoursesQuestions::find($id);
        $course = Courses::withTrashed()->find($question->course_id);
        $section = CoursesSections::find($question->section_id);
        $user = NormalUser::find($question->user_id);
        $files=CoursesQuestionsFiles::where('course_question_id',$id)->get();
        return view('auth.courses_questions_and_answers.edit',compact('question','course','section','user','files'));
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
        $question = CoursesQuestions::findOrFail($id);
        $rules = array(
            'answer' => 'required',
            'files.*' => 'mimes:jpeg,jpg,png,gif,xlsx,doc,docx,ppt,pdf,pptx,ods,odt,odp,mp3,wav,mpga,ogg|max:20000',
        );
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $question->answer = $data['answer'];
            $question->answertime = date("Y-m-d H:i:s");
            if ($question->save()) {
                $files = $request->file('files');
                if($files) {
                    foreach ($files as $key => $file) {
                        $c_file = new CoursesQuestionsFiles();
                        $fileName = uploadFileToE3melbusiness($file, false,null,false,true);
                        $c_file->file = $fileName;
                        $c_file->course_question_id = $question->id;
                        $c_file->save();
                    }
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.courses_QandA'));
                return Redirect::to("admin/courses_QandA/$question->id/edit");
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
        //
    }

}
