<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

class ModulesTrainingsQuestionsController extends Controller

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
        return view('auth.modules_trainings_questions.view',compact('modules'));

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
                                            <a href="' . URL('admin/modules_trainings_questions/' . $question->id . '/edit') . '">
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
            return view('auth.modules_trainings_questions.add', compact('modules'));
        } else {
            Session::flash('error', Lang::get('main.error_no_modules_to_add_question_to_it_you_can_edit_module_only'));
            return Redirect::to('admin/modules_trainings_questions');
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
                    foreach ($data['questions']['name_ar'] as $key => $name) {
                        if (empty($name)) {
                            $validator->errors()->add('error_exams_question_name_ar_' . ($x + 1), Lang::get('main.error_exams_question_name_ar') . ($x + 1));
                        }
                        if (isset($data['questions']['type'][$key])) {
                            if ($data['questions']['type'][$key] == 'true_false' && !isset($data['questions']['answers'][$key])) {
                                $validator->errors()->add('error_exams_enter_answers_of_question' . ($x + 1), Lang::get('main.error_exams_enter_answers_of_question') . ($x + 1));
                            }
                            if ($data['questions']['type'][$key] != 'true_false') {
                                $countAnswersAR = 0;
                                $countAnswersEN = 0;
                                if (isset($data['questions_ar']['answers'][$key])) {
                                    foreach ($data['questions_ar']['answers'][$key] as $answer) {
                                        if (!empty($answer)) {
                                            $countAnswersAR++;
                                        }
                                    }
                                } else {

                                    $validator->errors()->add('error_exams_enter_answers_of_question_' . ($x + 1), Lang::get('main.error_exams_enter_answers_of_question') . ($x + 1));
                                }
                                if (isset($data['questions_en']['answers'][$key])) {
                                    foreach ($data['questions_en']['answers'][$key] as $answer) {
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
                                if ($data['questions']['type'][$key] == 'chose_single' && !isset($data['chose_question_answer'][$key])) {
                                    $validator->errors()->add('error_exams_select_answer_of_question_' . ($x + 1), Lang::get('main.error_exams_select_answer_of_question') . ($x + 1));
                                }
                                if ($data['questions']['type'][$key] == 'chose_multiple') {
                                    if (!isset($data['chose_question_answer'][$key])) {
                                        $validator->errors()->add('error_exams_select_answer_of_question_' . ($x + 1), Lang::get('main.error_exams_select_answer_of_question') . ($x + 1));
                                    }
                                    $t = 0;
                                    foreach ($data['chose_question_answer'][$key] as $a) {
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
                    $validator->errors()->add('error_exams_enter_question_name_ar', Lang::get('main.error_exams_question_name_ar'));
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
            foreach ($data['questions']['name_ar'] as $key => $name) {
                $type = $data['questions']['type'][$key];
                $answers_ar = isset($data['questions_ar']['answers'][$key]) ? $data['questions_ar']['answers'][$key] : null;
                $answers_en = isset($data['questions_en']['answers'][$key]) ? $data['questions_en']['answers'][$key] : null;
                $answers = (isset($data['questions']['answers'][$key])) ? $data['questions']['answers'][$key] : null;
                $modules_trainings_questions = new ModulesTrainingsQuestions();
                $modules_trainings_questions->module_id = $data['module_id'];
                $modules_trainings_questions->type = $type;
                $modules_trainings_questions->name_ar = $data['questions']['name_ar'][$key];
                $modules_trainings_questions->name_en = $data['questions']['name_en'][$key];
                $modules_trainings_questions->difficulty_type = $data['questions']['difficulty_type'][$key];
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
                            $answer = (isset($data['chose_question_answer'][$key]) && $data['chose_question_answer'][$key] == ($y + 1)) ? 1 : 0;
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
                            $answer = (isset($data['chose_question_answer'][$key][$y])) ? 1 : 0;
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
            return Redirect::to('admin/modules_trainings_questions');
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
        $modules_trainings = ModulesTrainings::find($id);
        if (count($modules_trainings)) {
            $allModules = Modules::where(function ($q) use ($modules_trainings) {
                $q->where('id', $modules_trainings->module_id)->orWhereNotIn('id', ModulesTrainingsQuestions::groupBy('module_id')->pluck('module_id')->toArray());
            })->get();

            $modules = Modules::find($modules_trainings->module_id);
            $modules_trainings_questions = ModulesTrainingsQuestions::where('module_id', $modules_trainings->module_id)->where('training_id', $id)->get();
            return view('auth.modules_trainings_questions.edit', compact('modules', 'modules_trainings_questions', 'allModules', 'modules_trainings'));
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
                'training_id' => 'required|not_in:0',
                'questions' => 'required',
            ));
        $validator->after(function ($validator) use ($data) {
            if (isset($data['questions'])) {
                $x = 0;
                if (isset($data['questions']['name_ar'])) {
                    foreach ($data['questions']['name_ar'] as $key => $name) {
                        if (empty($name)) {
                            $validator->errors()->add('error_exams_question_name_ar_' . ($x + 1), Lang::get('main.error_exams_question_name_ar') . ($x + 1));
                        }
                        if (isset($data['questions']['type'][$key])) {
                            if ($data['questions']['type'][$key] == 'true_false' && !isset($data['questions']['answers'][$key])) {
                                $validator->errors()->add('error_exams_enter_answers_of_question' . ($x + 1), Lang::get('main.error_exams_enter_answers_of_question') . ($x + 1));
                            }
                            if ($data['questions']['type'][$key] != 'true_false') {
                                $countAnswersAR = 0;
                                $countAnswersEN = 0;
                                if (isset($data['questions_ar']['answers'][$key])) {
                                    foreach ($data['questions_ar']['answers'][$key] as $answer) {
                                        if (!empty($answer)) {
                                            $countAnswersAR++;
                                        }
                                    }
                                } else {

                                    $validator->errors()->add('error_exams_enter_answers_of_question_' . ($x + 1), Lang::get('main.error_exams_enter_answers_of_question') . ($x + 1));
                                }
                                if (isset($data['questions_en']['answers'][$key])) {
                                    foreach ($data['questions_en']['answers'][$key] as $answer) {
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
                                if ($data['questions']['type'][$key] == 'chose_single' && !isset($data['chose_question_answer'][$key])) {
                                    $validator->errors()->add('error_exams_select_answer_of_question_' . ($x + 1), Lang::get('main.error_exams_select_answer_of_question') . ($x + 1));
                                }
                                if ($data['questions']['type'][$key] == 'chose_multiple') {
                                    if (!isset($data['chose_question_answer'][$key])) {
                                        $validator->errors()->add('error_exams_select_answer_of_question_' . ($x + 1), Lang::get('main.error_exams_select_answer_of_question') . ($x + 1));
                                    }
                                    $t = 0;
                                    foreach ($data['chose_question_answer'][$key] as $a) {
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
            $ModulesTrainingsQuestionsIDS = ModulesTrainingsQuestions::where('module_id', $data['module_id'])->where('training_id', $data['training_id'])->pluck('id')->toArray();
            if (count($ModulesTrainingsQuestionsIDS)) {
//                ModulesTrainingsQuestions::destroy($ModulesTrainingsQuestionsIDS);
//                ModulesTrainingsQuestionsDetails::where('question_id',$ModulesTrainingsQuestionsIDS)->delete();
                ModulesTrainingsQuestions::whereIn('id', $ModulesTrainingsQuestionsIDS)->delete();
                ModulesTrainingsQuestionsDetails::whereIn('question_id', $ModulesTrainingsQuestionsIDS)->delete();
            }
            $x = 0;
            foreach ($data['questions']['name_ar'] as $key => $name) {
                $type = $data['questions']['type'][$key];
                $answers_ar = isset($data['questions_ar']['answers'][$key]) ? $data['questions_ar']['answers'][$key] : null;
                $answers_en = isset($data['questions_en']['answers'][$key]) ? $data['questions_en']['answers'][$key] : null;
                $answers = (isset($data['questions']['answers'][$key])) ? $data['questions']['answers'][$key] : null;
                $modules_trainings_questions = new ModulesTrainingsQuestions();
                $modules_trainings_questions->module_id = $data['module_id'];
                $modules_trainings_questions->training_id = $data['training_id'];
                $modules_trainings_questions->type = $type;
                $modules_trainings_questions->name_ar = $data['questions']['name_ar'][$key];
                $modules_trainings_questions->name_en = $data['questions']['name_en'][$key];
                $modules_trainings_questions->difficulty_type = $data['questions']['difficulty_type'][$key];
                $modules_trainings_questions->save();

                switch ($type) {
                    case'true_false':
                        $modules_trainings_questions_details = new ModulesTrainingsQuestionsDetails();
                        $modules_trainings_questions_details->module_id = $data['module_id'];
                        $modules_trainings_questions_details->training_id = $data['training_id'];
                        $modules_trainings_questions_details->question_id = $modules_trainings_questions->id;
                        $modules_trainings_questions_details->answer = $answers;
                        $modules_trainings_questions_details->save();
                        break;
                    case'chose_single':
                        for ($y = 0; $y < 4; $y++) {
                            $answer = (isset($data['chose_question_answer'][$key]) && $data['chose_question_answer'][$key] == ($y + 1)) ? 1 : 0;
                            if (!empty($answers_ar[$y]) && !empty($answers_en[$y])) {
                                $modules_trainings_questions_details = new ModulesTrainingsQuestionsDetails();
                                $modules_trainings_questions_details->module_id = $data['module_id'];
                                $modules_trainings_questions_details->training_id = $data['training_id'];
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
                            $answer = (isset($data['chose_question_answer'][$key][$y])) ? 1 : 0;
                            if (!empty($answers_ar[$y]) && !empty($answers_en[$y])) {
                                $modules_trainings_questions_details = new ModulesTrainingsQuestionsDetails();
                                $modules_trainings_questions_details->module_id = $data['module_id'];
                                $modules_trainings_questions_details->training_id = $data['training_id'];
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
            return Redirect::to('admin/modules_trainings_questions/' . $id . '/edit');
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
        $modules_trainings_questions = ModulesTrainingsQuestions::where('module_id', $id)->delete();
        if ($modules_trainings_questions) {
            return response()->json(['success' => true, 'message' => 'success'])->setCallback($request->input('callback'));
        }
    }

    public function getModuleTraining($id)
    {
        $module_training = ModulesTrainings::where('module_id', '=', $id)->where('active', '=', 1)->get();
        return response()->json($module_training);
    }

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
                return response()->json(Lang::get('main.no_search_result'));
            } else {
                return response()->json($training_questions);
            }
        } else {
            $training_questions = ModulesTrainingsQuestions::where('training_id', $training_id)->with('ModulesTrainingsQuestionsDetails')->get();
            return response()->json($training_questions);
        }
    }


}
