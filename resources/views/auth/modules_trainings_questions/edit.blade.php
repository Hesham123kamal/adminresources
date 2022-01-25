@extends('auth.layouts.app')
@section('pageTitle')
    <title>{{ Lang::get('main.home_page_title') }}</title>
    <style>

        #report-details {
            font-size: 23px;
        }

        .color1 {
            color: #262d49;
        }

        .color2 {
            color: #ea4923;
        }

        .bold {
            font-weight: bold;
            font-size: 25px;
        }

        .small {
            font-size: 15px;
        }

        #about-course input[type='checkbox'] {
            top: 3px;
            right: 0px;
            left: auto;
        }

        #about-course .checkbox p {
            padding-right: 20px;
        }

        input[type="checkbox"] {
            display: none;
        }

        .chose input[type="checkbox"] + label span {
            display: inline-block;
            width: 35px;
            height: 35px;
            margin: -2px 10px 0 0;
            vertical-align: middle;
            background: url('{{ asset('img/checkbox/checkbox-unchecked.png') }}') center center no-repeat;
            cursor: pointer;
        }

        .chose input[type="checkbox"]:checked + label span {
            background: url('{{ asset('img/checkbox/checkbox-checked.png') }}') center center no-repeat;
        }

        .chose input[type="checkbox"] + label span:hover, .chose input[type="checkbox"] + label:hover span {
            background: url('{{ asset('img/checkbox/checkbox-checked.png') }}') center center no-repeat;
            opacity: 0.5;
        }

        .chose input[type="radio"] + label span {
            display: inline-block;
            width: 35px;
            height: 35px;
            margin: -2px 10px 0 0;
            vertical-align: middle;
            background: url('{{ asset('img/checkbox/checkbox-unchecked.png') }}') center center no-repeat;
            cursor: pointer;
        }

        .chose input[type="radio"]:checked + label span {
            background: url('{{ asset('img/checkbox/checkbox-checked.png') }}') center center no-repeat;
        }

        .chose input[type="radio"] + label span:hover, .chose input[type="checkbox"] + label:hover span {
            background: url('{{ asset('img/checkbox/checkbox-checked.png') }}') center center no-repeat;
            opacity: 0.5;
        }

        input[type="radio"] {
            display: none;
        }

        .false input[type="radio"]:checked + label span {
            background: url('{{ asset('img/checkbox/radio-wrong-checked.png') }}') center center no-repeat #272E4A;
        }

        .false input[type="radio"] + label span {
            display: inline-block;
            width: 35px;
            height: 35px;
            margin: -2px 10px 0 0;
            -webkit-border-radius: 25px !important;
            border-radius: 25px !important;
            vertical-align: middle;
            background: url('{{ asset('img/checkbox/radio-wrong-unchecked.png') }}') center center no-repeat #ededed;
            cursor: pointer;
        }

        .false input[type="radio"] + label span:hover {
            background: url('{{ asset('img/checkbox/radio-wrong-checked.png') }}') center center no-repeat #272E4A;
            opacity: 0.5;
        }

        .true input[type="radio"]:checked + label span {
            background: url('{{ asset('img/checkbox/radio-correct-checked.png') }}') center center no-repeat #272E4A;
        }

        .true input[type="radio"] + label span {
            display: inline-block;
            width: 35px;
            height: 35px;
            margin: -2px 10px 0 0;
            -webkit-border-radius: 25px !important;
            border-radius: 25px !important;
            vertical-align: middle;
            background: url('{{ asset('img/checkbox/radio-correct-unchecked.png') }}') center center no-repeat #ededed;
            cursor: pointer;
        }

        .true input[type="radio"] + label span:hover {
            background: url('{{ asset('img/checkbox/radio-correct-checked.png') }}') center center no-repeat #272E4A;
            opacity: 0.5;
        }

        .true, .false {
            display: inline-block;
        }

        .input-group-addon.chose, .input-group-addon.chose label {
            padding: 0px;
            margin: 0px;
        }

        #coursesQuestions {
            max-height: 200px;
            overflow-x: hidden;
            overflow-y: scroll;
        }

        #customDiv {
            border-right: 2px solid #eef1f5;
            min-height: 400px;
        }

        .questions-area::-webkit-scrollbar {
            width: 10px;
        }

        /* Track */
        .questions-area::-webkit-scrollbar-track {
            background: #fff;
        }

        /* Handle */
        .questions-area::-webkit-scrollbar-thumb {
            background: #ccc;
        }

        /* Handle on hover */
        .questions-area::-webkit-scrollbar-thumb:hover {
            background: #999;
        }

        .questions-area .md-checkbox {
            margin-bottom: 10px;
        }

        .questions-area .overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            background: rgba(0, 0, 0, .3);
            width: 100px;
            height: 67px;
            border-radius: 10px !important;
            transform: translate(-50%, -50%);
            z-index: 9999999999;
            box-shadow: 0 0 11px 0px #999;
        }

        .overlay i {
            line-height: 1.5;
            margin: auto;
            left: 20%;
            top: 25px;
            position: absolute;
        }

        .checkAllQuestion span {
            margin-right: 10px;
            font-weight: bold;
        }

        .checkAllQuestion span i {
            font-weight: normal;
        }

    </style>
