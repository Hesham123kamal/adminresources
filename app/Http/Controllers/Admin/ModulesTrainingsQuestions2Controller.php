<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ModulesTrainingsQuestionsRequest;
use App\Mba;
use App\Modules;
use App\ModulesTrainings;
use App\ModulesTrainingsQuestions;
use App\ModulesTrainingsQuestionsDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ModulesTrainingsQuestions2Controller extends Controller

{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $modules=Mba::pluck('name', 'id');
        return view('auth.modules_trainings_questions2.view',compact('modules'));

    }

    public function getModuleTrainingsQuestionsAJAX(Request $request)
    {
        $data = $request->input();
        $modules_trainings_questions = ModulesTrainings::select('modules_trainings.*', 'mba.name AS module_name', 'modules_trainings.count_questions')
            ->join('mba', 'mba.id', '=', 'modules_trainings.module_id');
        if (isset($data['module_training_name']) && !empty($data['module_training_name'])) {
            $module_training_name = $data['module_training_name'];
            $modules_trainings_questions = $modules_trainings_questions->where('modules_trainings.name', 'Like', "%$module_training_name%");
        }
        if (isset($data['module']) && !empty($data['module'])) {
            $module = $data['module'];
            $modules_trainings_questions = $modules_trainings_questions->where('modules_trainings.module_id', '=', $module);
        }
        if (isset($data['question_numbers']) && !empty($data['question_numbers'])) {
            $question_numbers = $data['question_numbers'];
            $modules_trainings_questions = $modules_trainings_questions->where('modules_trainings.questions_numbers', '=', $question_numbers);
        }
        if (isset($data['question_count']) && !empty($data['question_count'])) {
            $question_count = $data['question_count'];
            $modules_trainings_questions = $modules_trainings_questions->where('count_questions', '=', $question_count);
        }

        $iTotalRecords = $modules_trainings_questions->count();
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
                $columnName = 'modules_trainings.id';
                break;
            case 1:
                $columnName = 'modules_trainings.name';
                break;
            case 2:
                $columnName = 'mba.name';
                break;
            case 3:
                $columnName = 'modules_trainings.questions_numbers';
                break;
            case 4:
                $columnName = 'modules_trainings.count_questions';
                break;
        }
        $search = $data['search']['value'];
        if ($search) {
            $modules_trainings_questions = $modules_trainings_questions->where(function ($q) use ($search) {
                $q->where('mba.name', 'LIKE', "%$search%")
                    ->orWhere('mba.questions_numbers', '=', $search)
                    ->orWhere('count_questions', '=', $search);
            });
        }
        $modules_trainings_questions = $modules_trainings_questions->orderBy($columnName, $data['order'][0]['dir'])->skip($iDisplayStart)->take($iDisplayLength)
            ->get();
        foreach ($modules_trainings_questions as $question) {
            $records["data"][] = [
                $question->id,
                $question->name,
                $question->module_name,
                $question->questions_numbers,
                $question->count_questions,
                '<div class="btn-group text-center">
                                    <button class="btn green btn-xs btn-outline dropdown-toggle" data-toggle="dropdown">' . Lang::get('main.action') . '
                                        <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                    ' . ((PerUser('modules_trainings_questions_edit')) ? '<li>
                                            <a href="' . URL('admin/modules_trainings_questions2/' . $question->id . '/edit') . '">
                                                <i class="fa fa-comments-o"></i> ' . Lang::get('main.edit') . ' 
                                            </a>
                                        </li>' : '') . '
                                    ' . ((PerUser('modules_trainings_questions_delete')) ? '<li>
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
        $modules = Modules::whereNotIn('id', ModulesTrainingsQuestions::groupBy('module_id')->pluck('module_id')->toArray())->get();
        if (count($modules)) {
            return view('auth.modules_trainings_questions2.add', compact('modules'));
        } else {
            Session::flash('error', Lang::get('main.error_no_modules_to_add_question_to_it_you_can_edit_module_only'));
            return Redirect::to('admin/modules_trainings_questions2');
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
                $modules_trainings_questions = new ModulesTrainingsQuestions();
                $modules_trainings_questions->module_id = $data['module_id'];
                $modules_trainings_questions->type = $type;
                $modules_trainings_questions->name_ar = $data['questions']['name_ar'][$x];
                $modules_trainings_questions->name_en = $data['questions']['name_en'][$x];
                $modules_trainings_questions->difficulty_type = $data['questions']['difficulty_type'][$x];
                $modules_trainings_questions->save();

                switch ($type) {
                    case'true_false':
                        $modules_trainings_questions_details = new ModulesTrainingsQuestionsDetails();
                        $modules_trainings_questions_details->question_id = $modules_trainings_questions->id;
                        $modules_trainings_questions_details->answer = $answers;
                        $modules_trainings_questions_details->save();
                        break;
                    case'chose_single':
                        for ($y = 0; $y < 4; $y++) {
                            $answer = (isset($data['chose_question_answer'][$x]) && $data['chose_question_answer'][$x] == ($y + 1)) ? 1 : 0;
                            if (!empty($answers_ar[$y]) && !empty($answers_en[$y])) {
                                $modules_trainings_questions_details = new ModulesTrainingsQuestionsDetails();
                                $modules_trainings_questions_details->question_id = $modules_trainings_questions->id;
                                $modules_trainings_questions_details->name_ar = $answers_ar[$y];
                                $modules_trainings_questions_details->name_en = $answers_en[$y];
                                $modules_trainings_questions_details->answer = $answer;
                                $modules_trainings_questions_details->save();
                            }
                        }
                        break;
                    case'chose_multiple':
                        for ($y = 0; $y < 4; $y++) {
                            $answer = (isset($data['chose_question_answer'][$x][$y])) ? 1 : 0;
                            if (!empty($answers_ar[$y]) && !empty($answers_en[$y])) {
                                $modules_trainings_questions_details = new ModulesTrainingsQuestionsDetails();
                                $modules_trainings_questions_details->question_id = $modules_trainings_questions->id;
                                $modules_trainings_questions_details->name_ar = $answers_ar[$y];
                                $modules_trainings_questions_details->name_en = $answers_en[$y];
                                $modules_trainings_questions_details->answer = $answer;
                                $modules_trainings_questions_details->save();
                            }
                        }
                        break;
                }
                $x++;
            }
            Session::flash('success', Lang::get('main.insert') . Lang::get('main.modules_trainings_questions'));
            return Redirect::to('admin/modules_trainings_questions2');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($type, $id)
    {
        switch ($type) {
            case 'true_false':
                $returnHTML = view('auth.modules_trainings_questions2.new_questions.true_false')->render();
                return response()->json(['html'=>$returnHTML], 200);
                break;
            case 'chose_single':
                $returnHTML = view('auth.modules_trainings_questions2.new_questions.chose_single')->render();
                return response()->json(['html'=>$returnHTML], 200);
                break;
            case 'chose_multiple':
                $returnHTML = view('auth.modules_trainings_questions2.new_questions.chose_multiple')->render();
                return response()->json(['html'=>$returnHTML], 200);
                break;
            default:
                // code...
                break;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $trainings = ModulesTrainings::find($id);
        if (count($trainings)) {
            $modules = Modules::find($trainings->module_id);
            $allModules = Modules::all();
            $allTrainings = ModulesTrainings::all();
            $modules_trainings_questions = ModulesTrainingsQuestions::where('training_id', $id)->get();
            return view('auth.modules_trainings_questions2.edit', compact('modules','trainings', 'modules_trainings_questions', 'allModules', 'allTrainings'));
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
    public function update(ModulesTrainingsQuestionsRequest $request, $id)
    {
        $Question_id = $request->input('edit');
        $data = $request->input();

        $modules_trainings_question = ModulesTrainingsQuestions::find($Question_id);
        if(!count($modules_trainings_question)) {
            $modules_trainings_question = new ModulesTrainingsQuestions();
        }

        $modules_trainings_question->training_id       = $id;
        $modules_trainings_question->module_id       = $data['module_id'];
        $modules_trainings_question->difficulty_type = $data['difficulty'];
        $modules_trainings_question->type            = $data['type'];
        $modules_trainings_question->name_ar         = $data['question_ar'];
        $modules_trainings_question->name_en         = $data['question_en'];

        $modules_trainings_question->save();

        switch ($data['type']) {
            case 'true_false':
                $modules_trainings_questions_details = ModulesTrainingsQuestionsDetails::where('question_id', $Question_id)->first();
                if(!count($modules_trainings_questions_details)){
                    $modules_trainings_questions_details = new ModulesTrainingsQuestionsDetails();
                }
                $modules_trainings_questions_details->module_id =  $data['module_id'];
                $modules_trainings_questions_details->training_id =  $id;
                $modules_trainings_questions_details->question_id = $modules_trainings_question->id;
                $modules_trainings_questions_details->answer = $data['answers'];
                $modules_trainings_questions_details->save();

                break;
            case 'chose_single':
                $orders = [];
                foreach ($data['answers_text'] as $key => $answer_text) {
                    if($Question_id == null) {
                        $modules_trainings_questions_details = new ModulesTrainingsQuestionsDetails();
                    } else {
                        $modules_trainings_questions_details = ModulesTrainingsQuestionsDetails::find($key);
                        $orders[] = $key;
                    }
                    $modules_trainings_questions_details->module_id =  $data['module_id'];
                    $modules_trainings_questions_details->training_id =  $id;
                    $modules_trainings_questions_details->question_id = $modules_trainings_question->id;
                    $modules_trainings_questions_details->name_ar = $answer_text;
                    $modules_trainings_questions_details->name_en = $data['answers_text_en'][$key];
                    $modules_trainings_questions_details->answer = $data['chose_single'] == $key ? 1 : 0;

                    $modules_trainings_questions_details->save();
                }
                $curriculum_question_details = ModulesTrainingsQuestionsDetails::where('question_id', $Question_id)->whereNotIn('id', $orders)->delete();

                break;
            case 'chose_multiple':
                $orders = [];
                foreach ($data['answers_text'] as $key => $answer_text) {
                    if($Question_id == null) {
                        $modules_trainings_questions_details = new ModulesTrainingsQuestionsDetails();
                    } else {
                        $modules_trainings_questions_details = ModulesTrainingsQuestionsDetails::find($key);
                        $orders[] = $key;
                    }
                    $modules_trainings_questions_details->module_id =  $data['module_id'];
                    $modules_trainings_questions_details->training_id =  $id;
                    $modules_trainings_questions_details->question_id = $modules_trainings_question->id;
                    $modules_trainings_questions_details->name_ar = $answer_text;
                    $modules_trainings_questions_details->name_en = $data['answers_text_en'][$key];
                    $modules_trainings_questions_details->answer = isset($data['chose_multiple'][$key]) && $data['chose_multiple'][$key] == $key ? 1 : 0;

                    $modules_trainings_questions_details->save();
                }

                $curriculum_question_details = ModulesTrainingsQuestionsDetails::where('question_id', $Question_id)->whereNotIn('id', $orders)->delete();
                break;
            default:
                break;
        }

        $modules_trainings_questions = ModulesTrainingsQuestions::where('training_id', $id)->get();
        $returnHTML = view('auth.modules_trainings_questions2.questions.question_body')->with(['modules_trainings_questions' => $modules_trainings_questions])->render();
        return response()->json(['html'=>$returnHTML], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $modules_trainings_question = ModulesTrainingsQuestions::where('training_id', $id)->where('id', $request->id)->delete();
        $details  = ModulesTrainingsQuestionsDetails::where('question_id', $request->id)->get();
        foreach ($details as $key => $value) {
            $value->delete();
        }

        if($modules_trainings_question) {
            Session::flash('success', Lang::get('main.update') . Lang::get('main.courses_questions'));
        } else {
            Session::flash('error', Lang::get('main.update') . Lang::get('main.delete_question_error'));

        }
    }

//    public function getModuleTraining($id)
//    {
//        $module_training = ModulesTrainings::where('module_id', '=', $id)->where('active', '=', 1)->get();
//        return response()->json($module_training);
//    }


    public function getTrainingQuestions($id)
    {
        $training_questions = ModulesTrainingsQuestions::where('training_id', $id)->with('ModulesTrainingsQuestionsDetails')->get();
        return response()->json($training_questions);
    }

    public function importTrainingQuestions(Request $request)
    {
        dd($request->input());
    }

    public function searchTrainingQuestions(Request $request)
    {
        $search = $request->input('contains');
        $training_id = $request->input('training_id');
        if ($search != null) {
            $training_questions = ModulesTrainingsQuestions::where('training_id', $training_id)->where('name_ar', 'like', "%$search%")->orWhere('name_en', 'like', "%$search%")->with('ModulesTrainingsQuestionsDetails')->get();
            if ($training_questions->isEmpty()) {
                return response()->json('No search result');
            } else {
                return response()->json($training_questions);
            }
        } else {
            $training_questions = ModulesTrainingsQuestions::where('training_id', $training_id)->with('ModulesTrainingsQuestionsDetails')->get();
            return response()->json($training_questions);
        }
    }

    public function fetchQuestions(Request $request) {
        $curriculum_questions = ModulesTrainingsQuestions::whereIn('id', $request->ids)->get();
        foreach ($curriculum_questions as $key => $question) {
            $modules_trainings_question = new ModulesTrainingsQuestions();
            $modules_trainings_question->module_id = $request->module;
            $modules_trainings_question->training_id = $request->training;
            $modules_trainings_question->type = $question->type;
            $modules_trainings_question->name_ar = $question->name_ar;
            $modules_trainings_question->name_en = $question->name_en;
            $modules_trainings_question->difficulty_type = $question->difficulty_type;
            $modules_trainings_question->save();

            if($question->type == 'true_false') {
                $modules_trainings_questions_details = new ModulesTrainingsQuestionsDetails();
                $modules_trainings_questions_details->module_id = $request->module;
                $modules_trainings_questions_details->training_id = $request->training;
                $modules_trainings_questions_details->question_id = $modules_trainings_question->id;
                $modules_trainings_questions_details->answer = $question->ModulesTrainingsQuestionsDetails->answer;
                $modules_trainings_questions_details->save();
            } else {
                foreach ($question->ModulesTrainingsQuestionsDetails as $subkey => $answers) {
                    $modules_trainings_questions_details = new ModulesTrainingsQuestionsDetails();
                    $modules_trainings_questions_details->module_id = $request->module;
                    $modules_trainings_questions_details->training_id = $request->training;
                    $modules_trainings_questions_details->question_id = $modules_trainings_question->id;
                    $modules_trainings_questions_details->name_ar = $answers->name_ar;
                    $modules_trainings_questions_details->name_en = $answers->name_en;
                    $modules_trainings_questions_details->answer = $answers->answer;
                    $modules_trainings_questions_details->save();
                }
            }
        }
        $modules_trainings_questions = ModulesTrainingsQuestions::where('training_id', $request->training)->get();

        $returnHTML = view('auth.modules_trainings_questions2.questions.question_body')->with(['modules_trainings_questions' => $modules_trainings_questions])->render();
        return response()->json(['html' => $returnHTML], 200);
    }


}
