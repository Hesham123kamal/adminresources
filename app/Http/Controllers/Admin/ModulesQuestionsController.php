<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\CoursesCurriculum;
use App\CurriculumQuestions;
use App\Http\Controllers\Controller;
use App\Mba;
use App\Modules;
use App\ModulesCourses;
use App\ModulesQuestions;
use App\ModulesQuestionsDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ModulesQuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $modules=Mba::pluck('name','id')->toArray();
        return view('auth.modules_questions.view',compact('modules'));

    }

    public function getModulesQuestionsAJAX(Request $request)
    {
        $data = $request->input();
        $modules_questions = ModulesQuestions::select('mba.id', 'mba.name AS module_name', 'mba.questions_numbers', DB::raw("(SELECT COUNT(id) FROM modules_questions WHERE module_id=mba.id AND ISNULL(deleted_at)) AS count_questions"))
            ->join('mba', 'mba.id', '=', 'modules_questions.module_id')
            ->groupBy('modules_questions.module_id');
        if (isset($data['module']) && !empty($data['module'])) {
            $module = $data['module'];
            $modules_questions = $modules_questions->where('modules_questions.module_id', '=', "$module");
        }
        if (isset($data['question_numbers']) && !empty($data['question_numbers'])) {
            $question_numbers = $data['question_numbers'];
            $modules_questions = $modules_questions->where('mba.questions_numbers', '=', $question_numbers);
        }
        if (isset($data['question_count']) && !empty($data['question_count'])) {
            $question_count = $data['question_count'];
            $modules_questions = $modules_questions->where('count_questions', '=', $question_count);
        }

        $iTotalRecords = $modules_questions->count();
        $iDisplayLength = intval($data['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($data['start']);
        $sEcho = intval($data['draw']);
        $records = [];
        $records["data"] = [];
        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;
        $columnName = 'mba.id';
        switch ($data['order'][0]['column']) {
            case 0:
                $columnName = 'mba.id';
                break;
            case 1:
                $columnName = 'mba.name';
                break;
            case 2:
                $columnName = 'mba.questions_numbers';
                break;
            case 3:
                $columnName = 'count_questions';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $modules_questions = $modules_questions->where(function ($q) use ($search) {
                $q->where('mba.name', 'LIKE', "%$search%")
                    ->orWhere('mba.questions_numbers', '=', $search)
                    ->orWhere('count_questions', '=', $search);
            });
        }
        $modules_questions = $modules_questions->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();
        foreach ($modules_questions as $question) {
            $records["data"][] = [
                $question->id,
                $question->module_name,
                $question->questions_numbers,
                $question->count_questions,
                '<div class="btn-group text-center">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('modules_questions_edit')) ? '<li>
                                            <a href="' . URL('admin/modules_questions/' . $question->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . ' 
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('modules_questions_delete')) ? '<li>
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
        //dd(old());
        $modules = Modules::whereNotIn('id', ModulesQuestions::groupBy('module_id')->pluck('module_id')->toArray())->get();
        if (count($modules)) {
            return view('auth.modules_questions.add', compact('modules'));
        } else {
            Session::flash('error', Lang::get('main.error_no_modules_to_add_question_to_it_you_can_edit_module_only'));
            return Redirect::to('admin/modules_questions');
        }

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
        $validator = Validator::make($request->all(),
            array(
                'module_id' => 'required|not_in:0',
                'questions' => 'required',
            ));
        $validator->after(function ($validator) use ($data) {
            if (isset($data['questions'])) {
                $x = 0;
                if (isset($data['questions']['name_ar'])) {
                    foreach ($data['questions']['name_ar'] as $name) {
                        if (empty($name)) {
                            $validator->errors()->add('error_exams_question_name_ar_' . ($x + 1), Lang::get('main.error_exams_question_name_ar') . ($x + 1));
                        }
                        if (isset($data['questions']['type'][$x])) {
                            if ($data['questions']['type'][$x] == 'true_false' && !isset($data['questions']['answers'][$x])) {
                                $validator->errors()->add('error_exams_enter_answers_of_question' . ($x + 1), Lang::get('main.error_exams_enter_answers_of_question') . ($x + 1));
                            }
                            if ($data['questions']['type'][$x] != 'true_false') {
                                $countAnswersAR = 0;
                                $countAnswersEN = 0;
                                if (isset($data['questions_ar']['answers'][$x])) {
                                    foreach ($data['questions_ar']['answers'][$x] as $answer) {
                                        if (!empty($answer)) {
                                            $countAnswersAR++;
                                        }
                                    }
                                } else {

                                    $validator->errors()->add('error_exams_enter_answers_of_question_' . ($x + 1), Lang::get('main.error_exams_enter_answers_of_question') . ($x + 1));
                                }
                                if (isset($data['questions_en']['answers'][$x])) {
                                    foreach ($data['questions_en']['answers'][$x] as $answer) {
                                        if (!empty($answer)) {
                                            $countAnswersEN++;
                                        }
                                    }
                                } else {

                                    $validator->errors()->add('error_exams_enter_answers_of_question_' . ($x + 1), Lang::get('main.error_exams_enter_answers_of_question') . ($x + 1));
                                }
                                //dd ($countAnswers);
                                if ($countAnswersAR < 2) {
                                    $validator->errors()->add('error_exams_question_answer_at_less', Lang::get('main.error_exams_question_answer_at_less'));
                                }
                                if ($countAnswersEN < 2) {
                                    $validator->errors()->add('error_exams_question_answer_at_less', Lang::get('main.error_exams_question_answer_at_less'));
                                }
                                if ($data['questions']['type'][$x] == 'chose_single' && !isset($data['chose_question_answer'][$x])) {
                                    $validator->errors()->add('error_exams_select_answer_of_question_' . ($x + 1), Lang::get('main.error_exams_select_answer_of_question') . ($x + 1));
                                }
                                if ($data['questions']['type'][$x] == 'chose_multiple') {
                                    if (!isset($data['chose_question_answer'][$x])) {
                                        $validator->errors()->add('error_exams_select_answer_of_question_' . ($x + 1), Lang::get('main.error_exams_select_answer_of_question') . ($x + 1));
                                    }
                                    $t = 0;
                                    foreach ($data['chose_question_answer'][$x] as $a) {
                                        $t++;
                                    }
                                    if ($t <= 1) {
                                        $validator->errors()->add('error_exams_select_answer_of_question_at_less_' . ($x + 1), Lang::get('main.error_exams_select_answer_of_question_at_less') . ($x + 1));
                                    }
                                }
                            }
                        } else {
                            $validator->errors()->add('error_exams_enter_type_of_question_' . ($x + 1), Lang::get('main.error_exams_enter_type_of_question') . ($x + 1));
                        }

                        $x++;
                    }
                } else {
                    $validator->errors()->add('error_exams_enter_question_name_ar', Lang::get('main.error_exams_enter_question_name'));
                }
                if (isset($data['questions']['name_en'])) {
                    foreach ($data['questions']['name_en'] as $name) {
                        if (empty($name)) {
                            $validator->errors()->add('error_exams_question_name_en_' . ($x + 1), Lang::get('main.error_exams_question_name_en') . ($x + 1));
                        }
                        $x++;
                    }
                } else {
                    $validator->errors()->add('error_exams_enter_question_name_en', Lang::get('main.error_exams_enter_question_name'));
                }

            }

        });
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $x = 0;
            foreach ($data['questions']['name_ar'] as $name) {
                $type = $data['questions']['type'][$x];
                $answers_ar = isset($data['questions_ar']['answers'][$x]) ? $data['questions_ar']['answers'][$x] : null;
                $answers_en = isset($data['questions_en']['answers'][$x]) ? $data['questions_en']['answers'][$x] : null;
                $answers = (isset($data['questions']['answers'][$x])) ? $data['questions']['answers'][$x] : null;
                $modules_questions = new ModulesQuestions();
                $modules_questions->module_id = $data['module_id'];
                $modules_questions->type = $type;
                $modules_questions->name_ar = $data['questions']['name_ar'][$x];
                $modules_questions->name_en = $data['questions']['name_en'][$x];
                $modules_questions->difficulty_type = $data['questions']['difficulty_type'][$x];
                $modules_questions->save();

                switch ($type) {
                    case'true_false':
                        $modules_questions_details = new ModulesQuestionsDetails();
                        $modules_questions_details->question_id = $modules_questions->id;
                        $modules_questions_details->answer = $answers;
                        $modules_questions_details->save();
                        break;
                    case'chose_single':
                        for ($y = 0; $y < 4; $y++) {
                            $answer = (isset($data['chose_question_answer'][$x]) && $data['chose_question_answer'][$x] == ($y + 1)) ? 1 : 0;
                            if (!empty($answers_ar[$y]) && !empty($answers_en[$y])) {
                                $modules_questions_details = new ModulesQuestionsDetails();
                                $modules_questions_details->question_id = $modules_questions->id;
                                $modules_questions_details->name_ar = $answers_ar[$y];
                                $modules_questions_details->name_en = $answers_en[$y];
                                $modules_questions_details->answer = $answer;
                                $modules_questions_details->save();
                            }
                        }
                        break;
                    case'chose_multiple':
                        for ($y = 0; $y < 4; $y++) {
                            $answer = (isset($data['chose_question_answer'][$x][$y])) ? 1 : 0;
                            if (!empty($answers_ar[$y]) && !empty($answers_en[$y])) {
                                $modules_questions_details = new ModulesQuestionsDetails();
                                $modules_questions_details->question_id = $modules_questions->id;
                                $modules_questions_details->name_ar = $answers_ar[$y];
                                $modules_questions_details->name_en = $answers_en[$y];
                                $modules_questions_details->answer = $answer;
                                $modules_questions_details->save();
                            }
                        }
                        break;
                }
                $x++;
            }
            Session::flash('success', Lang::get('main.insert') . Lang::get('main.modules_questions'));
            return Redirect::to('admin/modules_questions');
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
        $allModules = Modules::where(function ($q) use ($id) {
            $q->where('id', $id)->orWhereNotIn('id', ModulesQuestions::groupBy('module_id')->pluck('module_id')->toArray());
        })->get();
        $modules = Modules::find($id);
        if (count($modules)) {
//            dd($modules);
            $modules_questions = ModulesQuestions::where('module_id', $id)->get();
//            dd($modules_questions);
            return view('auth.modules_questions.edit', compact('modules', 'modules_questions', 'allModules'));
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
        $validator = Validator::make($request->all(),
            array(
                'module_id' => 'required|not_in:0',
                'questions' => 'required',
            ));
        $validator->after(function ($validator) use ($data) {
            if (isset($data['questions'])) {
                $x = 0;
                if (isset($data['questions']['name_ar'])) {
                    foreach ($data['questions']['name_ar'] as $name) {
                        if (empty($name)) {
                            $validator->errors()->add('error_exams_question_name_ar_' . ($x + 1), Lang::get('main.error_exams_question_name_ar') . ($x + 1));
                        }
                        if (isset($data['questions']['type'][$x])) {
                            if ($data['questions']['type'][$x] == 'true_false' && !isset($data['questions']['answers'][$x])) {
                                $validator->errors()->add('error_exams_enter_answers_of_question' . ($x + 1), Lang::get('main.error_exams_enter_answers_of_question') . ($x + 1));
                            }
                            if ($data['questions']['type'][$x] != 'true_false') {
                                $countAnswersAR = 0;
                                $countAnswersEN = 0;
                                if (isset($data['questions_ar']['answers'][$x])) {
                                    foreach ($data['questions_ar']['answers'][$x] as $answer) {
                                        if (!empty($answer)) {
                                            $countAnswersAR++;
                                        }
                                    }
                                } else {

                                    $validator->errors()->add('error_exams_enter_answers_of_question_' . ($x + 1), Lang::get('main.error_exams_enter_answers_of_question') . ($x + 1));
                                }
                                if (isset($data['questions_en']['answers'][$x])) {
                                    foreach ($data['questions_en']['answers'][$x] as $answer) {
                                        if (!empty($answer)) {
                                            $countAnswersEN++;
                                        }
                                    }
                                } else {

                                    $validator->errors()->add('error_exams_enter_answers_of_question_' . ($x + 1), Lang::get('main.error_exams_enter_answers_of_question') . ($x + 1));
                                }
                                //dd ($countAnswers);
                                if ($countAnswersAR < 2) {
                                    $validator->errors()->add('error_exams_question_answer_at_less', Lang::get('main.error_exams_question_answer_at_less'));
                                }
                                if ($countAnswersEN < 2) {
                                    $validator->errors()->add('error_exams_question_answer_at_less', Lang::get('main.error_exams_question_answer_at_less'));
                                }
                                if ($data['questions']['type'][$x] == 'chose_single' && !isset($data['chose_question_answer'][$x])) {
                                    $validator->errors()->add('error_exams_select_answer_of_question_' . ($x + 1), Lang::get('main.error_exams_select_answer_of_question') . ($x + 1));
                                }
                                if ($data['questions']['type'][$x] == 'chose_multiple') {
                                    if (!isset($data['chose_question_answer'][$x])) {
                                        $validator->errors()->add('error_exams_select_answer_of_question_' . ($x + 1), Lang::get('main.error_exams_select_answer_of_question') . ($x + 1));
                                    }
                                    $t = 0;
                                    foreach ($data['chose_question_answer'][$x] as $a) {
                                        $t++;
                                    }
                                    if ($t <= 1) {
                                        $validator->errors()->add('error_exams_select_answer_of_question_at_less_' . ($x + 1), Lang::get('main.error_exams_select_answer_of_question_at_less') . ($x + 1));
                                    }
                                }
                            }
                        } else {
                            $validator->errors()->add('error_exams_enter_type_of_question_' . ($x + 1), Lang::get('main.error_exams_enter_type_of_question') . ($x + 1));
                        }

                        $x++;
                    }
                } else {
                    $validator->errors()->add('error_exams_enter_question_name_ar', Lang::get('main.error_exams_enter_question_name'));
                }
                if (isset($data['questions']['name_en'])) {
                    foreach ($data['questions']['name_en'] as $name) {
                        if (empty($name)) {
                            $validator->errors()->add('error_exams_question_name_en_' . ($x + 1), Lang::get('main.error_exams_question_name_en') . ($x + 1));
                        }
                        $x++;
                    }
                } else {
                    $validator->errors()->add('error_exams_enter_question_name_en', Lang::get('main.error_exams_enter_question_name'));
                }

            }

        });
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput();
        } else {
            $modulesQuestionsIDS = ModulesQuestions::where('module_id', $id)->pluck('id')->toArray();
            if (count($modulesQuestionsIDS)) {
                //ModulesQuestions::destroy($modulesQuestionsIDS);
                //ModulesQuestionsDetails::where('question_id',$modulesQuestionsIDS)->delete();
                ModulesQuestions::whereIn('id', $modulesQuestionsIDS)->update(['deleted_at' => date("Y-m-d H:i:s")]);
                ModulesQuestionsDetails::whereIn('question_id', $modulesQuestionsIDS)->update(['deleted_at' => date("Y-m-d H:i:s")]);
            }
            $x = 0;
            foreach ($data['questions']['name_ar'] as $name) {
                $type = $data['questions']['type'][$x];
                $answers_ar = isset($data['questions_ar']['answers'][$x]) ? $data['questions_ar']['answers'][$x] : null;
                $answers_en = isset($data['questions_en']['answers'][$x]) ? $data['questions_en']['answers'][$x] : null;
                $answers = (isset($data['questions']['answers'][$x])) ? $data['questions']['answers'][$x] : null;
                $modules_questions = new ModulesQuestions();
                $modules_questions->module_id = $data['module_id'];
                $modules_questions->type = $type;
                $modules_questions->name_ar = $data['questions']['name_ar'][$x];
                $modules_questions->name_en = $data['questions']['name_en'][$x];
                $modules_questions->difficulty_type = $data['questions']['difficulty_type'][$x];
                $modules_questions->save();

                switch ($type) {
                    case'true_false':
                        $modules_questions_details = new ModulesQuestionsDetails();
                        $modules_questions_details->question_id = $modules_questions->id;
                        $modules_questions_details->answer = $answers;
                        $modules_questions_details->save();
                        break;
                    case'chose_single':
                        for ($y = 0; $y < 4; $y++) {
                            $answer = (isset($data['chose_question_answer'][$x]) && $data['chose_question_answer'][$x] == ($y + 1)) ? 1 : 0;
                            if (!empty($answers_ar[$y]) && !empty($answers_en[$y])) {
                                $modules_questions_details = new ModulesQuestionsDetails();
                                $modules_questions_details->question_id = $modules_questions->id;
                                $modules_questions_details->name_ar = $answers_ar[$y];
                                $modules_questions_details->name_en = $answers_en[$y];
                                $modules_questions_details->answer = $answer;
                                $modules_questions_details->save();
                            }
                        }
                        break;
                    case'chose_multiple':
                        for ($y = 0; $y < 4; $y++) {
                            $answer = (isset($data['chose_question_answer'][$x][$y])) ? 1 : 0;
                            if (!empty($answers_ar[$y]) && !empty($answers_en[$y])) {
                                $modules_questions_details = new ModulesQuestionsDetails();
                                $modules_questions_details->question_id = $modules_questions->id;
                                $modules_questions_details->name_ar = $answers_ar[$y];
                                $modules_questions_details->name_en = $answers_en[$y];
                                $modules_questions_details->answer = $answer;
                                $modules_questions_details->save();
                            }
                        }
                        break;
                }
                $x++;
            }
            Session::flash('success', Lang::get('main.insert') . Lang::get('main.modules_questions'));
            return Redirect::to('admin/modules_questions/' . $id . '/edit');
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
        $modules_questions = ModulesQuestions::where('module_id', $id)->delete();
        if ($modules_questions) {
            return response()->json(['success' => true, 'message' => 'success'])->setCallback($request->input('callback'));
        }
    }


    public function getModuleQuestions($id){
        $modules_questions = ModulesQuestions::where('module_id', $id)->with('ModulesQuestionsDetails')->get();
        return response()->json($modules_questions);
    }


    public function importModuleQuestions(Request $request)
    {
        dd($request->input());
    }

    public function searchModuleQuestions(Request $request)
    {
//        dd($request->input());
        $search = $request->input('contains');
        $module_id = $request->input('module_id');
        if ($search != null) {
            $modules_questions = ModulesQuestions::where('module_id', $module_id)->where('name_ar', 'like', "%$search%")->orWhere('name_en', 'like', "%$search%")->with('ModulesQuestionsDetails')->get();
            if ($modules_questions->isEmpty()) {
                return response()->json('No search result');
            } else {
                return response()->json($modules_questions);
            }
        } else {
            $modules_questions = ModulesQuestions::where('module_id', $module_id)->with('ModulesQuestionsDetails')->get();
            return response()->json($modules_questions);
        }
    }


    //    public function getModuleCourses($id)
//    {
//        $related_courses = [];
//        $modules_courses = ModulesCourses::where('module_id', '=', $id)->pluck('related_course');
//        foreach ($modules_courses as $course) {
//            $related_courses[] = Courses::where('id', '=', $course)->get();
//        }
//        return response()->json($related_courses);
//    }

//    public function getCourseCurriculums($id)
//    {
//        $course_curriculum = CoursesCurriculum::where('course_id', '=', $id)->where('type', '!=', 'default')->get();
//        return response()->json($course_curriculum);
//    }

//    public function getCurriculumQuestions($id)
//    {
//        $courses_questions = CurriculumQuestions::where('curriculum_id', $id)->with('CurriculumQuestionsDetails')->get();
//        return response()->json($courses_questions);
//    }

}