@endsection
@section('contentHeader')
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL('/admin') }}">{{ Lang::get('main.dashboard') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ URL('/admin/modules_trainings_questions') }}">{{ Lang::get('main.modules_trainings_questions') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
            <li>
                <span>{{ $modules->name }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.modules_trainings_questions') }}
        <small>{{ Lang::get('main.edit') }}</small>
        <button type="button" class="btn btn-primary pull-right" data-toggle="modal"
                data-target=".import-course-question-modal">{{ Lang::get('main.import_questions') }}
        </button>
    </h1>

    <div class="modal fade import-course-question-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" style="margin-top: 50px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ Lang::get('main.import_questions') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(['url'=>'admin/import_course_questions']) !!}
                {!! Form::close() !!}
                <div class="modal-body">
                    <div class="form-group form-group-select2">
                        <label for="module_id">{{ Lang::get('main.module_name') }}</label>
                        <select style="width: 100%" id="module_id" class="sel2" name="module_id">
                            <option value="" id="first_opt">{{ Lang::get('main.select') }} {{ Lang::get('main.module') }}</option>
                            @foreach($allModules as $module)
                                <option value="{{ $module->id }}">{{ $module->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group form-group-select2">
                        <label for="module_training">{{ Lang::get('main.module_training') }}</label>
                        <select style="width: 100%" id="module_training" class="sel2" name="module_training">
                        </select>
                    </div>
                    <div class="row checkAllQuestion"
                         style="display: none;box-shadow: 0 7px 7px -6px #999; margin-bottom: 5px; padding-bottom: 10px;">
                        <div class="col-sm-6">
                            <span style="margin-left: 19px;">{{ Lang::get('main.true_false') }}
                            <i style="color: #f0ad4e;" class="fa fa-check"></i></span>
                            <span>{{ Lang::get('main.chose_answer') }}
                            <i style="color: #337ab7;" class="fa fa-check-square-o"></i></span>
                            <span>{{ Lang::get('main.chose_answers') }}
                            <i style="color: #ed6b75;" class="fa fa-check-square-o"></i><i style="color: #ed6b75;"
                                                                                           class="fa fa-check-square-o"></i></span>
                        </div>
                        <div class="col-sm-3">
                            <div class="md-checkbox checkAllQuestion">
                                <input name="checkAllQuestion" type="checkbox" id="checkAllQuestion"
                                       class="md-check ">
                                <label for="checkAllQuestion">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span>{{ Lang::get('main.check_all') }}</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="text" class="form-control" id="searchQuestion" name="search_question"
                                       placeholder="Search">
                            </div>
                        </div>
                    </div>

                    <div class="questions-area"
                         style="display: none;height: 230px; overflow-x: hidden; overflow-y: scroll;padding: 5px 19px;position: relative;z-index: 55">
                        <div class="overlay">
                            <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                            <span class="sr-only">{{ Lang::get('main.loading') }}</span>
                        </div>
                        <h5 style="margin-bottom: 20px; font-weight: bold;text-transform: capitalize">select questions
                            as you want </h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeModal" data-dismiss="modal">{{ Lang::get('main.cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="importSelected">{{ Lang::get('main.import_selected') }}</button>
                </div>

            </div>
        </div>
    </div>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
@endsection
@section('content')
    <div class="row">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-modules_trainings_questions font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.modules_trainings_questions') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/modules_trainings_questions/'.$modules_trainings->id,'id'=>'addmodules_trainings_questionsEditForm']) !!}
                <div class="form-body">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_error') }}
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_success') }}
                    </div>
                    <div class="form-group form-group-select2 col-lg-6">
                        <label for="module_id">{{ Lang::get('main.module_name') }}</label>
                        <select style="width: 100%" id="module_id" readonly class="sel2" name="module_id">
                            @foreach($allModules as $module)
                                <option @if(old('module_id')==$module->id) selected="selected"
                                        @endif value="{{ $module->id }}">{{ $module->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group form-group-select2 col-lg-6">
                        <label for="training_id">{{ Lang::get('main.training') }}</label>
                        <select style="width: 100%" id="training_id" readonly class="sel2" name="training_id">
                            <option @if(old('training_id')==$modules_trainings->id) selected="selected"
                                    @endif value="{{ $modules_trainings->id }}">{{ $modules_trainings->name }}</option>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-lg-3">
                        <label for="type-1">{{ Lang::get('main.type') }}</label>
                        <div class="form-group form-group-select2">
                            <select style="width:100%" class="sel2" name="type" id="type-1">
                                <option selected="selected"
                                        value="true_false">{{ Lang::get('main.true_false') }}</option>
                                <option value="chose_single">{{ Lang::get('main.chose_answer') }}</option>
                                <option value="chose_multiple">{{ Lang::get('main.chose_answers') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1" style="margin-top: 22px;">
                        <button data-id="type-1" style="margin-top: 2px;" class="btn btn-primary addNewQuestion"><i
                                    class="glyphicon glyphicon-plus"></i></button>
                    </div>
                    <div class="clearfix"></div>
                    <div id="questionsContent">
                        @if(count($modules_trainings_questions))

                            <?php
                            $x = 0;
                            $trueFalseCount = 0;
                            $choseSingleCount = 0;
                            $choseMultipleCount = 0;
                            ?>
                            @foreach($modules_trainings_questions as $modules_question)
                                @if($modules_question->type=='true_false')
                                    <div class="row">
                                        <div class="col-lg-10">
                                            <div class="form-group col-lg-8">
                                                <label for="questions_name_{{ $x }}">{{ Lang::get('main.question') }}</label>
                                                <input type="text" class="form-control"
                                                       name="questions[name_ar][{{ $x }}]"
                                                       value="{{ $modules_question->name_ar }}"
                                                       id="questions_name_ar_{{ $x }}"
                                                       placeholder="{{ Lang::get('main.enter').Lang::get('main.question_ar') }}">
                                                <input type="text" class="form-control"
                                                       name="questions[name_en][{{ $x }}]"
                                                       value="{{ $modules_question->name_en }}"
                                                       id="questions_name_en_{{ $x }}"
                                                       placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                                <input type="hidden" name="questions[type][{{ $x }}]"
                                                       value="true_false">
                                            </div>
                                            <div class="col-lg-2">
                                                <label for="">{{ Lang::get('main.difficulty_type') }}</label>
                                                <select style="width:100%" class="sel2"
                                                        name="questions[difficulty_type][{{ $x }}]"
                                                        id="difficulty_type">
                                                    <option @if($modules_question->difficulty_type=='easy') selected="selected"
                                                            @endif value="easy">{{ Lang::get('main.easy') }}</option>
                                                    <option @if($modules_question->difficulty_type=='normal') selected="selected"
                                                            @endif value="normal">{{ Lang::get('main.normal') }}</option>
                                                    <option @if($modules_question->difficulty_type=='hard') selected="selected"
                                                            @endif value="hard">{{ Lang::get('main.hard') }}</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-2" style="margin-top: 24px;">
                                                <div class="true">
                                                    <input type="radio" name="questions[answers][{{ $x }}]"
                                                           @if($modules_question->ModulesTrainingsQuestionsDetails->answer == 1) checked="checked"
                                                           @endif value="1" id="questions_answers_{{ $x }}_true">
                                                    <label for="questions_answers_{{ $x }}_true">
                                                        <span></span>
                                                    </label>
                                                </div>

                                                <div class="false">
                                                    <input type="radio" name="questions[answers][{{ $x }}]"
                                                           @if($modules_question->ModulesTrainingsQuestionsDetails->answer == 0) checked="checked"
                                                           @endif value="0" id="questions_answers_{{ $x }}_false">
                                                    <label for="questions_answers_{{ $x }}_false">
                                                        <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-danger col-lg-2 remove_question" data-type="true_false"
                                                style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i>
                                        </button>
                                    </div>
                                    <?php $trueFalseCount++;?>
                                @elseif($modules_question->type=='chose_single')
                                    <div>
                                        <div class="form-group col-lg-8">
                                            <label for="questions_name_{{ $x }}">{{ Lang::get('main.question') }}
                                            </label>
                                            <input type="text" class="form-control" name="questions[name_ar][{{ $x }}]"
                                                   value="{{ $modules_question->name_ar }}"
                                                   id="questions_name_ar_{{ $x }}"
                                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.question_ar') }}">
                                            <input type="text" class="form-control" name="questions[name_en][{{ $x }}]"
                                                   value="{{ $modules_question->name_en }}"
                                                   id="questions_name_en_{{ $x }}"
                                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                            <input type="hidden" name="questions[type][{{ $x }}]" value="chose_single">
                                        </div>
                                        <div class="col-lg-2">
                                            <label for="">{{ Lang::get('main.difficulty_type') }}</label>
                                            <select style="width:100%" class="sel2"
                                                    name="questions[difficulty_type][{{ $x }}]" id="difficulty_type">
                                                <option @if($modules_question->difficulty_type=='easy') selected="selected"
                                                        @endif value="easy">{{ Lang::get('main.easy') }}</option>
                                                <option @if($modules_question->difficulty_type=='normal') selected="selected"
                                                        @endif value="normal">{{ Lang::get('main.normal') }}</option>
                                                <option @if($modules_question->difficulty_type=='hard') selected="selected"
                                                        @endif value="hard">{{ Lang::get('main.hard') }}</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_single"
                                                style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i>
                                        </button>
                                        <?php $yx = 1;?>
                                        @foreach($modules_question->ModulesTrainingsQuestionsDetails as $detail)
                                            <div class="form-group col-lg-6">
                                                <label for="chose_single_{{ $x }}_{{ $choseSingleCount }}_{{ $yx }}">{{ Lang::get('main.choice').' '.$yx }}
                                                </label>
                                                <div class="input-group">
                                                        <span class="input-group-addon chose">
                                                        <input type="radio"
                                                               id="chose_single_{{ $x }}_{{ $choseSingleCount }}_{{ $yx }}"
                                                               @if($detail->answer) checked="checked"
                                                               @endif value="{{ $yx }}"
                                                               name="chose_question_answer[{{ $x }}]">
                                                        <label for="chose_single_{{ $x }}_{{ $choseSingleCount }}_{{ $yx }}">

                                                        <span>
                                                        </span>
                                                        </label>
                                                        </span>
                                                    <input type="text" class="form-control"
                                                           name="questions_ar[answers][{{ $x }}][]"
                                                           value="{{ $detail->name_ar }}"
                                                           id="chose_single_{{ $x }}_{{ $choseSingleCount }}_{{ $yx }}"
                                                           placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} {{ $yx }}">
                                                    <input type="text" class="form-control"
                                                           name="questions_en[answers][{{ $x }}][]"
                                                           value="{{ $detail->name_en }}"
                                                           id="chose_single_{{ $x }}_{{ $choseSingleCount }}_{{ $yx }}"
                                                           placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} {{ $yx }}">

                                                    <span class="input-group-addon "> <a href="#"
                                                                                         class="removeAnswer"><i
                                                                    class="glyphicon glyphicon-trash"></i></a>
                                                        </span>
                                                </div>
                                            </div>
                                            <?php $yx++;?>
                                        @endforeach
                                    </div>
                                    <?php $choseSingleCount++;?>
                                @elseif($modules_question->type=='chose_multiple')
                                    <div>
                                        <div class="form-group col-lg-8">
                                            <label for="questions_name_{{ $x }}">{{ Lang::get('main.question') }}
                                            </label>
                                            <input type="text" class="form-control" name="questions[name_ar][{{ $x }}]"
                                                   value="{{ $modules_question->name_ar }}"
                                                   id="questions_name_ar_{{ $x }}"
                                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.question_ar') }}">
                                            <input type="text" class="form-control" name="questions[name_en][{{ $x }}]"
                                                   value="{{ $modules_question->name_en }}"
                                                   id="questions_name_en_{{ $x }}"
                                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                            <input type="hidden" name="questions[type][{{ $x }}]"
                                                   value="chose_multiple">
                                        </div>
                                        <div class="col-lg-2">
                                            <label for="">{{ Lang::get('main.difficulty_type') }}</label>
                                            <select style="width:100%" class="sel2"
                                                    name="questions[difficulty_type][{{ $x }}]" id="difficulty_type">
                                                <option @if($modules_question->difficulty_type=='easy') selected="selected"
                                                        @endif value="easy">{{ Lang::get('main.easy') }}</option>
                                                <option @if($modules_question->difficulty_type=='normal') selected="selected"
                                                        @endif value="normal">{{ Lang::get('main.normal') }}</option>
                                                <option @if($modules_question->difficulty_type=='hard') selected="selected"
                                                        @endif value="hard">{{ Lang::get('main.hard') }}</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-danger col-lg-2 remove_question"
                                                data-type="chose_multiple" style="margin-top: 24px;"><i
                                                    class="glyphicon glyphicon-trash"></i></button>
                                        <?php $yx = 1;?>
                                        @foreach($modules_question->ModulesTrainingsQuestionsDetails as $detail)
                                            <div class="form-group col-lg-6">
                                                <label for="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_{{ $yx }}">{{ Lang::get('main.choice') }} {{ $yx }}
                                                </label>
                                                <div class="input-group">
                                                    <span class="input-group-addon chose">
                                                    <input type="checkbox"
                                                           id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_{{ $yx }}"
                                                           @if($detail->answer)  checked="checked" @endif value="1"
                                                           name="chose_question_answer[{{ $x }}][{{ $yx-1 }}]">
                                                    <label for="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_{{ $yx }}">

                                                    <span>
                                                    </span>
                                                    </label>
                                                    </span>
                                                    <input type="text" class="form-control"
                                                           name="questions_ar[answers][{{ $x }}][]"
                                                           value="{{ $detail->name_ar }}"
                                                           id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_{{ $yx-1 }}"
                                                           placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} {{ $yx-1 }}">
                                                    <input type="text" class="form-control"
                                                           name="questions_en[answers][{{ $x }}][]"
                                                           value="{{ $detail->name_en }}"
                                                           id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_{{ $yx-1 }}"
                                                           placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} {{ $yx-1 }}">
                                                </div>
                                            </div>
                                            <?php $yx++;?>
                                        @endforeach
                                    </div>
                                    <?php $choseMultipleCount++;?>
                                @endif
                                <?php $x++?>
                            @endforeach
                        @endif

                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-lg-3">
                        <label for="type-2">{{ Lang::get('main.type') }}</label>
                        <div class="form-group form-group-select2">
                            <select style="width:100%" class="sel2" name="type" id="type-2">
                                <option selected="selected"
                                        value="true_false">{{ Lang::get('main.true_false') }}</option>
                                <option value="chose_single">{{ Lang::get('main.chose_answer') }}</option>
                                <option value="chose_multiple">{{ Lang::get('main.chose_answers') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1" style="margin-top: 22px;">
                        <button data-id="type-2" style="margin-top: 2px;" class="btn btn-primary addNewQuestion"><i
                                    class="glyphicon glyphicon-plus"></i></button>
                    </div>
                    <div class="clearfix"></div>
                    <div class="pull-right">
                        <button class="btn btn-primary" name="btnAction" id="btnAction"
                                type="submit">{{ Lang::get('main.add') }}</button>
                    </div>
                </div>
                <div class="clearfix"></div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@section('scriptCode')
    <script>
        var questions ={{ isset($x)?$x:0 }};
        $(document).ready(function () {
            $(".sel2").select2()
            var token = '{{ csrf_token() }}';
            var wrapper_times = $("#questionsContent"); //title Fields wrapper
            var add_button_times = $(".addNewQuestion"); //title Add button ID
            var trueFalseAnswersCount ={{ (isset($trueFalseCount))?$trueFalseCount+1:0 }};
            var choiceMultipleAnswersCount ={{ (isset($choseMultipleCount))?$choseMultipleCount+1:0 }};
            var choiceSingleAnswersCount ={{ (isset($choseSingleCount))?$choseSingleCount+1:0 }};
            $(document).on('click', '.removeAnswer', function (e) {
                e.preventDefault();
                $(this).parent().parent().parent().remove();
            });
            $(document).on('click', '.addNewQuestion', function (e) { //on add input button click
                e.preventDefault();
                type = $("#" + $(this).data('id')).val();
                console.log(type);
                questions = (questions > 0) ? questions : 0;
                $('.sel2').each(function () {
                    $(this).select2('destroy');
                });
                if (type == 'true_false') {
                    $(wrapper_times).append('<div class="row"> <div class="col-lg-10"> <div class="form-group col-lg-8"> <label for="questions_name_ar_' + questions + '">{{ Lang::get('main.question') }}</label> <input type="text" class="form-control" name="questions[name_ar][' + questions + ']" id="questions_name_ar_' + questions + '" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_ar') }}"> <input type="text" class="form-control" name="questions[name_en][' + questions + ']" id="questions_name_en_' + questions + '" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}"> <input type="hidden" name="questions[type][' + questions + ']" value="true_false"> </div><div class="col-lg-2"><label for="">{{ Lang::get('main.difficulty_type') }}</label><select style="width:100%" class="sel2" name="questions[difficulty_type][' + questions + ']" id="difficulty_type"><option  value="easy">{{ Lang::get('main.easy') }}</option><option  value="normal">{{ Lang::get('main.normal') }}</option><option  value="hard">{{ Lang::get('main.hard') }}</option></select></div> <div class="col-lg-2" style="margin-top: 24px;"> <div class="true"> <input type="radio" name="questions[answers][' + questions + ']" checked="checked" value="1" id="questions_answers_' + questions + '_true"> <label for="questions_answers_' + questions + '_true"> <span></span> </label> </div> <div class="false"> <input type="radio" name="questions[answers][' + questions + ']" value="0" id="questions_answers_' + questions + '_false"> <label for="questions_answers_' + questions + '_false"> <span></span> </label> </div> </div> </div> <button class="btn btn-danger col-lg-2 remove_question" data-type="true_false" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> </div>');//add input box
                    trueFalseAnswersCount++;
                } else if (type == 'chose_multiple') {
                    $(wrapper_times).append('<div class=""> <div class="form-group col-lg-8"> <label for="questions_name_' + questions + '">{{ Lang::get('main.question') }}</label> <input type="text" class="form-control" name="questions[name_ar][' + questions + ']" id="questions_name_ar_' + questions + '" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_ar') }}"> <input type="text" class="form-control" name="questions[name_en][' + questions + ']" id="questions_name_en_' + questions + '" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}"> <input type="hidden" name="questions[type][' + questions + ']" value="chose_multiple"> </div><div class="col-lg-2"><label for="">{{ Lang::get('main.difficulty_type') }}</label><select style="width:100%" class="sel2" name="questions[difficulty_type][' + questions + ']" id="difficulty_type"><option  value="easy">{{ Lang::get('main.easy') }}</option><option  value="normal">{{ Lang::get('main.normal') }}</option><option  value="hard">{{ Lang::get('main.hard') }}</option></select></div> <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_multiple" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> <div class="form-group col-lg-6"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_1">{{ Lang::get('main.choice') }} 1</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_1" value="1" name="chose_question_answer[' + questions + '][0]"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_1"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers][' + questions + '][]" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_1" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 1"> <input type="text" class="form-control" name="questions_en[answers][' + questions + '][]" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_1" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 1"> </div> </div> <div class="form-group col-lg-6"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_2">{{ Lang::get('main.choice') }} 2</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_2" value="1" name="chose_question_answer[' + questions + '][1]"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_2"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers][' + questions + '][]" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_2" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 2"> <input type="text" class="form-control" name="questions_en[answers][' + questions + '][]" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_2" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 2"> </div> </div> <div class="form-group col-lg-6"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_3">{{ Lang::get('main.choice') }} 3</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_3" value="1" name="chose_question_answer[' + questions + '][2]"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_3"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers][' + questions + '][]" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_3" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 3"> <input type="text" class="form-control" name="questions_en[answers][' + questions + '][]" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_3" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 3"> </div> </div> <div class="form-group col-lg-6"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_4">{{ Lang::get('main.choice') }} 4</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_4" value="1" name="chose_question_answer[' + questions + '][3]"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_4"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers][' + questions + '][]" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_4" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 4"> <input type="text" class="form-control" name="questions_en[answers][' + questions + '][]" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_4" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 4"> </div> </div> </div>');//add input box
                    choiceMultipleAnswersCount++
                } else if (type == 'chose_single') {
                    $(wrapper_times).append('<div class=""> <div class="form-group col-lg-8"> <label for="questions_name_' + questions + '">{{ Lang::get('main.question') }}</label> <input type="text" class="form-control" name="questions[name_ar][' + questions + ']" id="questions_name_ar_' + questions + '" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_ar') }}"><input type="text" class="form-control" name="questions[name_en][' + questions + ']" id="questions_name_en_' + questions + '" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}"> <input type="hidden" name="questions[type][' + questions + ']" value="chose_single"> </div><div class="col-lg-2"><label for="">{{ Lang::get('main.difficulty_type') }}</label><select style="width:100%" class="sel2" name="questions[difficulty_type][' + questions + ']" id="difficulty_type"><option  value="easy">{{ Lang::get('main.easy') }}</option><option  value="normal">{{ Lang::get('main.normal') }}</option><option  value="hard">{{ Lang::get('main.hard') }}</option></select></div> <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_single" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> <div class="form-group col-lg-6"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_1">{{ Lang::get('main.choice') }} 1</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_1" value="1" name="chose_question_answer[' + questions + ']"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_1"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers][' + questions + '][]" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_1" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 1"> <input type="text" class="form-control" name="questions_en[answers][' + questions + '][]" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_1" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 1"> <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_2">{{ Lang::get('main.choice') }} 2</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_2" value="2" name="chose_question_answer[' + questions + ']"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_2"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers][' + questions + '][]" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_2" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 2"> <input type="text" class="form-control" name="questions_en[answers][' + questions + '][]" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_2" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 2"> <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_3">{{ Lang::get('main.choice') }} 3</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_3" value="3" name="chose_question_answer[' + questions + ']"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_3"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers][' + questions + '][]" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_3" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 3"> <input type="text" class="form-control" name="questions_en[answers][' + questions + '][]" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_3" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 3"> <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_4">{{ Lang::get('main.choice') }} 4</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_4" value="4" name="chose_question_answer[' + questions + ']"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_4"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers][' + questions + '][]" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_4" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 4"> <input type="text" class="form-control" name="questions_en[answers][' + questions + '][]" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_4" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 4"> <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> </div>');//add input box
                    choiceSingleAnswersCount++
                }
                questions++;
                $('.sel2').select2()
            });
            $(wrapper_times).on("click", ".remove_question", function (e) { //user click on remove text
                e.preventDefault();
                type = $(this).data("type");
                if (type == 'true_false') {
                    $(this).parent('div').remove();
                    trueFalseAnswersCount--;
                } else if (type == 'chose_multiple') {
                    $(this).parent('div').remove();
                    choiceMultipleAnswersCount--;
                } else if (type == 'chose_single') {
                    $(this).parent('div').remove();
                    choiceSingleAnswersCount--;
                }
                //questions--;
            });
            $(document).on('click', '#addFromCoursesExam', function () {
                //$("body").toggleClass('page-sidebar-closed');

                t = $(".page-sidebar"),
                    i = $(".page-sidebar-menu"),
                    e = $("body");
                $(".sidebar-search", t).removeClass("open")
                    , e.hasClass("page-sidebar-closed") ? (e.removeClass("page-sidebar-closed")
                    , i.removeClass("page-sidebar-menu-closed")
                    , $.cookie && $.cookie("sidebar_closed", "0")) : (e.addClass("page-sidebar-closed")
                    , i.addClass("page-sidebar-menu-closed"), e.hasClass("page-sidebar-fixed") && i.trigger("mouseleave")
                    , $.cookie && $.cookie("sidebar_closed", "1"))
                    , $(window).trigger("resize");
                if (e.hasClass("page-sidebar-closed")) {
                    $("#formDiv").removeClass('col-lg-12');
                    $("#formDiv").addClass('col-lg-8');
                    $("#customDiv").removeClass('hidden');
                    $("#addFromCoursesExam").text('{{ Lang::get('main.close_course_exam') }}').toggleClass('btn-danger btn-success');
                } else {
                    $("#formDiv").removeClass('col-lg-8');
                    $("#formDiv").addClass('col-lg-12');
                    $("#customDiv").addClass('hidden');
                    $("#addFromCoursesExam").text('{{ Lang::get('main.add_from_courses_exam') }}').toggleClass('btn-danger btn-success');
                }
            });
            $(document).on('change', '#selectAll', function () {
                if ($(this).is(':checked')) {
                    $(".questionsCheckBox").prop('checked', true);
                } else {
                    $(".questionsCheckBox").prop('checked', false);
                }
            })
            $(document).on('change', '#courses_exam_id', function () {
                courses_exam_id = $(this).val();
                if (courses_exam_id) {
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/exams/getCoursesExamQuestions') }}",
                        data: {"courses_exam_id": courses_exam_id, _token: token},
                        success: function (msg) {
                            html = '';
                            msg.result.forEach(function (item) {
                                html += '<li> <div class="task-checkbox"> <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"> <input type="checkbox" class="checkboxes questionsCheckBox" data-type="' + item.type + '" data-name="' + item.name + '" data-details=\'' + JSON.stringify(item.details) + '\' value="1" /> <span></span> </label> </div> <div class="task-title"> <span class="task-title-sp"> ' + item.name + ' </span> </div> </li>';
                            });
                            $("#selectAll").parent().parent().parent().parent().removeClass('hidden');
                            $("#addSelectedQuestions").removeClass('hidden')
                            $("#coursesQuestions").html(html);
                        }
                    });
                }

            });

            $(document).on('click', '#addSelectedQuestions', function () {
                $(".questionsCheckBox:checked").each(function () {
                    name = $(this).data('name');
                    type = $(this).data('type');
                    details = JSON.parse($(this).attr('data-details'));
                    console.log(details);
                    questions = (questions > 0) ? questions : 0;
                    if (type == 'true_false') {
                        dataHTML = '<div class="row"> <div class="col-lg-10"> <div class="form-group col-lg-10"> <label for="questions_name_' + questions + '">{{ Lang::get('main.question') }}</label> <input type="text" class="form-control" name="questions[name][' + questions + ']" id="questions_name_' + questions + '" value="' + name + '" placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}"> <input type="hidden" name="questions[type][' + questions + ']" value="true_false"> </div> <div class="col-lg-2" style="margin-top: 24px;"> <div class="true"> <input type="radio" name="questions[answers][' + questions + ']" ' + ((details.answer == 1) ? 'checked="checked"' : '') + ' value="1" id="questions_answers_' + questions + '_true"> <label for="questions_answers_' + questions + '_true"> <span></span> </label> </div> <div class="false"> <input type="radio"  name="questions[answers][' + questions + ']" ' + ((details.answer == 0) ? 'checked="checked"' : '') + ' value="0" id="questions_answers_' + questions + '_false"> <label for="questions_answers_' + questions + '_false"> <span></span> </label> </div> </div> </div> <button class="btn btn-danger col-lg-2 remove_question" data-type="true_false" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> </div>';
                        $(wrapper_times).append(dataHTML);//add input box
                        trueFalseAnswersCount++;
                    } else if (type == 'chose_multiple') {
                        dataHTML = '<div class=""> <div class="form-group col-lg-10"> <label for="questions_name_' + questions + '">{{ Lang::get('main.question') }}</label> <input type="text" class="form-control" name="questions[name][' + questions + ']" id="questions_name_' + questions + '" value="' + name + '" placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}"> <input type="hidden" name="questions[type][' + questions + ']" value="chose_multiple"> </div> <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_multiple" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button>';
                        xx = 1;
                        details.forEach(function (dd) {
                            dataHTML += '<div class="form-group col-lg-6"><label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_' + xx + '">{{ Lang::get('main.choice') }} 1</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_' + xx + '" ' + ((dd.answer) ? 'checked="checked"' : '') + ' value="1" name="chose_question_answer[' + questions + '][' + (x - 1) + ']"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_' + xx + '"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][' + questions + '][]" value="' + dd.name + '" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_' + xx + '" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice') }} 1"> </div> </div> ';
                            xx++;
                        });
                        dataHTML += '</div>';
                        $(wrapper_times).append(dataHTML);//add input box
                        choiceMultipleAnswersCount++
                    } else if (type == 'chose_single') {
                        dataHTML = '<div class=""> <div class="form-group col-lg-10"> <label for="questions_name_' + questions + '">{{ Lang::get('main.question') }}</label> <input type="text" class="form-control" name="questions[name][' + questions + ']" id="questions_name_' + questions + '" value="' + name + '" placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}"> <input type="hidden" name="questions[type][' + questions + ']" value="chose_single"> </div> <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_single" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> ';
                        xx = 1;
                        details.forEach(function (dd) {
                            dataHTML += '<div class="form-group col-lg-6"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_' + xx + '">{{ Lang::get('main.choice') }} 1</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_' + xx + '" ' + ((dd.answer) ? 'checked="checked"' : '') + ' value="' + xx + '" name="chose_question_answer[' + questions + ']"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_' + xx + '"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][' + questions + '][]" value="' + dd.name + '" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_' + xx + '" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice') }} 1"> <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> ';
                            xx++;
                        });
                        dataHTML += '</div>';
                        $(wrapper_times).append(dataHTML);//add input box
                        choiceSingleAnswersCount++
                    }
                    questions++;
                });
            });
        });
    </script>
    <script>
        var all_questions;
        var str = '';
        var res = '';
        var wrapper_times = $("#questionsContent");
        var question_area = $('.questions-area');
        $(document).ready(function () {
            var token = "{{ csrf_token() }}";
            var module_questions = $('#module_id');
            var module_training = $('#module_training');
            module_questions.on('change', function () {
                $("#checkAllQuestion").prop('checked', false);
                question_area.hide();
                $('.checkAllQuestion').hide();
                var moduleID = $(this).val();
                if (moduleID) {
                    $.ajax({
                        url: "{{URL("admin/get_module_training")}}/" + moduleID,
                        type: "GET",
                        data: {_token: token},
                        dataType: "json",
                        success: function (data) {
                            console.log(data);
                            if (data) {
                                module_training.empty();
                                question_area.empty();
                                module_training.focus();
                                module_training.append('<option value="">{{ Lang::get('main.select') }} {{ Lang::get('main.training') }}</option>');
                                $.each(data, function (key, value) {
                                    console.log(value);
                                    $('select[name="module_training"]').append('<option value="' + value.id + '">' + value.name + '</option>');
                                });
                            } else {
                                module_training.empty();
                            }
                        }
                    });
                } else {
                    module_training.empty();
                }
            });


            module_training.on('change', function () {
                $("#checkAllQuestion").prop('checked', false);
                question_area.show().append('<div class="overlay"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">{{ Lang::get('main.loading') }}</span></div>');
                var trainingID = $(this).val();
                if (trainingID) {
                    $.ajax({
                        url: "{{URL("admin/get_training_questions")}}/" + trainingID,
                        type: "GET",
                        data: {_token: token},
                        dataType: "json",
                        success: function (data) {
                            if (data) {
                                console.log(data);
                                question_area.empty().append('<h5 style="margin-bottom: 20px; font-weight: bold;text-transform: capitalize">{{ Lang::get('main.select_questions_as_you_want') }}</h5>');
                                $.each(data, function (key, value2) {
                                    // console.log(value2);
                                    if (value2.type === 'true_false') {
                                         str = JSON.stringify(value2);
                                         res = str.replace(/'/g, "&apos;");
                                        question_area.show().append('<div class="md-checkbox"><input data-json=\'' + res + '\' name="selectedQuestions"  data-type="' + value2.type + '" data-name="' + value2.name_ar + '" type="checkbox" id="checkbox-' + value2.id + '" value="' + value2.id + '" class="md-check all"><label for="checkbox-' + value2.id + '"><span class="inc"></span><span class="check"></span><span class="box"></span> ' + value2.name_ar + '</label><i style="color: #f0ad4e; margin-left: 5px;" class="fa fa-check"></i> </div>');
                                    } else if (value2.type === 'chose_single') {
                                         str = JSON.stringify(value2);
                                         res = str.replace(/'/g, "&apos;");
                                        question_area.show().append('<div class="md-checkbox"><input data-json=\'' + res + '\' name="selectedQuestions" data-type="' + value2.type + '" data-name="' + value2.name_ar + '" type="checkbox" id="checkbox-' + value2.id + '" value="' + value2.id + '" class="md-check all"><label for="checkbox-' + value2.id + '"><span class="inc"></span><span class="check"></span><span class="box"></span> ' + value2.name_ar + '</label><i style="color: #337ab7; margin-left: 5px;" class="fa fa-check-square-o"></i> </div>');
                                    } else if (value2.type === 'chose_multiple') {
                                         str = JSON.stringify(value2);
                                         res = str.replace(/'/g, "&apos;");
                                        question_area.show().append('<div class="md-checkbox"><input data-json=\'' + res + '\' name="selectedQuestions"  data-type="' + value2.type + '" data-name="' + value2.name_ar + '" type="checkbox" id="checkbox-' + value2.id + '" value="' + value2.id + '" class="md-check all"><label for="checkbox-' + value2.id + '"><span class="inc"></span><span class="check"></span><span class="box"></span> ' + value2.name_ar + '</label><i style="color: #ed6b75; margin-left: 5px;" class="fa fa-check-square-o"></i><i style="color: #ed6b75; margin-left: 5px;" class="fa fa-check-square-o"></i> </div>');
                                    }
                                    $('.checkAllQuestion').show();
                                });
                            } else {
                                question_area.hide()
                            }
                        }
                    });
                } else {
                    question_area.hide()
                }
            });


            $("#checkAllQuestion").on('click', function () {
                console.log('checkAllQuestion');
                if ($("#checkAllQuestion").is(':checked')) {
                    $.each($('input[name="selectedQuestions"]'), function () {
                        $('input[name="selectedQuestions"]').prop("checked", true);
                    });
                } else {
                    $.each($('input[name="selectedQuestions"]'), function () {
                        $('input[name="selectedQuestions"]').prop("checked", false);
                    });
                }
            });

            $('#importSelected').on('click', function () {
                var data = '';
                var count = {{(isset($choseSingleCount))?$choseSingleCount+1:0}};
                var count_multiple = {{(isset($choseMultipleCount))?$choseMultipleCount+1:0}}};
                var question_name_ar = '';
                var question_name_en = '';
                var question_type = '';
                var single_question = '';
                var true_false_question = '';
                var difficulty_type = '';
                var multiple = '';
                var x ={{isset($x)?$x:0}};
                var yx ={{isset($yx)?$yx:0}};
                $('#first_opt').prop("selected", true);
                $('#select2-module_id-container').text('{{ Lang::get('main.select') }} {{ Lang::get('main.module') }}');
                $.each($('input[name="selectedQuestions"]:checked'), function (index) {
                    data = JSON.parse($(this).attr('data-json'));
                    console.log(data);
                    question_name_ar = data.name_ar;
                    question_name_en = data.name_en;
                    question_type = data.type;
                    difficulty_type = data.difficulty_type;
                    console.log(difficulty_type);
                    if (question_type === 'true_false') {
                        details = data.modules_trainings_questions_details[0];
                        console.log(details);
                        console.log(details.answer);
                        true_false_question = '<div class="row"><div class="col-lg-10"><div class="form-group col-lg-8"><label for="questions_name_' + questions + '">{{ Lang::get('main.question') }}</label><input type="text" class="form-control" name="questions[name_ar][]" value="' + question_name_ar + '" id="questions_name_ar_' + questions + '" placeholder="{{ Lang::get('main.enter_arabic_question') }}"><input type="text" class="form-control" name="questions[name_en][]" value="' + question_name_en + '" id="questions_name_en_' + questions + '" placeholder="{{ Lang::get('main.enter_english_question') }}"><input type="hidden" name="questions[type][]" value="true_false"></div><div class="col-lg-2"><label for="">{{ Lang::get('difficulty_type') }}</label><select style="width:100%" class="sel2 select2-hidden-accessible" name="questions[difficulty_type][]" id="difficulty_type" tabindex="-1" aria-hidden="true"><option ' + (difficulty_type === 'easy' ? 'selected="selected"' : '') + ' value="easy">{{ Lang::get('easy') }}</option><option ' + (difficulty_type === 'normal' ? 'selected="selected"' : '') + ' value="normal">{{ Lang::get('normal') }}</option><option ' + (difficulty_type === 'hard' ? 'selected="selected"' : '') + ' value="hard">{{ Lang::get('hard') }}</option></select><span class="select2 select2-container select2-container--bootstrap" dir="ltr" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-difficulty_type-container"><span class="select2-selection__rendered" id="select2-difficulty_type-container" title="' + difficulty_type + '" style="text-transform: capitalize">' + difficulty_type + '</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span></div>';
                        true_false_question += '<div class="col-lg-2" style="margin-top: 24px;"><div class="true"><input type="radio" name="questions[answers][' + questions + ']" value="1" ' + (details.answer == 1 ? 'checked="checked"' : '') + ' id="questions_answers_' + questions + '_true"><label for="questions_answers_' + questions + '_true"><span></span></label></div><div class="false"><input type="radio" name="questions[answers][' + questions + ']" value="0"  ' + (details.answer == 0 ? 'checked="checked"' : '') + ' id="questions_answers_' + questions + '_false"><label for="questions_answers_' + questions + '_false"><span></span></label></div></div>';
                        true_false_question += '</div><button class="btn btn-danger col-lg-2 remove_question" data-type="' + question_type + '" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button></div>';
                        $(wrapper_times).append(true_false_question);
                    } else if (question_type === 'chose_single') {
                        single_question = '<div>';
                        single_question += '<div class="form-group col-lg-8"><label for="questions_name_' + questions + '"></label><input type="text" class="form-control" name="questions[name_ar][]" value="' + question_name_ar + '" id="questions_name_ar_' + questions + '" placeholder="{{ Lang::get('main.enter_arabic_question') }}"><input type="text" class="form-control" name="questions[name_en][]" value="' + question_name_en + '" id="questions_name_en_' + questions + '" placeholder="{{ Lang::get('main.enter_english_question') }}"><input type="hidden" name="questions[type][]" value="' + question_type + '"></div><div class="col-lg-2"><label for="">{{ Lang::get('difficulty_type') }}</label><select style="width:100%" class="sel2 select2-hidden-accessible" name="questions[difficulty_type][]" id="difficulty_type" tabindex="-1" aria-hidden="true"><option ' + (difficulty_type === 'easy' ? 'selected="selected"' : '') + ' value="easy">{{ Lang::get('easy') }}</option><option ' + (difficulty_type === 'normal' ? 'selected="selected"' : '') + ' value="normal">{{ Lang::get('normal') }}</option><option ' + (difficulty_type === 'hard' ? 'selected="selected"' : '') + ' value="hard">{{ Lang::get('hard') }}</option></select><span class="select2 select2-container select2-container--bootstrap" dir="ltr" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-difficulty_type-container"><span class="select2-selection__rendered" id="select2-difficulty_type-container" title="' + difficulty_type + '" style="text-transform: capitalize">' + difficulty_type + '</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span></div><button class="btn btn-danger col-lg-2 remove_question" data-type="' + question_type + '" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button>';
                        $.each(data.modules_trainings_questions_details, function (i, value) {
                            single_question += '<div class="form-group col-lg-6"><label for="chose_single_' + x + '_' + count + '_' + yx + '">{{ Lang::get('main.choice') }} ' + (i + 1) + '</label><div class="input-group"><span class="input-group-addon chose"><input type="radio" id="chose_single_' + x + '_' + count + '_' + yx + '" value="' + value.answer + '"  ' + (value.answer === 1 ? 'checked="checked"' : '') + ' name="chose_question_answer[' + questions + ']"><label for="chose_single_' + x + '_' + count + '_' + yx + '"><span></span></label></span><input type="text" class="form-control" name="questions_ar[answers][' + questions + '][]" value="' + value.name_ar + '" id="chose_single_' + x + '_' + count + '_' + yx + '" placeholder="{{ Lang::get('enter_arabic_choice') }} ' + (i + 1) + '"><input type="text" class="form-control" name="questions_en[answers][' + questions + '][]" value="' + value.name_en + '" id="chose_single_' + x + '_' + count + '_' + yx + '" placeholder="{{ Lang::get('enter_english_choice') }} ' + (i + 1) + '"><span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a></span></div></div>';
                            count++;
                            yx++
                        });
                        single_question += '</div>';
                        $(wrapper_times).append(single_question);
                    } else if (question_type === 'chose_multiple') {
                        details = data.modules_trainings_questions_details;
                        multiple = '<div class="">';
                        multiple += '<div class="form-group col-lg-8"><label for="questions_name_' + questions + '">Question</label><input type="text" class="form-control" name="questions[name_ar][]" value="' + question_name_ar + '" id="questions_name_ar_' + questions + '" placeholder="{{ Lang::get('main.enter_arabic_question') }}"><input type="text" class="form-control" name="questions[name_en][]" value="' + question_name_en + '" id="questions_name_en_' + questions + '" placeholder="{{ Lang::get('main.enter_english_question') }}"><input type="hidden" name="questions[type][]" value="' + question_type + '"></div><div class="col-lg-2"><label for="">{{ Lang::get('difficulty_type') }}</label><select style="width:100%" class="sel2 select2-hidden-accessible" name="questions[difficulty_type][]" id="difficulty_type" tabindex="-1" aria-hidden="true"><option ' + (difficulty_type === 'easy' ? 'selected="selected"' : '') + ' value="easy">{{ Lang::get('easy') }}</option><option ' + (difficulty_type === 'normal' ? 'selected="selected"' : '') + ' value="normal">{{ Lang::get('normal') }}</option><option ' + (difficulty_type === 'hard' ? 'selected="selected"' : '') + ' value="hard">{{ Lang::get('hard') }}</option></select><span class="select2 select2-container select2-container--bootstrap" dir="ltr" style="width: 100%;"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-difficulty_type-container"><span class="select2-selection__rendered" id="select2-difficulty_type-container" title="' + difficulty_type + '" style="text-transform: capitalize">' + difficulty_type + '</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span></div><button class="btn btn-danger col-lg-2 remove_question" data-type="' + question_type + '" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button>';
                        $.each(data.modules_trainings_questions_details, function (i, value) {
                            multiple += '<div class="form-group col-lg-6"><label for="chose_multiple_' + x + '_' + count_multiple + '_' + yx + '">{{ Lang::get('main.choice') }} ' + (i + 1) + '</label><div class="input-group"><span class="input-group-addon chose"><input type="checkbox" id="chose_multiple_' + x + '_' + count_multiple + '_' + yx + '" value="1"  ' + (value.answer === 1 ? 'checked="checked"' : '') + ' name="chose_question_answer[' + questions + '][' + i + ']"><label for="chose_multiple_' + x + '_' + count_multiple + '_' + yx + '"><span></span></label></span><input type="text" class="form-control" name="questions_ar[answers][' + questions + '][' + i + ']" value="' + value.name_ar + '" id="chose_multiple_' + x + '_' + count_multiple + '_' + yx + '" placeholder="{{ Lang::get('enter_arabic_choice') }} ' + (i + 1) + '"><input type="text" class="form-control" name="questions_en[answers][' + questions + '][' + i + ']" value="' + value.name_en + '" id="chose_multiple_' + x + '_' + count_multiple + '_' + yx + '" placeholder="{{ Lang::get('enter_english_choice') }} ' + (i + 1) + '"></div></div>';
                            count_multiple++;
                            yx++;
                        });
                        // '<div class="form-group col-lg-6"><label for="chose_multiple_' + questions + '_' + count_multiple + '_2">Choice 2</label><div class="input-group"><span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + count_multiple + '_2" value="1" name="chose_question_answer[1][1]"> <label for="chose_multiple_' + questions + '_' + count_multiple + '_2"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][1][]" id="chose_multiple_' + questions + '_' + count_multiple + '_2" placeholder="Enter Choice 2"></div></div><div class="form-group col-lg-6"><label for="chose_multiple_' + questions + '_' + count_multiple + '_3">Choice 3</label><div class="input-group"><span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + count_multiple + '_3" value="1" name="chose_question_answer[1][2]"> <label for="chose_multiple_' + questions + '_' + count_multiple + '_3"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][1][]" id="chose_multiple_' + questions + '_' + count_multiple + '_3" placeholder="Enter Choice 3"></div></div><div class="form-group col-lg-6"><label for="chose_multiple_' + questions + '_' + count_multiple + '_4">Choice 4</label><div class="input-group"><span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + count_multiple + '_4" value="1" name="chose_question_answer[1][3]"> <label for="chose_multiple_' + questions + '_' + count_multiple + '_4"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][1][]" id="cchose_multiple_' + questions + '_' + count_multiple + '_4" placeholder="Enter Choice 4"></div></div>';
                        multiple += '</div>';
                        $(wrapper_times).append(multiple);
                    }
                    questions++;
                    $('#closeModal').click();
                    module_training.empty();
                    question_area.hide();
                    $('.checkAllQuestion').hide();
                    question_area.empty().append('<h5 style="margin-bottom: 20px; font-weight: bold;text-transform: capitalize">{{ Lang::get('select_questions_as_you_want') }}</h5>');
                });
                return false;
            });


            $('#searchQuestion').on('keyup', function () {
                $("#checkAllQuestion").prop('checked', false);
                console.log(module_training.val());
                var searchInput = $(this).val();
                $.ajax({
                    url: "{{URL("admin/search_training_question")}}",
                    type: "GET",
                    data: {_token: token, contains: searchInput, training_id: module_training.val()},
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        if (data != 'No search result') {
                            console.log(data);
                            question_area.empty().append('<h5 style="margin-bottom: 20px; font-weight: bold;text-transform: capitalize">{{ Lang::get('select_questions_as_you_want') }}</h5>');
                            $.each(data, function (key, value2) {
                                console.log(key);
                                console.log(JSON.stringify(value2));
                                console.log(value2.modules_trainings_questions_details);
                                if (value2.type === 'true_false') {
                                    str = JSON.stringify(value2);
                                    res = str.replace(/'/g, "&apos;");
                                    question_area.show().append('<div class="md-checkbox"><input data-json=\'' + res + '\' name="selectedQuestions"  data-type="' + value2.type + '" data-name="' + value2.name_ar + '" type="checkbox" id="checkbox-' + value2.id + '" value="' + value2.id + '" class="md-check all"><label for="checkbox-' + value2.id + '"><span class="inc"></span><span class="check"></span><span class="box"></span> ' + value2.name_ar + '</label><i style="color: #f0ad4e; margin-left: 5px;" class="fa fa-check"></i> </div>');
                                } else if (value2.type === 'chose_single') {
                                    str = JSON.stringify(value2);
                                    res = str.replace(/'/g, "&apos;");
                                    question_area.show().append('<div class="md-checkbox"><input data-json=\'' + res + '\' name="selectedQuestions" data-type="' + value2.type + '" data-name="' + value2.name_ar + '" type="checkbox" id="checkbox-' + value2.id + '" value="' + value2.id + '" class="md-check all"><label for="checkbox-' + value2.id + '"><span class="inc"></span><span class="check"></span><span class="box"></span> ' + value2.name_ar + '</label><i style="color: #337ab7; margin-left: 5px;" class="fa fa-check-square-o"></i> </div>');
                                } else if (value2.type === 'chose_multiple') {
                                    str = JSON.stringify(value2);
                                    res = str.replace(/'/g, "&apos;");
                                    question_area.show().append('<div class="md-checkbox"><input data-json=\'' + res + '\' name="selectedQuestions"  data-type="' + value2.type + '" data-name="' + value2.name_ar + '" type="checkbox" id="checkbox-' + value2.id + '" value="' + value2.id + '" class="md-check all"><label for="checkbox-' + value2.id + '"><span class="inc"></span><span class="check"></span><span class="box"></span> ' + value2.name_ar + '</label><i style="color: #ed6b75; margin-left: 5px;" class="fa fa-check-square-o"></i><i style="color: #ed6b75; margin-left: 5px;" class="fa fa-check-square-o"></i> </div>');
                                }
                                $('.checkAllQuestion').show();
                            });
                        } else {
                            question_area.empty().append('<h3>' + data + '</h3>');
                        }
                    }
                });
            });

        });
    </script>


@endsection