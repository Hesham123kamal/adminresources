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
                <a href="{{ URL('/admin/courses_questions') }}">{{ Lang::get('main.courses_questions') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
            <li>
                <span>{{ $course_question->name }}</span>
            </li>
        </ul>

    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.courses_questions') }}
        <small>{{ Lang::get('main.edit') }}</small>
        <button type="button" class="btn btn-primary pull-right" data-toggle="modal"
                data-target=".import-course-question-modal">Import Questions
        </button>
    </h1>

    <div class="modal fade import-course-question-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" style="margin-top: 50px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Import Questions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(['url'=>'admin/import_course_questions']) !!}
                <div class="modal-body">
                    <div class="form-group form-group-select2">
                        <label for="course_id">{{ Lang::get('main.course_name') }}</label>
                        <select style="width: 100%" id="course_id" class="sel2" name="course_id">
                            <option value="" id="first_opt">-- Select Course --</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group form-group-select2">
                        <label for="course_curriculum">{{ Lang::get('main.course_curriculums') }}</label>
                        <select style="width: 100%" id="course_curriculum" class="sel2" name="course_curriculum">
                        </select>
                    </div>
                    <div class="row checkAllQuestion"
                         style="display: none;box-shadow: 0 7px 7px -6px #999; margin-bottom: 5px; padding-bottom: 10px;">
                        <div class="col-sm-6">
                            <span style="margin-left: 19px;">                            True or false
                            <i style="color: #f0ad4e;" class="fa fa-check"></i></span>
                            <span>                            Single choice
                            <i style="color: #337ab7;" class="fa fa-check-square-o"></i></span>
                            <span>                            Multiple choices
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
                                    <span class="box"></span>Check All</label>
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
                    <button type="submit" class="btn btn-primary" id="importSelected">Import selected</button>
                </div>
                {!! Form::close() !!}
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
                    <i class="icon-modules_questions font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.courses_questions') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/courses_questions/'.$course_question->id,'id'=>'addmodules_questionsEditForm','files'=>true]) !!}
                <input type="hidden" class="form-control" name="questions_type" value="{{ $course_question->questions_type }}">
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
                        <label for="module_id">{{ Lang::get('main.course_name') }}</label>
                        <select style="width: 100%" id="module_id" class="sel2" name="module_id" disabled>
                            @foreach($courses as $course)
                                <option @if($course->id==$course_question->course_id) selected="selected"
                                        @endif value="{{ $course_question->id }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group form-group-select2 col-lg-6">
                        <label for="curriculum_name">{{ Lang::get('main.curriculum_name') }}</label>
                        <select style="width: 100%" id="curriculum_name" class="sel2" name="curriculum_name" disabled>
                            <option value="{{ $course_question->id }}">{{ $course_question->description }}</option>
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
                                <option value="chose_single_with_images">{{ Lang::get('main.chose_answer_with_images') }}</option>
                                <option value="chose_multiple_with_images">{{ Lang::get('main.chose_answers_with_images') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1" style="margin-top: 22px;">
                        <button type="button" data-id="type-1" style="margin-top: 2px;" class="btn btn-primary addNewQuestion"><i
                                    class="glyphicon glyphicon-plus"></i></button>
                    </div>
                    <div class="clearfix"></div>
                    <div id="questionsContent">
                        <?php
                        $x = 0;
                        $trueFalseCount = 0;
                        $choseSingleCount = 0;
                        $choseMultipleCount = 0;
                        $choseSingleWithImagesCount = 0;
                        $choseMultipleWithImagesCount = 0;
                        $yx = 1;
                        ?>
                        @if(count($courses_questions))
                            @foreach($courses_questions as $question)
                                @if($question->type=='true_false')
                                    <div class="row">
                                        <div class="col-lg-10">
                                            <div class="form-group col-lg-7">
                                                <label for="questions_name_{{ $x }}">{{ Lang::get('main.question') }}</label>
                                                <input type="text" class="form-control" name="questions[name][{{$x}}]"
                                                       value="{{ $question->name }}" id="questions_name_{{ $x }}"
                                                       placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">
                                                @if($course_question->questions_type=='arabic_and_english')
                                                    <input type="text" class="form-control" name="questions[name_en][{{$x}}]"
                                                           value="{{ $question->name_en }}" id="questions_name_{{ $x }}"
                                                           placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                                @endif
                                                <input type="hidden" name="questions[type][{{$x}}]" value="true_false">
                                            </div>
                                            <div class="form-group col-lg-3">
                                                @if($question->image)
                                                    <img style="width: 100%;max-height: 200px;"
                                                         src="{{ assetURL('exams_question/'.$question->image) }}"
                                                         alt="">
                                                    <input type="hidden" name="old_image[{{ $x }}]" value="{{$question->image}}">
                                                @endif
                                                <input type="file" style="width: 100%;margin-top: 30px;"
                                                       name="image[{{ $x }}]">
                                            </div>

                                            <div class="col-lg-2" style="margin-top: 24px;">

                                                <div class="true">
                                                    <input type="radio" name="questions[answers][{{ $x }}]"
                                                           @if((isset($question->CurriculumQuestionsDetails->answer) && $question->CurriculumQuestionsDetails->answer == 1)) checked="checked"
                                                           @endif value="1" id="questions_answers_{{ $x }}_true">

                                                    <label for="questions_answers_{{ $x }}_true">

<span>
</span>
                                                    </label>
                                                </div>

                                                <div class="false">
                                                    <input type="radio" name="questions[answers][{{ $x }}]"
                                                           @if((isset($question->CurriculumQuestionsDetails->answer) && $question->CurriculumQuestionsDetails->answer == 0))  checked="checked"
                                                           @endif value="0" id="questions_answers_{{ $x }}_false">

                                                    <label for="questions_answers_{{ $x }}_false">

<span>
</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-danger col-lg-2 remove_question" data-type="true_false"
                                                style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i>
                                        </button>
                                    </div>
                                    <?php $trueFalseCount++;?>
                                @elseif($question->type=='chose_single')
                                    <div>
                                        <div class="form-group col-lg-7">
                                            <label for="questions_name_{{ $x }}">{{ Lang::get('main.question') }}
                                            </label>
                                            <input type="text" class="form-control" name="questions[name][{{$x}}]"
                                                   value="{{ $question->name }}" id="questions_name_{{ $x }}"
                                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">
                                            @if($course_question->questions_type=='arabic_and_english')
                                                <input type="text" class="form-control" name="questions[name_en][{{$x}}]"
                                                       value="{{ $question->name_en }}" id="questions_name_{{ $x }}"
                                                       placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                            @endif
                                            <input type="hidden" name="questions[type][{{$x}}]" value="chose_single">
                                        </div>
                                        <div class="form-group col-lg-3">
                                            @if($question->image)
                                                <img style="width: 100%;max-height: 200px;"
                                                     src="{{ assetURL('exams_question/'.$question->image) }}" alt="">
                                                <input type="hidden" name="old_image[{{ $x }}]" value="{{$question->image}}">
                                            @endif
                                            <input type="file" style="width: 100%;margin-top: 30px;"
                                                   name="image[{{ $x }}]">
                                        </div>
                                        <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_single"
                                                style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i>
                                        </button>
                                        <?php $yx = 1;?>
                                        @foreach($question->CurriculumQuestionsDetails as $detail)
                                            <div class="form-group col-lg-6">
                                                <label for="chose_single_{{ $x }}_{{ $choseSingleCount }}_{{ $yx }}">{{ Lang::get('main.choice').' '.$yx }}
                                                </label>
                                                <div class="input-group">

<span class="input-group-addon chose">
<input type="radio" id="chose_single_{{ $x }}_{{ $choseSingleCount }}_{{ $yx }}" @if($detail->answer) checked="checked"
       @endif value="{{ $yx }}" name="chose_question_answer[{{ $x }}]">
<label for="chose_single_{{ $x }}_{{ $choseSingleCount }}_{{ $yx }}">

<span>
</span>
</label>
</span>
                                                    <input type="text" class="form-control"
                                                           name="questions[answers][{{ $x }}][]"
                                                           value="{{ $detail->name }}"
                                                           id="chose_single_{{ $x }}_{{ $choseSingleCount }}_{{ $yx }}"
                                                           placeholder="{{ Lang::get('main.enter').Lang::get('main.choice').' '.$yx }}">

                                                    @if($course_question->questions_type=='arabic_and_english')
                                                        <input type="text" class="form-control"
                                                               name="questions_en[answers][{{ $x }}][]"
                                                               value="{{ $detail->name_en }}"
                                                               id="chose_single_{{ $x }}_{{ $yx }}"
                                                               placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en').' '.$yx }}">
                                                    @endif

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
                                @elseif($question->type=='chose_multiple')
                                    <div>
                                        <div class="form-group col-lg-7">
                                            <label for="questions_name_{{ $x }}">{{ Lang::get('main.question') }}
                                            </label>
                                            <input type="text" class="form-control" name="questions[name][{{$x}}]"
                                                   value="{{ $question->name }}" id="questions_name_{{ $x }}"
                                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">
                                            @if($course_question->questions_type=='arabic_and_english')
                                                <input type="text" class="form-control" name="questions[name_en][{{$x}}]"
                                                       value="{{ $question->name_en }}" id="questions_name_{{ $x }}"
                                                       placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                            @endif
                                            <input type="hidden" name="questions[type][{{$x}}]" value="chose_multiple">
                                        </div>
                                        <div class="form-group col-lg-3">
                                            @if($question->image)
                                                <img style="width: 100%;max-height: 200px;"
                                                     src="{{ assetURL('exams_question/'.$question->image) }}" alt="">
                                                <input type="hidden" name="old_image[{{ $x }}]" value="{{$question->image}}">
                                            @endif
                                            <input type="file" style="width: 100%;margin-top: 30px;"
                                                   name="image[{{ $x }}]">
                                        </div>
                                        <button class="btn btn-danger col-lg-2 remove_question"
                                                data-type="chose_multiple" style="margin-top: 24px;"><i
                                                    class="glyphicon glyphicon-trash"></i></button>
                                        <?php $yx = 1;?>
                                        @foreach($question->CurriculumQuestionsDetails as $detail)
                                            <div class="form-group col-lg-6">
                                                <label for="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_{{ $yx }}">{{ Lang::get('main.choice').' '.$yx }}
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
                                                           name="questions[answers][{{ $x }}][]"
                                                           value="{{ $detail->name }}"
                                                           id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_{{ $yx-1 }}"
                                                           placeholder="{{ Lang::get('main.enter').Lang::get('main.choice').' '.$yx }}">
                                                    @if($course_question->questions_type=='arabic_and_english')
                                                        <input type="text" class="form-control"
                                                               name="questions_en[answers][{{ $x }}][]"
                                                               value="{{ $detail->name_en }}"
                                                               id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_{{ $yx-1 }}"
                                                               placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en').' '.$yx }}">
                                                    @endif
                                                    <span class="input-group-addon "> <a href="#"
                                                                                         class="removeAnswer"><i
                                                                    class="glyphicon glyphicon-trash"></i></a>
</span>
                                                </div>
                                            </div>
                                            <?php $yx++;?>
                                        @endforeach
                                    </div>
                                    <?php $choseMultipleCount++;?>
                                    @elseif($question->type=='chose_single_with_images')
                                        <div>
                                            <div class="form-group col-lg-7">
                                                <label for="questions_name_{{ $x }}">{{ Lang::get('main.question') }}
                                                </label>
                                                <input type="text" class="form-control" name="questions[name][{{$x}}]"
                                                       value="{{ $question->name }}" id="questions_name_{{ $x }}"
                                                       placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">
                                                @if($course_question->questions_type=='arabic_and_english')
                                                    <input type="text" class="form-control" name="questions[name_en][{{$x}}]"
                                                           value="{{ $question->name_en }}" id="questions_name_{{ $x }}"
                                                           placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                                @endif
                                                <input type="hidden" name="questions[type][{{$x}}]" value="chose_single_with_images">
                                            </div>
                                            <div class="form-group col-lg-3">
                                                @if($question->image)
                                                    <img style="width: 100%;max-height: 200px;"
                                                         src="{{ assetURL('exams_question/'.$question->image) }}" alt="">
                                                    <input type="hidden" name="old_image[{{ $x }}]" value="{{$question->image}}">
                                                @endif
                                                <input type="file" style="width: 100%;margin-top: 30px;"
                                                       name="image[{{ $x }}]">
                                            </div>
                                            <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_single_with_images"
                                                    style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i>
                                            </button>
                                            <?php $yx = 1;?>
                                            @foreach($question->CurriculumQuestionsDetails as $detail)
                                                <div class="form-group col-lg-6">
                                                    <label for="chose_single_with_images_{{ $x }}_{{ $choseSingleWithImagesCount }}_{{ $yx }}">{{ Lang::get('main.choice').' '.$yx }}
                                                    </label>
                                                    <div class="input-group">

<span class="input-group-addon chose">
<input type="radio" id="chose_single_with_images_{{ $x }}_{{ $choseSingleWithImagesCount }}_{{ $yx }}" @if($detail->answer) checked="checked"
       @endif value="{{ $yx }}" name="chose_question_answer[{{ $x }}]">
<label for="chose_single_with_images_{{ $x }}_{{ $choseSingleWithImagesCount }}_{{ $yx }}">

<span>
</span>
</label>
</span>


                                                        <img style="width:150px;" src="{{assetURL($detail->image) }}">
                                                        <input type="hidden" class="form-control" name="questions[images_answers][{{ $x }}][]" value="{{ $detail->image }}" id="chose_single_with_image_{{ $x }}_{{ $choseSingleWithImagesCount }}_{{ $yx }}">

                                                        <span class="input-group-addon "> <a href="#"
                                                                                             class="removeAnswer"><i
                                                                        class="glyphicon glyphicon-trash"></i></a>
</span>
                                                    </div>
                                                </div>
                                                <?php $yx++;?>
                                            @endforeach
                                        </div>
                                        <?php $choseSingleWithImagesCount++;?>
                                    @elseif($question->type=='chose_multiple_with_images')
                                        <div>
                                            <div class="form-group col-lg-7">
                                                <label for="questions_name_{{ $x }}">{{ Lang::get('main.question') }}
                                                </label>
                                                <input type="text" class="form-control" name="questions[name][{{$x}}]"
                                                       value="{{ $question->name }}" id="questions_name_{{ $x }}"
                                                       placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">
                                                @if($course_question->questions_type=='arabic_and_english')
                                                    <input type="text" class="form-control" name="questions[name_en][{{$x}}]"
                                                           value="{{ $question->name_en }}" id="questions_name_{{ $x }}"
                                                           placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                                @endif
                                                <input type="hidden" name="questions[type][{{$x}}]" value="chose_multiple_with_images">
                                            </div>
                                            <div class="form-group col-lg-3">
                                                @if($question->image)
                                                    <img style="width: 100%;max-height: 200px;"
                                                         src="{{ assetURL('exams_question/'.$question->image) }}" alt="">
                                                    <input type="hidden" name="old_image[{{ $x }}]" value="{{$question->image}}">
                                                @endif
                                                <input type="file" style=" width: 100%;margin-top: 30px;"
                                                       name="image[{{ $x }}]">
                                            </div>
                                            <button class="btn btn-danger col-lg-2 remove_question"
                                                    data-type="chose_multiple_with_images" style="margin-top: 24px;"><i
                                                        class="glyphicon glyphicon-trash"></i></button>
                                            <?php $yx = 1;?>
                                            @foreach($question->CurriculumQuestionsDetails as $detail)
                                                <div class="form-group col-lg-6">
                                                    <label for="chose_multiple_with_images_{{ $x }}_{{ $choseMultipleWithImagesCount }}_{{ $yx }}">{{ Lang::get('main.choice').' '.$yx }}
                                                    </label>
                                                    <div class="input-group">
                                                    <span class="input-group-addon chose">
                                                    <input type="checkbox"
                                                           id="chose_multiple_with_images_{{ $x }}_{{ $choseMultipleWithImagesCount }}_{{ $yx }}"
                                                           @if($detail->answer)  checked="checked" @endif value="1"
                                                           name="chose_question_answer[{{ $x }}][{{ $yx-1 }}]">
                                                    <label for="chose_multiple_with_images_{{ $x }}_{{ $choseMultipleWithImagesCount }}_{{ $yx }}">

                                                    <span>
                                                    </span>
                                                    </label>
                                                    </span>
                                                        <img style="width:150px;" src="{{assetURL($detail->image) }}">
                                                        <input type="hidden" class="form-control" name="questions[images_answers][{{ $x }}][]" value="{{ $detail->image }}" id="chose_multiple_with_images_{{ $x }}_{{ $choseMultipleWithImagesCount }}_{{ $yx-1 }}" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_'.$yx) }}">
                                                        <span class="input-group-addon "> <a href="#"
                                                                                             class="removeAnswer"><i
                                                                        class="glyphicon glyphicon-trash"></i></a>
</span>
                                                    </div>
                                                </div>
                                                <?php $yx++;?>
                                            @endforeach

                                        </div>
                                        <?php $choseMultipleWithImagesCount++;?>
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
                                <option value="chose_single_with_images">{{ Lang::get('main.chose_answer_with_images') }}</option>
                                <option value="chose_multiple_with_images">{{ Lang::get('main.chose_answers_with_images') }}</option>
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
            $(".sel2").select2();
            var token = '{{ csrf_token() }}';
            var wrapper_times = $("#questionsContent"); //title Fields wrapper
            var add_button_times = $(".addNewQuestion"); //title Add button ID
            var trueFalseAnswersCount ={{ (isset($trueFalseCount))?$trueFalseCount+1:0 }};
            var choiceMultipleAnswersCount ={{ (isset($choseMultipleCount))?$choseMultipleCount+1:0 }};
            var choiceSingleAnswersCount ={{ (isset($choseSingleCount))?$choseSingleCount+1:0 }};
            var choiceMultipleWithImagesAnswersCount ={{ (isset($choiceMultipleWithImagesCount))?$choiceMultipleWithImagesCount+1:0 }};
            var choiceSingleWithImagesAnswersCount ={{ (isset($choiceSingleWithImagesCount))?$choiceSingleWithImagesCount+1:0 }};
            $(document).on('click', '.removeAnswer', function (e) {
                e.preventDefault();
                $(this).parent().parent().parent().remove();
            });
            $(document).on('click', '.addNewQuestion', function (e) { //on add input button click
                e.preventDefault();
                type = $("#" + $(this).data('id')).val();
                console.log(type);
                questions = (questions  > 0) ? questions : 0;
                english_question='';

                @if($course_question->questions_type=='arabic_and_english')
                    english_question='<input type="text" class="form-control" name="questions[name_en]['+questions+']" id="questions_name_'+questions+'" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">';
                @endif

                if (type == 'true_false') {
                    $(wrapper_times).append('<div class="row"> <div class="col-lg-10"> <div class="form-group col-lg-7"> <label for="questions_name_' + questions + '">Question</label> <input type="text" class="form-control" name="questions[name][' + questions + ']" id="questions_name_' + questions + '" placeholder="Enter Question">'+english_question+'<input type="hidden" name="questions[type][' + questions + ']" value="true_false"> </div><div class="form-group col-lg-3"><input type="file" style="width: 100%;margin-top: 30px;" name="image[' + questions + ']"></div> <div class="col-lg-2" style="margin-top: 24px;"> <div class="true"> <input type="radio" name="questions[answers][' + questions + ']" checked="checked" value="1" id="questions_answers_' + questions + '_true"> <label for="questions_answers_' + questions + '_true"> <span></span> </label> </div> <div class="false"> <input type="radio" name="questions[answers][' + questions + ']" value="0" id="questions_answers_' + questions + '_false"> <label for="questions_answers_' + questions + '_false"> <span></span> </label> </div> </div> </div> <button class="btn btn-danger col-lg-2 remove_question" data-type="true_false" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> </div>');//add input box
                } else if (type == 'chose_multiple') {
                    english_detail1='';
                    english_detail2='';
                    english_detail3='';
                    english_detail4='';
                    @if($course_question->questions_type=='arabic_and_english')
                        english_detail1='<input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_1" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en').' 1' }}">';
                        english_detail2='<input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_2" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en').' 2' }}">';
                        english_detail3='<input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_3" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en').' 3' }}">';
                        english_detail4='<input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_4" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en').' 4' }}">';
                    @endif
                    $(wrapper_times).append('<div class=""> <div class="form-group col-lg-7"> <label for="questions_name_' + questions + '">Question</label> <input type="text" class="form-control" name="questions[name][' + questions + ']" id="questions_name_' + questions + '" placeholder="Enter Question">'+english_question+'<input type="hidden" name="questions[type][' + questions + ']" value="chose_multiple"> </div><div class="form-group col-lg-3"><input type="file" style="width: 100%;margin-top: 30px;" name="image[' + questions + ']"></div> <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_multiple" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> <div class="form-group col-lg-6"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_1">Choice 1</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_1" value="1" name="chose_question_answer[' + questions + '][0]"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_1"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][' + questions + '][]" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_1" placeholder="Enter Choice 1">'+english_detail1+'<span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_2">Choice 2</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_2" value="1" name="chose_question_answer[' + questions + '][1]"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_2"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][' + questions + '][]" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_2" placeholder="Enter Choice 2">'+english_detail2+'<span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_3">Choice 3</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_3" value="1" name="chose_question_answer[' + questions + '][2]"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_3"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][' + questions + '][]" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_3" placeholder="Enter Choice 3">'+english_detail3+'<span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_4">Choice 4</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_4" value="1" name="chose_question_answer[' + questions + '][3]"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_4"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][' + questions + '][]" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_4" placeholder="Enter Choice 4">'+english_detail4+'<span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> </div>');//add input box
                } else if (type == 'chose_single') {
                    english_detail1='';
                    english_detail2='';
                    english_detail3='';
                    english_detail4='';
                    @if($course_question->questions_type=='arabic_and_english')
                        english_detail1='<input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_1" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en').' 1' }}">';
                        english_detail2='<input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_2" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en').' 2' }}">';
                        english_detail3='<input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_3" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en').' 3' }}">';
                        english_detail4='<input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_4" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en').' 4' }}">';
                    @endif
                    $(wrapper_times).append('<div class=""> <div class="form-group col-lg-7"> <label for="questions_name_' + questions + '">Question</label> <input type="text" class="form-control" name="questions[name][' + questions + ']" id="questions_name_' + questions + '" placeholder="Enter Question">'+english_question+'<input type="hidden" name="questions[type][' + questions + ']" value="chose_single"> </div><div class="form-group col-lg-3"><input type="file" style="width: 100%;margin-top: 30px;" name="image[' + questions + ']"></div> <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_single" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> <div class="form-group col-lg-6"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_1">Choice 1</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_1" value="1" name="chose_question_answer[' + questions + ']"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_1"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][' + questions + '][]" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_1" placeholder="Enter Choice 1">'+english_detail1+'<span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_2">Choice 2</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_2" value="2" name="chose_question_answer[' + questions + ']"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_2"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][' + questions + '][]" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_2" placeholder="Enter Choice 2">'+english_detail1+'<span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_3">Choice 3</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_3" value="3" name="chose_question_answer[' + questions + ']"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_3"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][' + questions + '][]" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_3" placeholder="Enter Choice 3">'+english_detail3+'<span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_4">Choice 4</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_4" value="4" name="chose_question_answer[' + questions + ']"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_4"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][' + questions + '][]" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_4" placeholder="Enter Choice 4">'+english_detail4+'<span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> </div>');//add input box
                }else if (type == 'chose_multiple_with_images') {
                    $(wrapper_times).append('<div class=""> <div class="form-group col-lg-7"> <label for="questions_name_' + questions + '">Question</label> <input type="text" class="form-control" name="questions[name][' + questions + ']" id="questions_name_' + questions + '" placeholder="Enter Question">'+english_question+'<input type="hidden" name="questions[type][' + questions + ']" value="chose_multiple_with_images"> </div><div class="form-group col-lg-3"><input type="file" style="width: 100%;margin-top: 30px;" name="image[' + questions + ']"></div> <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_multiple_with_images" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> <div class="form-group col-lg-6"> <label for="chose_multiple_with_images_' + questions + '_' + choiceMultipleWithImagesAnswersCount + '_1">Choice 1</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_with_images_' + questions + '_' + choiceMultipleWithImagesAnswersCount + '_1" value="1" name="chose_question_answer[' + questions + '][0]"> <label for="chose_multiple_with_images_' + questions + '_' + choiceMultipleWithImagesAnswersCount + '_1"> <span></span> </label> </span> <input type="file" class="form-control" name="questions[answers][' + questions + '][]" id="chose_multiple_with_images_' + questions + '_' + choiceMultipleWithImagesAnswersCount + '_1" placeholder="Enter Choice 1"><span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_multiple_with_images_' + questions + '_' + choiceMultipleWithImagesAnswersCount + '_2">Choice 2</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_with_images_' + questions + '_' + choiceMultipleWithImagesAnswersCount + '_2" value="1" name="chose_question_answer[' + questions + '][1]"> <label for="chose_multiple_with_images_' + questions + '_' + choiceMultipleWithImagesAnswersCount + '_2"> <span></span> </label> </span> <input type="file" class="form-control" name="questions[answers][' + questions + '][]" id="chose_multiple_with_images_' + questions + '_' + choiceMultipleWithImagesAnswersCount + '_2" placeholder="Enter Choice 2"><span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_multiple_with_images_' + questions + '_' + choiceMultipleWithImagesAnswersCount + '_3">Choice 3</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_with_images_' + questions + '_' + choiceMultipleWithImagesAnswersCount + '_3" value="1" name="chose_question_answer[' + questions + '][2]"> <label for="chose_multiple_with_images_' + questions + '_' + choiceMultipleWithImagesAnswersCount + '_3"> <span></span> </label> </span> <input type="file" class="form-control" name="questions[answers][' + questions + '][]" id="chose_multiple_with_images_' + questions + '_' + choiceMultipleAnswersCount + '_3" placeholder="Enter Choice 3"><span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_multiple_with_images_' + questions + '_' + choiceMultipleWithImagesAnswersCount + '_4">Choice 4</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_with_images_' + questions + '_' + choiceMultipleWithImagesAnswersCount + '_4" value="1" name="chose_question_answer[' + questions + '][3]"> <label for="chose_multiple_with_images_' + questions + '_' + choiceMultipleWithImagesAnswersCount + '_4"> <span></span> </label> </span> <input type="file" class="form-control" name="questions[answers][' + questions + '][]" id="chose_multiple_with_images_' + questions + '_' + choiceMultipleWithImagesAnswersCount + '_4" placeholder="Enter Choice 4"><span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> </div>');//add input box
                }else if (type == 'chose_single_with_images') {
                    $(wrapper_times).append('<div class=""> <div class="form-group col-lg-7"> <label for="questions_name_' + questions + '">Question</label> <input type="text" class="form-control" name="questions[name][' + questions + ']" id="questions_name_' + questions + '" placeholder="Enter Question">'+english_question+'<input type="hidden" name="questions[type][' + questions + ']" value="chose_single_with_images"> </div><div class="form-group col-lg-3"><input type="file" style="width: 100%;margin-top: 30px;" name="image[' + questions + ']"></div> <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_single_with_images" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> <div class="form-group col-lg-6"> <label for="chose_single_with_images_' + questions + '_' + choiceSingleWithImagesAnswersCount + '_1">Choice 1</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_with_images_' + questions + '_' + choiceSingleWithImagesAnswersCount + '_1" value="1" name="chose_question_answer[' + questions + ']"> <label for="chose_single_with_images_' + questions + '_' + choiceSingleWithImagesAnswersCount + '_1"> <span></span> </label> </span> <input type="file" class="form-control" name="questions[answers][' + questions + '][]" id="chose_single_with_images_' + questions + '_' + choiceSingleWithImagesAnswersCount + '_1" placeholder="Enter Choice 1"> <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_single_with_images_' + questions + '_' + choiceSingleWithImagesAnswersCount + '_2">Choice 2</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_with_images_' + questions + '_' + choiceSingleWithImagesAnswersCount + '_2" value="2" name="chose_question_answer[' + questions + ']"> <label for="chose_single_with_images_' + questions + '_' + choiceSingleAnswersCount + '_2"> <span></span> </label> </span> <input type="file" class="form-control" name="questions[answers][' + questions + '][]" id="chose_single_with_images_' + questions + '_' + choiceSingleWithImagesAnswersCount + '_2" placeholder="Enter Choice 2"> <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_single_with_images_' + questions + '_' + choiceSingleWithImagesAnswersCount + '_3">Choice 3</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_with_images_' + questions + '_' + choiceSingleWithImagesAnswersCount + '_3" value="3" name="chose_question_answer[' + questions + ']"> <label for="chose_single_with_images_' + questions + '_' + choiceSingleWithImagesAnswersCount + '_3"> <span></span> </label> </span> <input type="file" class="form-control" name="questions[answers][' + questions + '][]" id="chose_single_with_images_' + questions + '_' + choiceSingleWithImagesAnswersCount + '_3" placeholder="Enter Choice 3"> <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_single_with_images_' + questions + '_' + choiceSingleWithImagesAnswersCount + '_4">Choice 4</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_with_images_' + questions + '_' + choiceSingleWithImagesAnswersCount + '_4" value="4" name="chose_question_answer[' + questions + ']"> <label for="chose_single_with_images_' + questions + '_' + choiceSingleWithImagesAnswersCount + '_4"> <span></span> </label> </span> <input type="file" class="form-control" name="questions[answers][' + questions + '][]" id="chose_single_with_images_' + questions + '_' + choiceSingleWithImagesAnswersCount + '_4" placeholder="Enter Choice 4"> <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> </div>');//add input box
                }
                questions++;
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
                } else if(type=='chose_multiple_with_images'){
                    $(this).parent('div').remove();
                    choiceMultipleWithImagesAnswersCount--;
                }else if(type=='chose_single_with_images'){
                    $(this).parent('div').remove();
                    choiceSingleWithImagesAnswersCount--;
                }
                questions--;
            });

        });
    </script>
    <script>
        var all_questions;
        var wrapper_times = $("#questionsContent");
        var question_area = $('.questions-area');
        $(document).ready(function () {
            var token = "{{ csrf_token() }}";
            var course_curriculum = $('#course_curriculum');
            $('#course_id').on('change', function () {
                $("#checkAllQuestion").prop('checked', false);
                question_area.hide();
                $('.checkAllQuestion').hide();
                var courseID = $(this).val();
                if (courseID) {
                    $.ajax({
                        url: "{{URL("admin/get_course_curriculum")}}/" + courseID,
                        type: "GET",
                        data: {_token: token},
                        dataType: "json",
                        success: function (data) {
                            //console.log(data.id);
                            //console.log(data.description);
                            if (data) {
                                course_curriculum.empty();
                                question_area.empty();
                                course_curriculum.focus();
                                course_curriculum.append('<option value="">-- Select Curriculum --</option>');
                                $.each(data, function (key, value) {
                                    current_questions_type="{{$course_question->questions_type}}";
                                    //console.log(value.id);
                                    //console.log(value.description);
                                    //if(current_questions_type == value.questions_type)
                                    $('select[name="course_curriculum"]').append('<option value="' + value.id + '">' + value.description + '</option>');
                                });
                            } else {
                                course_curriculum.empty();
                            }
                        }
                    });
                } else {
                    course_curriculum.empty();
                }
            });


            course_curriculum.on('change', function () {
                $("#checkAllQuestion").prop('checked', false);
                question_area.show().append('<div class="overlay"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">{{ Lang::get('main.loading') }}</span></div>');
                var curriculumID = $(this).val();
                if (curriculumID) {
                    $.ajax({
                        url: "{{URL("admin/get_curriculum_questions")}}/" + curriculumID,
                        type: "GET",
                        data: {_token: token},
                        dataType: "json",
                        success: function (data) {
                            if (data) {
                                console.log(data);
                                question_area.empty().append('<h5 style="margin-bottom: 20px; font-weight: bold;text-transform: capitalize">select questions as you want</h5>');
                                $.each(data, function (key, value2) {
                                    console.log(key);
                                    console.log(value2.curriculum_questions_details);
                                    if (value2.type === 'true_false') {
                                        question_area.show().append('<div class="md-checkbox"><input data-json=\'' + JSON.stringify(value2) + '\' name="selectedQuestions"  data-type="' + value2.type + '" data-name="' + value2.name + '" type="checkbox" id="checkbox-' + value2.id + '" value="' + value2.id + '" class="md-check all"><label for="checkbox-' + value2.id + '"><span class="inc"></span><span class="check"></span><span class="box"></span> ' + value2.name + '</label><i style="color: #f0ad4e; margin-left: 5px;" class="fa fa-check"></i> </div>');
                                    } else if (value2.type === 'chose_single' || value2.type=='chose_single_with_images') {
                                        question_area.show().append('<div class="md-checkbox"><input data-json=\'' + JSON.stringify(value2) + '\' name="selectedQuestions" data-type="' + value2.type + '" data-name="' + value2.name + '" type="checkbox" id="checkbox-' + value2.id + '" value="' + value2.id + '" class="md-check all"><label for="checkbox-' + value2.id + '"><span class="inc"></span><span class="check"></span><span class="box"></span> ' + value2.name + '</label><i style="color: #337ab7; margin-left: 5px;" class="fa fa-check-square-o"></i> </div>');
                                    } else if (value2.type === 'chose_multiple' || value2.type === 'chose_multiple_with_images') {
                                        question_area.show().append('<div class="md-checkbox"><input data-json=\'' + JSON.stringify(value2) + '\' name="selectedQuestions"  data-type="' + value2.type + '" data-name="' + value2.name + '" type="checkbox" id="checkbox-' + value2.id + '" value="' + value2.id + '" class="md-check all"><label for="checkbox-' + value2.id + '"><span class="inc"></span><span class="check"></span><span class="box"></span> ' + value2.name + '</label><i style="color: #ed6b75; margin-left: 5px;" class="fa fa-check-square-o"></i><i style="color: #ed6b75; margin-left: 5px;" class="fa fa-check-square-o"></i> </div>');
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
                var count = {{ (isset($choseSingleCount))?$choseSingleCount+1:0 }};
                var count_multiple = {{ (isset($choseMultipleCount))?$choseMultipleCount+1:0 }};
                var count_single_with_images = {{ (isset($choseSingleWithImagesCount))?$choseSingleWithImagesCount+1:0 }};
                var count_multiple_with_images = {{ (isset($choseMultipleWithImagesCount))?$choseMultipleWithImagesCount+1:0 }};
                var question_name = '';
                var question_type = '';
                var single_question = '';
                var single_question_with_images = '';
                var true_false_question = '';
                var multiple_question_with_images= '';
                var multiple= '';
                var x ={{ isset($x)?$x:0 }};
                var yx ={{ isset($yx)?$yx:0 }};
                $('#first_opt').prop("selected", true);
                $('#select2-course_id-container').text('-- Select Course --');
                $.each($('input[name="selectedQuestions"]:checked'), function (index) {
                    data = JSON.parse($(this).attr('data-json'));
                    console.log(data);
                    question_name = data.name;
                    question_name_en = data.name_en==null ?'':data.name_en;
                    question_type = data.type;
                    english_question='';
                    @if($course_question->questions_type=='arabic_and_english')
                        english_question='<input type="text" class="form-control" name="questions[name_en]['+questions+']" value="'+question_name_en+'" id="questions_name_'+questions+'" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">';
                    @endif
                    if (question_type === 'true_false') {
                        true_false_question = '<div class="row"><div class="col-lg-10"><div class="form-group col-lg-7"><label for="questions_name_' + questions + '">Question</label><input type="text" class="form-control" name="questions[name]['+questions+']" value="' + question_name + '" id="questions_name_' + questions + '" placeholder="Enter Question">'+english_question+'<input type="hidden" name="questions[type]['+questions+']" value="' + question_type + '"></div><div class="form-group col-lg-3"><input type="file" style="width: 100%;margin-top: 30px;" name="image[' + questions + ']"></div>';
                        details = data.curriculum_questions_details[0];
                        console.log(details);
                        console.log(details.answer);
                        true_false_question += '<div class="col-lg-2" style="margin-top: 24px;"><div class="true"><input type="radio" name="questions[answers][' + questions + ']" value="1" ' + (details.answer == 1 ? 'checked="checked"' : '') + ' id="questions_answers_' + questions + '_true"><label for="questions_answers_' + questions + '_true"><span></span></label></div><div class="false"><input type="radio" name="questions[answers][' + questions + ']" value="0"  ' + (details.answer == 0 ? 'checked="checked"' : '') + ' id="questions_answers_' + questions + '_false"><label for="questions_answers_' + questions + '_false"><span></span></label></div></div></div>';
                        true_false_question += '<button class="btn btn-danger col-lg-2 remove_question" data-type="' + question_type + '" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button></div>';
                        $(wrapper_times).append(true_false_question);
                    } else if (question_type === 'chose_single') {
                        english_detail='';
                        single_question = '<div>';
                        single_question += '<div class="form-group col-lg-7"><label for="questions_name_' + questions + '">Question</label><input type="text" class="form-control" name="questions[name]['+questions+']" value="' + question_name + '" id="questions_name_' + questions + '" placeholder="Enter Question">'+english_question+'<input type="hidden" name="questions[type]['+questions+']" value="' + question_type + '"></div><div class="form-group col-lg-3"><input type="file" style="width: 100%;margin-top: 30px;" name="image[' + questions + ']"></div><button class="btn btn-danger col-lg-2 remove_question" data-type="' + question_type + '" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button>';
                        $.each(data.curriculum_questions_details, function (i, value) {
                            @if($course_question->questions_type=='arabic_and_english')
                                english_detail='<input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" value="' + value.name_en + '" id="chose_single_'+questions+'_'+count+'_'+ yx +'" placeholder="Enter English Choice ' + (i + 1) + '">';
                            @endif
                            single_question += '<div class="form-group col-lg-6"><label for="chose_single_' + questions + '_' + count + '_' + yx + '">Choice ' + (i + 1) + '</label><div class="input-group"><span class="input-group-addon chose"><input type="radio" id="chose_single_' + questions + '_' + count + '_' + yx + '" value="' + value.answer + '"  ' + (value.answer === 1 ? 'checked="checked"' : '') + '  name="chose_question_answer[' + questions + ']"><label for="chose_single_' + questions + '_' + count + '_' + yx + '"><span></span></label></span><input type="text" class="form-control" name="questions[answers][' + questions + '][]" value="' + value.name + '" id="chose_single_' + questions + '_' + count + '_' + yx + '" placeholder="Enter Choice ' + (i + 1) + '">'+english_detail+'<span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a></span></div></div>';
                            count++;
                            yx++
                        });
                        single_question += '</div>';
                        $(wrapper_times).append(single_question);
                    } else if (question_type === 'chose_multiple') {
                        english_detail='';
                        multiple = '<div class="">';
                        multiple += '<div class="form-group col-lg-7"><label for="questions_name_' + questions + '">Question</label> <input type="text" class="form-control" value="' + question_name + '" name="questions[name][' + questions + ']" id="questions_name_' + questions + '" placeholder="Enter Question">'+english_question+'<input type="hidden" name="questions[type][' + questions + ']" value="' + question_type + '"></div><div class="form-group col-lg-3"><input type="file" style="width: 100%;margin-top: 30px;" name="image[' + questions + ']"></div><button class="btn btn-danger col-lg-2 remove_question" data-type="' + question_type + '" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button>';
                        $.each(data.curriculum_questions_details, function (i, value) {
                            @if($course_question->questions_type=='arabic_and_english')
                                english_detail='<input type="text" class="form-control" name="questions_en[answers]['+questions+'][' + i + ']" value="' + value.name_en + '" id="chose_multiple_'+questions+'_'+count_multiple+'_'+ yx +'" placeholder="Enter English Choice ' + (i + 1) + '">';
                            @endif
                            multiple += '<div class="form-group col-lg-6"><label for="chose_multiple_' + questions + '_' + count_multiple + '_' + yx + '">Choice ' + (i + 1) + '</label><div class="input-group"><span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + count_multiple + '_' + yx + '" value="1"  ' + (value.answer === 1 ? 'checked="checked"' : '') + ' name="chose_question_answer[' + questions + '][' + i + ']"> <label for="chose_multiple_' + questions + '_' + count_multiple + '_' + yx + '"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][' + questions + '][' + i + ']" value="' + value.name + '"  id="chose_multiple_' + questions + '_' + count_multiple + '_' + yx + '" placeholder="Enter Choice ' + (i + 1) + '">'+english_detail+'</div></div>';
                            count_multiple++;
                            yx++;
                        });
                        // '<div class="form-group col-lg-6"><label for="chose_multiple_' + questions + '_' + count_multiple + '_2">Choice 2</label><div class="input-group"><span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + count_multiple + '_2" value="1" name="chose_question_answer[1][1]"> <label for="chose_multiple_' + questions + '_' + count_multiple + '_2"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][1][]" id="chose_multiple_' + questions + '_' + count_multiple + '_2" placeholder="Enter Choice 2"></div></div><div class="form-group col-lg-6"><label for="chose_multiple_' + questions + '_' + count_multiple + '_3">Choice 3</label><div class="input-group"><span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + count_multiple + '_3" value="1" name="chose_question_answer[1][2]"> <label for="chose_multiple_' + questions + '_' + count_multiple + '_3"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][1][]" id="chose_multiple_' + questions + '_' + count_multiple + '_3" placeholder="Enter Choice 3"></div></div><div class="form-group col-lg-6"><label for="chose_multiple_' + questions + '_' + count_multiple + '_4">Choice 4</label><div class="input-group"><span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + count_multiple + '_4" value="1" name="chose_question_answer[1][3]"> <label for="chose_multiple_' + questions + '_' + count_multiple + '_4"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][1][]" id="cchose_multiple_' + questions + '_' + count_multiple + '_4" placeholder="Enter Choice 4"></div></div>';
                        multiple += '</div>';
                        $(wrapper_times).append(multiple);
                    }else if (question_type === 'chose_single_with_images') {
                        single_question_with_images = '<div>';
                        single_question_with_images += '<div class="form-group col-lg-7"><label for="questions_name_' + questions + '">Question</label><input type="text" class="form-control" name="questions[name]['+questions+']" value="' + question_name + '" id="questions_name_' + questions + '" placeholder="Enter Question">'+english_question+'<input type="hidden" name="questions[type]['+questions+']" value="' + question_type + '"></div><div class="form-group col-lg-3"><input type="file" style="width: 100%;margin-top: 30px;" name="image[' + questions + ']"></div><button class="btn btn-danger col-lg-2 remove_question" data-type="' + question_type + '" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button>';
                        $.each(data.curriculum_questions_details, function (i, value) {
                            var image_path='{{assetURL()}}' + value.image;
                            single_question_with_images += '<div class="form-group col-lg-6"><label for="chose_single_with_images_' + questions + '_' + count + '_' + yx + '">Choice ' + (i + 1) + '</label><div class="input-group"><span class="input-group-addon chose"><input type="radio" id="chose_single_with_images_' + questions + '_' + count + '_' + yx + '" value="' + value.answer + '"  ' + (value.answer === 1 ? 'checked="checked"' : '') + '  name="chose_question_answer[' + questions + ']"><label for="chose_single_with_images_' + questions + '_' + count + '_' + yx + '"><span></span></label></span><img style="width:150px;" src="'+image_path+'"><input type="hidden" class="form-control" name="questions[images_answers]['+questions+'][]" value="'+value.image+'" id="chose_single_with_image_'+questions+'_'+ count_single_with_images +'_'+yx+'"><span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a></span></div></div>';
                            count_single_with_images++;
                            yx++
                        });
                        single_question_with_images += '</div>';
                        $(wrapper_times).append(single_question_with_images);
                    } else if (question_type === 'chose_multiple_with_images') {
                        multiple_question_with_images = '<div class="">';
                        multiple_question_with_images += '<div class="form-group col-lg-7"><label for="questions_name_' + questions + '">Question</label> <input type="text" class="form-control" value="' + question_name + '" name="questions[name][' + questions + ']" id="questions_name_' + questions + '" placeholder="Enter Question">'+english_question+'<input type="hidden" name="questions[type][' + questions + ']" value="' + question_type + '"></div><div class="form-group col-lg-3"><input type="file" style="width: 100%;margin-top: 30px;" name="image[' + questions + ']"></div><button class="btn btn-danger col-lg-2 remove_question" data-type="' + question_type + '" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button>';
                        $.each(data.curriculum_questions_details, function (i, value) {
                            var multiple_image_path='{{assetURL()}}' + value.image;
                            multiple_question_with_images += '<div class="form-group col-lg-6"><label for="chose_multiple_with_images_' + questions + '_' + count_multiple_with_images + '_' + yx + '">Choice ' + (i + 1) + '</label><div class="input-group"><span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_with_images_' + questions + '_' + count_multiple_with_images + '_' + yx + '" value="1"  ' + (value.answer === 1 ? 'checked="checked"' : '') + ' name="chose_question_answer[' + questions + '][' + i + ']"> <label for="chose_multiple_with_images_' + questions + '_' + count_multiple_with_images + '_' + yx + '"> <span></span> </label> </span> <img style="width:150px;" src="'+multiple_image_path+'"><input type="hidden" class="form-control" name="questions[images_answers][{{ $x }}][]" value="'+value.image+'" id="chose_multiple_with_image_'+questions+'_'+count_multiple_with_images+'_'+yx+'"></div></div>';
                            count_multiple_with_images++;
                            yx++;
                        });
                        // '<div class="form-group col-lg-6"><label for="chose_multiple_' + questions + '_' + count_multiple + '_2">Choice 2</label><div class="input-group"><span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + count_multiple + '_2" value="1" name="chose_question_answer[1][1]"> <label for="chose_multiple_' + questions + '_' + count_multiple + '_2"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][1][]" id="chose_multiple_' + questions + '_' + count_multiple + '_2" placeholder="Enter Choice 2"></div></div><div class="form-group col-lg-6"><label for="chose_multiple_' + questions + '_' + count_multiple + '_3">Choice 3</label><div class="input-group"><span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + count_multiple + '_3" value="1" name="chose_question_answer[1][2]"> <label for="chose_multiple_' + questions + '_' + count_multiple + '_3"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][1][]" id="chose_multiple_' + questions + '_' + count_multiple + '_3" placeholder="Enter Choice 3"></div></div><div class="form-group col-lg-6"><label for="chose_multiple_' + questions + '_' + count_multiple + '_4">Choice 4</label><div class="input-group"><span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + count_multiple + '_4" value="1" name="chose_question_answer[1][3]"> <label for="chose_multiple_' + questions + '_' + count_multiple + '_4"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][1][]" id="cchose_multiple_' + questions + '_' + count_multiple + '_4" placeholder="Enter Choice 4"></div></div>';
                        multiple_question_with_images += '</div>';
                        $(wrapper_times).append(multiple_question_with_images);
                    }
                    questions++;
                    $('#closeModal').click();
                    course_curriculum.empty();
                    question_area.hide();
                    $('.checkAllQuestion').hide();
                    question_area.empty().append('<h5 style="margin-bottom: 20px; font-weight: bold;text-transform: capitalize">select questions as you want</h5>');
                });
                return false;
            });


            $('#searchQuestion').on('keyup', function () {
                $("#checkAllQuestion").prop('checked', false);
                console.log(course_curriculum.val());
                var searchInput = $(this).val();
                $.ajax({
                    url: "{{URL("admin/search_question")}}",
                    type: "GET",
                    data: {_token: token, contains: searchInput, curriculum_id: course_curriculum.val()},
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        if (data != 'No search result') {
                            console.log(data);
                            question_area.empty().append('<h5 style="margin-bottom: 20px; font-weight: bold;text-transform: capitalize">select questions as you want</h5>');
                            $.each(data, function (key, value2) {
                                console.log(key);
                                console.log(value2.curriculum_questions_details);
                                if (value2.type === 'true_false') {
                                    question_area.show().append('<div class="md-checkbox"><input data-json=\'' + JSON.stringify(value2) + '\' name="selectedQuestions"  data-type="' + value2.type + '" data-name="' + value2.name + '" type="checkbox" id="checkbox-' + value2.id + '" value="' + value2.id + '" class="md-check all"><label for="checkbox-' + value2.id + '"><span class="inc"></span><span class="check"></span><span class="box"></span> ' + value2.name + '</label><i style="color: #f0ad4e; margin-left: 5px;" class="fa fa-check"></i> </div>');
                                } else if (value2.type === 'chose_single') {
                                    question_area.show().append('<div class="md-checkbox"><input data-json=\'' + JSON.stringify(value2) + '\' name="selectedQuestions" data-type="' + value2.type + '" data-name="' + value2.name + '" type="checkbox" id="checkbox-' + value2.id + '" value="' + value2.id + '" class="md-check all"><label for="checkbox-' + value2.id + '"><span class="inc"></span><span class="check"></span><span class="box"></span> ' + value2.name + '</label><i style="color: #337ab7; margin-left: 5px;" class="fa fa-check-square-o"></i> </div>');
                                } else if (value2.type === 'chose_multiple') {
                                    question_area.show().append('<div class="md-checkbox"><input data-json=\'' + JSON.stringify(value2) + '\' name="selectedQuestions"  data-type="' + value2.type + '" data-name="' + value2.name + '" type="checkbox" id="checkbox-' + value2.id + '" value="' + value2.id + '" class="md-check all"><label for="checkbox-' + value2.id + '"><span class="inc"></span><span class="check"></span><span class="box"></span> ' + value2.name + '</label><i style="color: #ed6b75; margin-left: 5px;" class="fa fa-check-square-o"></i><i style="color: #ed6b75; margin-left: 5px;" class="fa fa-check-square-o"></i> </div>');
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