<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\CoursesCurriculum;
use App\CurriculumQuestions;
use App\CurriculumQuestionsDetails;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CoursesQuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = Courses::select('courses.*')->get();
        return view('auth.courses_questions.view', compact('courses'));
    }

    public function search(Request $request)
    {
        $courses_questions = CoursesCurriculum::select('cources_curriculum.*', 'courses.name AS course_name')
            ->leftJoin('courses', 'course_id', '=', 'courses.id')->whereIn('type', ['exam', 'training']);
        $data = $request->input();

        if (isset($data['id']) && !empty($data['id'])) {
            $id = $data['id'];
            $courses_questions = $courses_questions->where('cources_curriculum.id', '=', "$id");
        }
        if (isset($data['course_name']) && !empty($data['course_name'])) {
            $course_name = $data['course_name'];
            $courses_questions = $courses_questions->where('courses.name', 'LIKE', "%$course_name%");
        }
        if (isset($data['name']) && !empty($data['name'])) {
            $name = $data['name'];
            $courses_questions = $courses_questions->where('cources_curriculum.name', 'LIKE', "%$name%");
        }
        if (isset($data['type']) && !empty($data['type'])) {
            $type = $data['type'];
            $courses_questions = $courses_questions->where('cources_curriculum.type', '=', $type);
        }
        if (isset($data['questions_numbers']) && !empty($data['questions_numbers'])) {
            $questions_numbers = $data['questions_numbers'];
            $courses_questions = $courses_questions->where('cources_curriculum.questions_numbers', '=', $questions_numbers);
        }
        if (isset($data['questions_count']) && !empty($data['questions_count'])) {
            $questions_count = $data['questions_count'];
            $courses_questions = $courses_questions->where('cources_curriculum.questions_count', '=', $questions_count);
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
        $columnName = 'cources_curriculum.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'cources_curriculum.id';
                break;
            case 1:
                $columnName = 'courses.name';
                break;
            case 2:
                $columnName = 'cources_curriculum.name';
                break;
            case 3:
                $columnName = 'cources_curriculum.type';
                break;
            case 4:
                $columnName = 'cources_curriculum.questions_numbers';
                break;
            case 5:
                $columnName = 'questions_count';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $courses_questions = $courses_questions->where(function ($q) use ($search) {
                $q->where('cources_curriculum.name', 'LIKE', "%$search%")
                    ->orWhere('courses.name', 'LIKE', "%$search%")
                    ->orWhere('cources_curriculum.type', 'LIKE', "%$search%")
                    ->orWhere('cources_curriculum.questions_numbers', '=', $search)
                    ->orWhere('questions_count', '=', $search)
                    ->orWhere('cources_curriculum.id', '=', $search);
            });
        }
        $courses_questions = $courses_questions->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();
        foreach ($courses_questions as $question) {
            $records["data"][] = [
                $question->id,
                $question->course_name,
                $question->name,
                $question->type,
                $question->questions_numbers,
                $question->questions_count,
                '<div class="btn-group text-center">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('courses_questions_edit')) ? '<li>
                                            <a href="' . URL('admin/courses_questions/' . $question->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . ' 
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('courses_questions_delete')) ? '<li>
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
        //dd($request->input());
        $data = $request->input();
        //dd($data);
        $validator = Validator::make($request->all(),
            array(
                'questions' => 'required',
            ));
        $validator->after(function ($validator) use ($data) {
            if (isset($data['questions'])) {
                $x = 0;
                if (isset($data['questions']['name'])) {
                    foreach ($data['questions']['name'] as $name) {
                        if (empty($name)) {
                            $validator->errors()->add('error_courses_questions_question_name_' . ($x + 1), Lang::get('main.error_courses_questions_question_name') . ($x + 1));
                        }
                        if (isset($data['questions']['type'][$x])) {
                            if ($data['questions']['type'][$x] == 'true_false' && !isset($data['questions']['answers'][$x])) {
                                $validator->errors()->add('error_courses_questions_enter_answers_of_question' . ($x + 1), Lang::get('main.error_courses_questions_enter_answers_of_question') . ($x + 1));
                            }
                            if ($data['questions']['type'][$x] != 'true_false') {
                                $countAnswers = 0;
                                if (isset($data['questions']['answers'][$x])) {
                                    foreach ($data['questions']['answers'][$x] as $answer) {
                                        if (!empty($answer)) {
                                            $countAnswers++;
                                        }
                                    }
                                } else {
                                    $validator->errors()->add('error_courses_questions_enter_answers_of_question_' . ($x + 1), Lang::get('main.error_courses_questions_enter_answers_of_question') . ($x + 1));
                                }
                                //dd ($countAnswers);
                                if ($countAnswers < 2) {
                                    $validator->errors()->add('error_courses_questions_question_answer_at_less', Lang::get('main.error_courses_questions_question_answer_at_less'));
                                }
                                if ($data['questions']['type'][$x] == 'chose_single' && !isset($data['chose_question_answer'][$x])) {
                                    $validator->errors()->add('error_courses_questions_select_answer_of_question_' . ($x + 1), Lang::get('main.error_courses_questions_select_answer_of_question') . ($x + 1));
                                }
                                if ($data['questions']['type'][$x] == 'chose_multiple') {
                                    if (!isset($data['chose_question_answer'][$x])) {
                                        $validator->errors()->add('error_courses_questions_select_answer_of_question_' . ($x + 1), Lang::get('main.error_courses_questions_select_answer_of_question') . ($x + 1));
                                    }
                                    $t = 0;
                                    foreach ($data['chose_question_answer'][$x] as $a) {
                                        $t++;
                                    }
                                    if ($t <= 1) {
                                        $validator->errors()->add('error_courses_questions_select_answer_of_question_at_less_' . ($x + 1), Lang::get('main.error_courses_questions_select_answer_of_question_at_less') . ($x + 1));
                                    }
                                }
                            }
                        } else {
                            $validator->errors()->add('error_courses_questions_enter_type_of_question_' . ($x + 1), Lang::get('main.error_courses_questions_enter_type_of_question') . ($x + 1));
                        }

                        $x++;
                    }
                } else {
                    $validator->errors()->add('error_courses_questions_enter_question_name', Lang::get('main.error_courses_questions_enter_question_name'));
                }

            }

        });
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $active = (isset($data['active'])) ? 1 : 0;
            $courses_questions = new CurriculumQuestions();
            $courses_questions->active = $active;
            if ($active == 1) {
                $courses_questions->active_by = Auth::user()->id;
                $courses_questions->active_date = date("Y-m-d H:i:s");
            }
            if ($active == 0) {
                $courses_questions->unactive_by = Auth::user()->id;
                $courses_questions->unactive_date = date("Y-m-d H:i:s");
            }

            $courses_questions->add_by = Auth::user()->id;
            $courses_questions->add_date = date("Y-m-d H:i:s");
            if ($courses_questions->save()) {
                $x = 0;
                foreach ($data['questions']['name'] as $name) {
                    $type = $data['questions']['type'][$x];
                    $answers = $data['questions']['answers'][$x];
                    $question = CurriculumQuestions::where('exam_id', $courses_questions->id)->where('type', $type)->where('name', $name)->get();
                    if (!count($question)) {
                        $question = new CurriculumQuestions();
                        $question->exam_id = $courses_questions->id;
                        $question->type = $type;
                        $question->name = $name;
                        $question->save();
                        switch ($type) {
                            case'true_false':
                                $questionDetails = new CurriculumQuestionsDetails();
                                $questionDetails->quetion_id = $question->id;
                                $questionDetails->answer = $answers;
                                $questionDetails->save();
                                break;
                            case'chose_single':
                                for ($y = 0; $y < 4; $y++) {
                                    $answer = (isset($data['chose_question_answer'][$x]) && $data['chose_question_answer'][$x] == ($y + 1)) ? 1 : 0;
                                    $nameD = $answers[$y];
                                    if (!empty($nameD)) {
                                        $questionDetails = new CurriculumQuestionsDetails();
                                        $questionDetails->quetion_id = $question->id;
                                        $questionDetails->name = $nameD;
                                        $questionDetails->answer = $answer;
                                        $questionDetails->save();
                                    }
                                }
                                break;
                            case'chose_multiple':
                                for ($y = 0; $y < 4; $y++) {
                                    $answer = (isset($data['chose_question_answer'][$x][$y])) ? 1 : 0;
                                    $nameD = $answers[$y];
                                    if (!empty($nameD)) {
                                        $questionDetails = new CurriculumQuestionsDetails();
                                        $questionDetails->quetion_id = $question->id;
                                        $questionDetails->name = $nameD;
                                        $questionDetails->answer = $answer;
                                        $questionDetails->save();
                                    }
                                }
                                break;
                        }
                    }
                    $x++;
                }
                Session::flash('success', Lang::get('main.insert') . Lang::get('main.courses_questions'));
                return Redirect::to('admin/courses_questions/create');
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
        $courses = Courses::get();
        $course_question = CoursesCurriculum::find($id);
//        dd($course_question);
        if (count($course_question)) {
//            dd($course_question);
            $courses_questions = CurriculumQuestions::where('curriculum_id', $id)->get();
//            dd($courses_questions);
            return view('auth.courses_questions.edit', compact('course_question', 'courses_questions', 'courses'));
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
        //dd($data);
        $validator = Validator::make($request->all(),
            array(
                'questions' => 'required',
            ));
        $validator->after(function ($validator) use ($data, $request) {
            if (isset($data['questions'])) {
                $x = 1;
                if (isset($data['questions']['name'])) {
                    foreach ($data['questions']['name'] as $key=>$name) {
                        if (Input::hasFile('image.' . $key)) {
                            $valid = Validator::make($request->all(), array(
                                'image.' . $key => 'required|mimes:jpeg,bmp,png'
                            ));
                            if ($valid->fails()) {
                                $validator->errors()->add('image_' . $key, Lang::get('main.please_upload_correct_image'));
                            }
                        }
                        if (empty($name)) {
                            $validator->errors()->add('error_courses_questions_question_name_' . ($x), Lang::get('main.error_courses_questions_question_name') . ($x));
                        }
                        if (isset($data['questions']['type'][$key])) {
                            if ($data['questions']['type'][$key] == 'true_false' && !isset($data['questions']['answers'][$key])) {
                                $validator->errors()->add('error_courses_questions_enter_answers_of_question' . ($x + 1), Lang::get('main.error_courses_questions_enter_answers_of_question') . ($x + 1));
                            }
                            if ($data['questions']['type'][$key] != 'true_false') {
                                $countAnswers = 0;
                                if (isset($data['questions']['answers'][$key])) {
                                    foreach ($data['questions']['answers'][$key] as $answer) {
                                        if (!empty($answer)) {
                                            $countAnswers++;
                                        }
                                    }
                                }elseif(isset($request->file('questions')['answers'][$key])){
                                    foreach($request->file('questions')['answers'][$key] as $answer){
                                        if(substr($answer->getMimeType(), 0, 5) != 'image') {
                                            $validator->errors()->add('error_courses_questions_question_answer_must_be_image', Lang::get('main.error_courses_questions_question_answer_must_be_image'));
                                            break;
                                        }
                                        if(!empty($answer)){
                                            $countAnswers++;
                                        }
                                    }
                                }
                                elseif(isset($data['questions']['images_answers'][$key])){
                                    foreach($data['questions']['images_answers'][$key] as $answer){
                                        if(!empty($answer)){
                                            $countAnswers++;
                                        }
                                    }
                                } else {
                                    $validator->errors()->add('error_courses_questions_enter_answers_of_question_' . ($x), Lang::get('main.error_courses_questions_enter_answers_of_question') . ($x));
                                }
                                //dd ($countAnswers);
                                if ($countAnswers < 2) {
                                    $validator->errors()->add('error_courses_questions_question_answer_at_less', Lang::get('main.error_courses_questions_question_answer_at_less'));
                                }
                                if (($data['questions']['type'][$key]=='chose_single' || $data['questions']['type'][$key]=='chose_single_with_images') && !isset($data['chose_question_answer'][$key])) {
                                    $validator->errors()->add('error_courses_questions_select_answer_of_question_' . ($x), Lang::get('main.error_courses_questions_select_answer_of_question') . ($x));
                                }
                                if ($data['questions']['type'][$key] == 'chose_multiple' || $data['questions']['type'][$key]=='chose_multiple_with_images') {
                                    if (!isset($data['chose_question_answer'][$key])) {
                                        $validator->errors()->add('error_courses_questions_select_answer_of_question_' . ($x), Lang::get('main.error_courses_questions_select_answer_of_question') . ($x));
                                    }
                                    if((isset($data['chose_question_answer'][$key]) && count($data['chose_question_answer'][$key])<=1) || !isset($data['chose_question_answer'][$key])){
                                        $validator->errors()->add('error_courses_questions_select_answer_of_question_at_less_'.($x), Lang::get('main.error_courses_questions_select_answer_of_question_at_less').($x));
                                    }
                                }
                            }
                        } else {
                            $validator->errors()->add('error_courses_questions_enter_type_of_question_' . ($x), Lang::get('main.error_courses_questions_enter_type_of_question') . ($x));
                        }

                        $x++;
                    }
                } else {
                    $validator->errors()->add('error_courses_questions_enter_question_name', Lang::get('main.error_courses_questions_enter_question_name'));
                }

            }

        });
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $courses_curriculum = CoursesCurriculum::find($id);
            if (count($courses_curriculum)) {
                CurriculumQuestionsDetails::whereIn('quetion_id', CurriculumQuestions::where('curriculum_id', $courses_curriculum->id)->pluck('id')->toArray())->delete();
                CurriculumQuestions::where('curriculum_id', $courses_curriculum->id)->delete();
                //$x = 0;
                foreach ($data['questions']['name'] as $key=>$name) {
                    $type = $data['questions']['type'][$key];
                    $images_answers=array();
                    $answers=array();
                    //$answers = $data['questions']['answers'][$key];
                    if(isset($data['questions']['answers'][$key])){
                        $answers= $data['questions']['answers'][$key];
                    }
                    elseif(isset($request->file('questions')['answers'][$key])){
                        $answers= $request->file('questions')['answers'][$key];
                    }
                    elseif(isset($data['questions']['images_answers'][$key])){
                        $images_answers= $data['questions']['images_answers'][$key];
                    }
                    $curriculum_question = new CurriculumQuestions();
                    $curriculum_question->curriculum_id = $courses_curriculum->id;
                    $curriculum_question->type = $type;
                    $curriculum_question->name = $name;
                    if (Input::hasFile('image.' . $key)) {
                        $image = $request->file('image.' . $key);
                        $fileImage = uploadFileToE3melbusiness($image, 'exams_question');
                        $curriculum_question->image = $fileImage;
                    }
                    $curriculum_question->save();
                    switch ($type) {
                        case'true_false':
                            $curriculum_question_details = new CurriculumQuestionsDetails();
                            $curriculum_question_details->quetion_id = $curriculum_question->id;
                            $curriculum_question_details->answer = $answers;
                            $curriculum_question_details->save();
                            break;
                        case'chose_single':
                            for ($y = 0; $y < 4; $y++) {
                                $answer = (isset($data['chose_question_answer'][$key]) && $data['chose_question_answer'][$key] == ($y + 1)) ? 1 : 0;
                                $nameD = (isset($answers[$y])) ? $answers[$y] : 0;
                                if (!empty($nameD)) {
                                    $curriculum_question_details = new CurriculumQuestionsDetails();
                                    $curriculum_question_details->quetion_id = $curriculum_question->id;
                                    $curriculum_question_details->name = $nameD;
                                    $curriculum_question_details->answer = $answer;
                                    $curriculum_question_details->save();
                                }
                            }
                            break;
                        case'chose_multiple':
                            for ($y = 0; $y < 4; $y++) {
                                $answer = (isset($data['chose_question_answer'][$key][$y])) ? 1 : 0;
                                $nameD = (isset($answers[$y])) ? $answers[$y] : 0;
                                if (!empty($nameD)) {
                                    $curriculum_question_details = new CurriculumQuestionsDetails();
                                    $curriculum_question_details->quetion_id = $curriculum_question->id;
                                    $curriculum_question_details->name = $nameD;
                                    $curriculum_question_details->answer = $answer;
                                    $curriculum_question_details->save();
                                }
                            }
                            break;
                        case'chose_single_with_images':
                            for($y=0;$y<4;$y++){
                                if(isset($answers[$y])){
                                    $nameD=$answers[$y];
                                }
                                elseif(isset($images_answers[$y])){
                                    $nameD=$images_answers[$y];
                                }
                                else{
                                    $nameD='';
                                }
                                //$nameD=isset($answers[$y])?$answers[$y]:'';
                                if(!empty($nameD)) {
                                    $answer=(isset($data['chose_question_answer'][$key])&&$data['chose_question_answer'][$key]==($y+1))?1:0;
                                    $curriculum_question_details = new CurriculumQuestionsDetails();
                                    $curriculum_question_details->quetion_id = $curriculum_question->id;
                                    if(isset($images_answers[$y])){
                                        $img_name = $images_answers[$y];
                                        $curriculum_question_details->image = $img_name;
                                    }
                                    elseif(isset($answers[$y])) {
                                        $img = $answers[$y];
                                        $img_name = uploadFileToE3melbusiness($img);
                                        $curriculum_question_details->image = $img_name;
                                    }
                                    $curriculum_question_details->name = '';
                                    $curriculum_question_details->answer = $answer;
                                    $curriculum_question_details->save();
                                }
                            }
                            break;
                        case'chose_multiple_with_images':
                            for($y=0;$y<4;$y++){
                                $answer=(isset($data['chose_question_answer'][$key][$y]))?1:0;
                                if(isset($answers[$y])){
                                    $nameD=$answers[$y];
                                }
                                elseif(isset($images_answers[$y])){
                                    $nameD=$images_answers[$y];
                                }
                                else{
                                    $nameD='';
                                }
                                //$nameD=isset($answers)?$answers[$y]:'';
                                if(!empty($nameD)) {
                                    $curriculum_question_details = new CurriculumQuestionsDetails();
                                    $curriculum_question_details->quetion_id = $curriculum_question->id;
                                    if(isset($images_answers[$y])){
                                        $img_name = $images_answers[$y];
                                        $curriculum_question_details->image = $img_name;
                                    }
                                    elseif(isset($answers[$y])) {
                                        $img = $answers[$y];
                                        $img_name = uploadFileToE3melbusiness($img);
                                        $curriculum_question_details->image = $img_name;
                                    }
                                    $curriculum_question_details->name = '';
                                    $curriculum_question_details->answer = $answer;
                                    $curriculum_question_details->save();
                                }
                            }
                            break;
                    }

                    //$x++;
                }
                Session::flash('success', Lang::get('main.update') . Lang::get('main.courses_questions'));
                return Redirect::to('admin/courses_questions/' . $id . '/edit');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        //
        return abort(404);
    }


    public function getCourseCurriculums($id)
    {
        $course_curriculum = CoursesCurriculum::where('course_id', '=', $id)->where('type', '!=', 'default')->get();
        return response()->json($course_curriculum);
    }

    public function getCurriculumQuestions($id)
    {
        $courses_questions = CurriculumQuestions::where('curriculum_id', $id)->with('CurriculumQuestionsDetails')->get();
        return response()->json($courses_questions);
    }

    public function importCourseQuestions(Request $request)
    {
        dd($request->input());
    }

    public function searchCurriculumQuestions(Request $request)
    {
        $search = $request->input('contains');
        $curriculum_id = $request->input('curriculum_id');
        if ($search != null) {
            $courses_questions = CurriculumQuestions::where('curriculum_id', $curriculum_id)->where('name', 'like', "%$search%")->with('CurriculumQuestionsDetails')->get();
            if ($courses_questions->isEmpty()) {
                return response()->json('No search result');
            } else {
                return response()->json($courses_questions);
            }
        } else {
            $courses_questions = CurriculumQuestions::where('curriculum_id', $curriculum_id)->with('CurriculumQuestionsDetails')->get();
            return response()->json($courses_questions);
        }
    }


}

