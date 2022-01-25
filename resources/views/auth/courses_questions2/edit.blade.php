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

        .questions-area .overlay,#overlay2 {
            position: absolute;
            left: 50%;
            background: rgba(0, 0, 0, .3);
            width: 100px;
            height: 67px;
            border-radius: 10px !important;
            transform: translate(-50%, -50%);
            z-index: 9999999999;
            box-shadow: 0 0 11px 0px #999;
        }

        .questions-area .overlay {
            top: 50%;
        }

        #overlay2{
            z-index: 99999999999;
            top: 70%;
        }
        .overlay i,#overlay2 i {
            line-height: 1.5;
            margin: auto;
            left: 20%;
            position: absolute;
        }
        .overlay i{
            top: 25px;
        }
        .checkAllQuestion span {
            margin-right: 10px;
            font-weight: bold;
        }

        .checkAllQuestion span i {
            font-weight: normal;
        }
        .remove-img{
            display: block;
        }
        .input-group .image-link{
            display: block;
            border: #ccc 1px solid;
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
                <a href="{{ URL('/admin/courses_questions2') }}">{{ Lang::get('main.courses_questions') }}</a>
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
                {!! Form::open(['url'=>'admin/import_course_questions2']) !!}
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
                    <div id="overlay2" class="hidden">
                        <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                        <span class="sr-only">{{ Lang::get('main.loading') }}</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeModal" data-dismiss="modal">{{ Lang::get('main.cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="importSelected">Import selected</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
@endsection
@section('content')
    <style>
        .add-btn{
            position: fixed;
            right: -116px;
            top: 75px;
            z-index: 10040;
            transition: right 0.5s;
        }
        .add-btn i{
            margin-right: 10px;
        }
        #demo ul li{
            padding:25px;
            text-align: center;
        }
        .add-btn:hover{
            right:0px;
        }
    </style>

    <a class="add-btn btn btn-primary btn-sm" data-toggle="modal" href="#demo"><i class="glyphicon glyphicon-plus"></i>Add New Question</a>
    <div id="demo" class="modal fade modal-scroll" tabindex="-1" data-replace="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Add New Question</h4>
                </div>
                <div class="modal-body">
                    <ul class="list-group q-list">
                        <li class="list-group-item"><button data-dismiss="modal" class="btn btn-default btn-m addNewQuestionAjax" type="button" data-type="true_false">True & False</button></li>
                        <li class="list-group-item"><button data-dismiss="modal" class="btn btn-default btn-m addNewQuestionAjax" type="button" data-type="chose_single">Chose Answer</button></li>
                        <li class="list-group-item"><button data-dismiss="modal" class="btn btn-default btn-m addNewQuestionAjax" type="button" data-type="chose_multiple">Chose Answers</button></li>
                        <li class="list-group-item"><button data-dismiss="modal" class="btn btn-default btn-m addNewQuestionAjax" type="button" data-type="chose_single_with_images">Chose Answer With Images</button></li>
                        <li class="list-group-item"><button data-dismiss="modal" class="btn btn-default btn-m addNewQuestionAjax" type="button" data-type="chose_multiple_with_images">Chose Answers With Images</button></li>
                    </ul>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">{{ Lang::get('main.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="image-modal" class="modal fade modal-scroll" tabindex="-1" data-replace="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <img width="100%"/>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn dark btn-outline">{{ Lang::get('main.close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                {!! Form::open(['method'=>'PUT','url'=>'admin/courses_questions2/'.$course_question->id,'id'=>'addmodules_questionsEditForm','files'=>true]) !!}
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
                    <div id="questionsContent">
                        @if(count($courses_questions))

                            <?php
                            //$x = 0;
                            $yx = 1;
                            ?>
                            @foreach($courses_questions as $question)
                                @if($question->type=='true_false' && (count($question->CurriculumQuestionsDetails) || isset(old('questions')['answers'][$question->id])))
                                    <div class="row">
                                        <div class="col-lg-10">
                                            <div class="form-group col-lg-8">
                                                <label for="questions_name_{{ $question->id }}">{{ Lang::get('main.question') }}</label>
                                                <input type="text" class="form-control" name="questions[name][{{$question->id}}]"
                                                       value="{{ $question->name }}" id="questions_name_{{ $question->id }}"
                                                       placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">
                                                @if($course_question->questions_type=='arabic_and_english')
                                                    <input type="text" class="form-control" name="questions[name_en][{{$question->id}}]"
                                                           value="{{ $question->name_en }}" id="questions_name_{{ $question->id }}"
                                                           placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                                @endif
                                            </div>

                                            <div class="form-group col-lg-2">

                                                <div style=" border: 1px solid whitesmoke ;text-align: center;position: relative" >
                                                    <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height: 34px; width: auto;" src="{{$question->image?assetURL('exams_question/'.$question->image):assetURL('none.png') }}" alt="" width="100%" /></a>
                                                    <button class="btn btn-primary" title="Change" onclick="javascript:changeImage(this)" type="button"  style="padding:0px; width:100%;">
                                                        <i class="glyphicon glyphicon-edit"></i>
                                                    </button>
                                                    <a class="remove-img {{ !$question->image?'hidden':'' }}" title="Remove" onClick="javascript:removeFile(this)" style="color: red;text-decoration: none; position: absolute; right: 0px; top: 0px;">
                                                        <i class="glyphicon glyphicon-remove"></i>

                                                    </a>


                                                    <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw" style="position: absolute;left: 25%;top: 35%;display: none"></i>
                                                </div>
                                                <input type="file" data-image_id="{{ $question->id }}" name="image[{{ $question->id }}]" onchange="doChangeImage(this)" style="display: none">

                                            </div>

                                            <div class="col-lg-2" style="margin-top: 24px;">

                                                <div class="true">
                                                    <input type="radio" name="questions[answers][{{ $question->id }}]"
                                                           @if((isset($question->CurriculumQuestionsDetails->answer) && $question->CurriculumQuestionsDetails->answer == 1) || (isset(old('questions')['answers'][ $question->id ]) && old('questions')['answers'][ $question->id ] == 1) ) checked="checked"
                                                           @endif value="1" id="questions_answers_{{ $question->id }}_true">
                                                    <label for="questions_answers_{{ $question->id }}_true">
                                                        <span></span>
                                                    </label>
                                                </div>

                                                <div class="false">
                                                    <input type="radio" name="questions[answers][{{ $question->id }}]"
                                                           @if((isset($question->CurriculumQuestionsDetails->answer) && $question->CurriculumQuestionsDetails->answer == 0) || (isset(old('questions')['answers'][ $question->id ]) && old('questions')['answers'][ $question->id ] == 0) )  checked="checked"
                                                           @endif value="0" id="questions_answers_{{ $question->id }}_false">
                                                    <label for="questions_answers_{{ $question->id }}_false">

<span>
</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-danger col-lg-2 remove_question" data-id="{{ $question->id  }}" data-type="true_false"
                                                style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i>
                                        </button>
                                    </div>
                                @elseif($question->type=='chose_single' && (count($question->CurriculumQuestionsDetails) || isset(old('questions')['answers'][$question->id])))
                                    <div>
                                        <div class="form-group col-lg-8">
                                            <label for="questions_name_{{ $question->id }}">{{ Lang::get('main.question') }}
                                            </label>
                                            <input type="text" class="form-control" name="questions[name][{{$question->id}}]"
                                                   value="{{ $question->name }}" id="questions_name_{{ $question->id }}"
                                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">
                                            @if($course_question->questions_type=='arabic_and_english')
                                                <input type="text" class="form-control" name="questions[name_en][{{$question->id}}]"
                                                       value="{{ $question->name_en }}" id="questions_name_{{ $question->id }}"
                                                       placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                            @endif
                                        </div>
                                        <div class="form-group col-lg-2">

                                            <div style=" border: 1px solid whitesmoke ;text-align: center;position: relative" >
                                                <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height: 34px; width: auto;" src="{{$question->image? assetURL('exams_question/'.$question->image):assetURL('none.png') }}" alt="" width="100%" /></a>
                                                <button class="btn btn-primary" title="Change" onclick="javascript:changeImage(this)" type="button"  type="button" style="width:100%; padding:0px;">
                                                    <i class="glyphicon glyphicon-edit"></i>
                                                </button>
                                                <a class="remove-img {{ !$question->image?'hidden':'' }}" title="Remove" onClick="javascript:removeFile(this)" style="color: red;text-decoration: none; position: absolute; right: 0px; top: 0px;">
                                                    <i class="glyphicon glyphicon-remove"></i>

                                                </a>

                                                <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw" style="position: absolute;left: 25%;top: 35%;display: none"></i>
                                            </div>
                                            <input type="file" data-image_id="{{ $question->id }}" name="image[{{ $question->id }}]" onchange="doChangeImage(this)" style="display: none">

                                        </div>
                                        <button class="btn btn-danger col-lg-2 remove_question" data-id="{{ $question->id  }}" data-type="chose_single"
                                                style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i>
                                        </button>
                                        <?php $yx = 1;?>
                                        @if(count($question->CurriculumQuestionsDetails))
                                            @foreach($question->CurriculumQuestionsDetails as $detail)
                                                <div class="form-group col-lg-6">
                                                    <label for="chose_single_{{ $question->id }}_{{ $yx }}">{{ Lang::get('main.choice').' '.$yx }}
                                                    </label>
                                                    <div class="input-group">

        <span class="input-group-addon chose">
        <input type="radio" id="chose_single_{{ $question->id }}_{{ $yx }}" @if(isset($detail->answer) && $detail->answer) checked="checked"
               @endif value="{{ $yx }}" name="chose_question_answer[{{ $question->id }}]">
        <label for="chose_single_{{ $question->id }}_{{ $yx }}">

        <span>
        </span>
        </label>
        </span>
                                                        <input type="text" class="form-control"
                                                               name="questions[answers][{{ $question->id }}][]"
                                                               value="{{ $detail->name }}"
                                                               id="chose_single_{{ $question->id }}_{{ $yx }}"
                                                               placeholder="{{ Lang::get('main.enter').Lang::get('main.choice').' '.$yx }}">

                                                        @if($course_question->questions_type=='arabic_and_english')
                                                        <input type="text" class="form-control"
                                                               name="questions_en[answers][{{ $question->id }}][]"
                                                               value="{{ $detail->name_en }}"
                                                               id="chose_single_{{ $question->id }}_{{ $yx }}"
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
                                        @elseif(isset(old('questions')['answers'][$question->id]))
                                            @foreach(old('questions')['answers'][$question->id] as $key=>$detail)
                                                <div class="form-group col-lg-6">
                                                    <label for="chose_single_{{ $question->id }}_{{ $yx }}">{{ Lang::get('main.choice').' '.$yx }}
                                                    </label>
                                                    <div class="input-group">

        <span class="input-group-addon chose">
        <input type="radio" id="chose_single_{{ $question->id }}_{{ $yx }}" @if(isset(old('chose_question_answer')[ $question->id]) && old('chose_question_answer')[ $question->id]==$yx) checked="checked"
               @endif value="{{ $yx }}" name="chose_question_answer[{{ $question->id }}]">
        <label for="chose_single_{{ $question->id }}_{{ $yx }}">

        <span>
        </span>
        </label>
        </span>
                                                        <input type="text" class="form-control"
                                                               name="questions[answers][{{ $question->id }}][]"
                                                               value="{{ $detail }}"
                                                               id="chose_single_{{ $question->id }}_{{ $yx }}"
                                                               placeholder="{{ Lang::get('main.enter').Lang::get('main.choice').' '.$yx }}">

                                                        @if($course_question->questions_type=='arabic_and_english')
                                                            <input type="text" class="form-control"
                                                                   name="questions_en[answers][{{ $question->id }}][]"
                                                                   value="{{ old('questions_en')['answers'][$question->id][$key] }}"
                                                                   id="chose_single_{{ $question->id }}_{{ $yx }}"
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
                                        @endif
                                    </div>
                                @elseif($question->type=='chose_multiple' && (count($question->CurriculumQuestionsDetails) || isset(old('questions')['answers'][$question->id])))
                                    <div>
                                        <div class="form-group col-lg-8">
                                            <label for="questions_name_{{ $question->id }}">{{ Lang::get('main.question') }}
                                            </label>
                                            <input type="text" class="form-control" name="questions[name][{{$question->id}}]"
                                                   value="{{ $question->name }}" id="questions_name_{{ $question->id }}"
                                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">
                                            @if($course_question->questions_type=='arabic_and_english')
                                                <input type="text" class="form-control" name="questions[name_en][{{$question->id}}]"
                                                       value="{{ $question->name_en }}" id="questions_name_{{ $question->id }}"
                                                       placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                            @endif
                                        </div>
                                        <div class="form-group col-lg-2">

                                            <div style=" border: 1px solid whitesmoke ;text-align: center;position: relative" >
                                                <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height: 34px; width: auto;" src="{{$question->image? assetURL('exams_question/'.$question->image):assetURL('none.png') }}" alt="" width="100%" /></a>
                                                <button class="btn btn-primary" title="Change" onclick="javascript:changeImage(this)" type="button"  style="width:100%; padding:0px;">
                                                    <i class="glyphicon glyphicon-edit"></i>
                                                </button>
                                                <a class="remove-img {{ !$question->image?'hidden':'' }}" title="Remove" onClick="javascript:removeFile(this)" style="color: red;text-decoration: none; position: absolute; right: 0px; top: 0px;">
                                                    <i class="glyphicon glyphicon-remove"></i>

                                                </a>

                                                <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw" style="position: absolute;left: 25%;top: 35%;display: none"></i>
                                            </div>
                                            <input type="file" data-image_id="{{ $question->id }}" name="image[{{ $question->id }}]" onchange="doChangeImage(this)" style="display: none">
                                        </div>
                                        <button class="btn btn-danger col-lg-2 remove_question"
                                                data-type="chose_multiple" data-id="{{ $question->id  }}" style="margin-top: 24px;"><i
                                                    class="glyphicon glyphicon-trash"></i></button>
                                        <?php $yx = 1;?>
                                        @if(count($question->CurriculumQuestionsDetails))
                                            @foreach($question->CurriculumQuestionsDetails as $detail)
                                                <div class="form-group col-lg-6">
                                                    <label for="chose_multiple_{{ $question->id }}_{{ $yx }}">{{ Lang::get('main.choice').' '.$yx }}
                                                    </label>
                                                    <div class="input-group">
                                                    <span class="input-group-addon chose">
                                                    <input type="checkbox"
                                                           id="chose_multiple_{{ $question->id }}_{{ $yx }}"
                                                           @if(isset($detail->answer) && $detail->answer)  checked="checked" @endif value="1"
                                                           name="chose_question_answer[{{ $question->id }}][{{ $yx-1 }}]">
                                                    <label for="chose_multiple_{{ $question->id }}_{{ $yx }}">

                                                    <span>
                                                    </span>
                                                    </label>
                                                    </span>
                                                        <input type="text" class="form-control"
                                                               name="questions[answers][{{ $question->id }}][]"
                                                               value="{{ $detail->name }}"
                                                               id="chose_multiple_{{ $question->id }}_{{ $yx-1 }}"
                                                               placeholder="{{ Lang::get('main.enter').Lang::get('main.choice').' '.$yx }}">

                                                        @if($course_question->questions_type=='arabic_and_english')
                                                            <input type="text" class="form-control"
                                                                   name="questions_en[answers][{{ $question->id }}][]"
                                                                   value="{{ $detail->name_en }}"
                                                                   id="chose_multiple_{{ $question->id }}_{{ $yx-1 }}"
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
                                        @elseif(isset(old('questions')['answers'][$question->id]))
                                            @foreach(old('questions')['answers'][$question->id] as $key=>$detail)
                                                <div class="form-group col-lg-6">
                                                    <label for="chose_multiple_{{ $question->id }}_{{ $yx }}">{{ Lang::get('main.choice').' '.$yx }}
                                                    </label>
                                                    <div class="input-group">
                                                    <span class="input-group-addon chose">
                                                    <input type="checkbox"
                                                           id="chose_multiple_{{ $question->id }}_{{ $yx }}"
                                                           @if(isset(old('chose_question_answer')[ $question->id][ $yx-1 ]) && old('chose_question_answer')[ $question->id][ $yx-1 ]) checked="checked"
                                                           @endif value="1"
                                                           name="chose_question_answer[{{ $question->id }}][{{ $yx-1 }}]">
                                                    <label for="chose_multiple_{{ $question->id }}_{{ $yx }}">

                                                    <span>
                                                    </span>
                                                    </label>
                                                    </span>
                                                        <input type="text" class="form-control"
                                                               name="questions[answers][{{ $question->id }}][]"
                                                               value="{{ $detail }}"
                                                               id="chose_multiple_{{ $question->id }}_{{ $yx-1 }}"
                                                               placeholder="{{ Lang::get('main.enter').Lang::get('main.choice').' '.$yx }}">
                                                        @if($course_question->questions_type=='arabic_and_english')
                                                            <input type="text" class="form-control"
                                                                   name="questions_en[answers][{{ $question->id }}][]"
                                                                   value="{{ old('questions_en')['answers'][$question->id][$key] }}"
                                                                   id="chose_multiple_{{ $question->id }}_{{ $yx-1 }}"
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
                                        @endif
                                    </div>
                                @elseif($question->type=='chose_single_with_images' && (count($question->CurriculumQuestionsDetails) || isset(old('questions')['answers_e'][$question->id])))
                                    <div>
                                        <div class="form-group col-lg-8">
                                            <label for="questions_name_{{ $question->id }}">{{ Lang::get('main.question') }}
                                            </label>
                                            <input type="text" class="form-control" name="questions[name][{{$question->id}}]"
                                                   value="{{ $question->name }}" id="questions_name_{{ $question->id }}"
                                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">
                                            @if($course_question->questions_type=='arabic_and_english')
                                                <input type="text" class="form-control" name="questions[name_en][{{$question->id}}]"
                                                       value="{{ $question->name_en }}" id="questions_name_{{ $question->id }}"
                                                       placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                            @endif
                                        </div>
                                        <div class="form-group col-lg-2">
                                            <div style=" border: 1px solid whitesmoke ;text-align: center;position: relative" >
                                                <a class="image-link" href="#image-modal" data-toggle="modal"> <img style="max-height: 34px; width: auto;" src="{{$question->image? assetURL('exams_question/'.$question->image):assetURL('none.png') }}" alt="" width="100%" /></a>
                                                <button class="btn btn-primary" title="Change" onclick="javascript:changeImage(this)" type="button"  style="width:100%; padding:0px;">
                                                    <i class="glyphicon glyphicon-edit"></i>
                                                </button>
                                                <a class="remove-img {{ !$question->image?'hidden':'' }}" title="Remove" onClick="javascript:removeFile(this)" style="color: red;text-decoration: none; position: absolute; right: 0px; top: 0px;">
                                                    <i class="glyphicon glyphicon-remove"></i>

                                                </a>

                                                <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw" style="position: absolute;left: 25%;top: 35%;display: none"></i>
                                            </div>
                                            <input type="file" data-image_id="{{ $question->id }}" name="image[{{ $question->id }}]" onchange="doChangeImage(this)" style="display: none">

                                        </div>
                                        <button class="btn btn-danger col-lg-2 remove_question" data-id="{{ $question->id }}" data-type="chose_single_with_images"
                                                style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i>
                                        </button>
                                        <?php $yx = 1;?>
                                        @if(count($question->CurriculumQuestionsDetails))
                                            @foreach($question->CurriculumQuestionsDetails as $detail)
                                                <div class="form-group col-lg-6">
                                                    <label for="chose_single_with_images_{{ $question->id }}_{{ $yx }}">{{ Lang::get('main.choice').' '.$yx }}
                                                    </label>
                                                    <div class="input-group">

<span class="input-group-addon chose">
<input type="radio" id="chose_single_with_images_{{ $question->id }}_{{ $yx }}" @if(isset($detail->answer) && $detail->answer) checked="checked"
       @endif value="{{ $yx }}" name="chose_question_answer[{{ $question->id }}]">
<label for="chose_single_with_images_{{ $question->id }}_{{ $yx }}">

<span>
</span>
</label>
</span>


                                                        <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height:34px; width:auto;" src="{{assetURL($detail->image) }}"></a>
                                                        <input type="hidden" class="form-control" name="questions[images_answers][{{ $question->id }}][]" value="{{ $detail->image }}" id="chose_single_with_image_{{ $question->id }}_{{ $yx }}">
                                                        <span class="input-group-addon "> <a href="#"
                                                                                             class="removeAnswer"><i
                                                                        class="glyphicon glyphicon-trash"></i></a>
</span>
                                                    </div>
                                                </div>
                                                <?php $yx++;?>
                                            @endforeach
                                        @elseif(isset(old('questions')['answers_e'][$question->id]))
                                            @foreach(old('questions')['answers_e'][$question->id] as $detail)
                                                <div class="form-group col-lg-6">
                                                    <label for="chose_single_with_images_{{ $question->id }}_{{ $yx }}">{{ Lang::get('main.choice').' '.$yx }}
                                                    </label>
                                                    <div class="input-group">

<span class="input-group-addon chose">
<input type="radio" id="chose_single_with_images_{{ $question->id }}_{{ $yx }}" @if(isset(old('chose_question_answer')[ $question->id]) && old('chose_question_answer')[ $question->id]) checked="checked"
       @endif value="{{ $yx }}" name="chose_question_answer[{{ $question->id }}]">
<label for="chose_single_with_images_{{ $question->id }}_{{ $yx }}">

<span>
</span>
</label>
</span>

                                                        <input type="file" class="form-control" name="questions[answers][{{$question->id}}][]" id="chose_single_with_images_{{$question->id}}_{{$yx}}" placeholder="Enter Choice {{$yx}}">
                                                        <input type="hidden" class="form-control" name="questions[answers_e][{{$question->id}}][]">
                                                        <span class="input-group-addon "> <a href="#"
                                                                                             class="removeAnswer"><i
                                                                        class="glyphicon glyphicon-trash"></i></a>
</span>
                                                    </div>
                                                </div>
                                                <?php $yx++;?>
                                            @endforeach
                                        @endif
                                    </div>
                                @elseif($question->type=='chose_multiple_with_images' && (count($question->CurriculumQuestionsDetails) || isset(old('questions')['answers_e'][$question->id])))
                                    <div>
                                        <div class="form-group col-lg-8">
                                            <label for="questions_name_{{ $question->id }}">{{ Lang::get('main.question') }}
                                            </label>
                                            <input type="text" class="form-control" name="questions[name][{{$question->id}}]"
                                                   value="{{ $question->name }}" id="questions_name_{{ $question->id }}"
                                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">
                                            @if($course_question->questions_type=='arabic_and_english')
                                                <input type="text" class="form-control" name="questions[name_en][{{$question->id}}]"
                                                       value="{{ $question->name_en }}" id="questions_name_{{ $question->id }}"
                                                       placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                            @endif
                                        </div>
                                        <div class="form-group col-lg-2">

                                            <div style=" border: 1px solid whitesmoke ;text-align: center;position: relative" >
                                                <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height: 34px; width: auto;" src="{{$question->image? assetURL('exams_question/'.$question->image):assetURL('none.png') }}" alt="" width="100%" /></a>
                                                <button class="btn btn-primary" title="Change" onclick="javascript:changeImage(this)" type="button"  style="width:100%; padding:0px;">
                                                    <i class="glyphicon glyphicon-edit"></i>
                                                </button>
                                                <a class="remove-img {{ !$question->image?'hidden':'' }}" title="Remove" onClick="javascript:removeFile(this)" style="color: red;text-decoration: none; position: absolute; right: 0px; top: 0px;">
                                                    <i class="glyphicon glyphicon-remove"></i>

                                                </a>

                                                <i id="loading" class="fa fa-spinner fa-spin fa-3x fa-fw" style="position: absolute;left: 25%;top: 35%;display: none"></i>
                                            </div>
                                            <input type="file" data-image_id="{{ $question->id }}" name="image[{{ $question->id }}]" onchange="doChangeImage(this)" style="display: none">

                                        </div>
                                        <button class="btn btn-danger col-lg-2 remove_question" data-id="{{ $question->id }}"
                                                data-type="chose_multiple_with_images" style="margin-top: 24px;"><i
                                                    class="glyphicon glyphicon-trash"></i></button>
                                        <?php $yx = 1;?>
                                        @if(count($question->CurriculumQuestionsDetails))
                                            @foreach($question->CurriculumQuestionsDetails as $detail)
                                                <div class="form-group col-lg-6">
                                                    <label for="chose_multiple_with_images_{{ $question->id }}_{{ $yx }}">{{ Lang::get('main.choice').' '.$yx }}
                                                    </label>
                                                    <div class="input-group">
                                                    <span class="input-group-addon chose">
                                                    <input type="checkbox"
                                                           id="chose_multiple_with_images_{{ $question->id }}_{{ $yx }}"
                                                           @if(isset($detail->answer) && $detail->answer)  checked="checked" @endif value="1"
                                                           name="chose_question_answer[{{ $question->id }}][{{ $yx-1 }}]">
                                                    <label for="chose_multiple_with_images_{{ $question->id }}_{{ $yx }}">

                                                    <span>
                                                    </span>
                                                    </label>
                                                    </span>
                                                        <a class="image-link" href="#image-modal" data-toggle="modal"><img style="max-height:34px; width:auto;" src="{{assetURL($detail->image) }}"></a>
                                                        <input type="hidden" class="form-control" name="questions[images_answers][{{ $question->id }}][]" value="{{ $detail->image }}" id="chose_multiple_with_images_{{ $question->id }}_{{ $yx-1 }}" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_'.$yx) }}">
                                                        <span class="input-group-addon "> <a href="#"
                                                                                             class="removeAnswer"><i
                                                                        class="glyphicon glyphicon-trash"></i></a>
</span>
                                                    </div>
                                                </div>
                                                <?php $yx++;?>
                                            @endforeach
                                        @elseif(isset(old('questions')['answers_e'][$question->id]))
                                            @foreach(old('questions')['answers_e'][$question->id] as $detail)
                                                <div class="form-group col-lg-6">
                                                    <label for="chose_multiple_with_images_{{ $question->id }}_{{ $yx }}">{{ Lang::get('main.choice').' '.$yx }}
                                                    </label>
                                                    <div class="input-group">
                                                    <span class="input-group-addon chose">
                                                    <input type="checkbox"
                                                           id="chose_multiple_with_images_{{ $question->id }}_{{ $yx }}"
                                                           @if(isset(old('chose_question_answer')[ $question->id][ $yx-1]) && old('chose_question_answer')[ $question->id][ $yx-1])  checked="checked" @endif value="1"
                                                           name="chose_question_answer[{{ $question->id }}][{{ $yx-1 }}]">
                                                    <label for="chose_multiple_with_images_{{ $question->id }}_{{ $yx }}">

                                                    <span>
                                                    </span>
                                                    </label>
                                                    </span>
                                                        <input type="file" class="form-control" name="questions[answers][{{$question->id}}][]" id="chose_multiple_with_images_{{$question->id}}_{{$yx}}" placeholder="Enter Choice {{$yx}}">
                                                        <input type="hidden" class="form-control" name="questions[answers_e][{{$question->id}}][]">
                                                        <span class="input-group-addon "> <a href="#"
                                                                                             class="removeAnswer"><i
                                                                        class="glyphicon glyphicon-trash"></i></a>
</span>
                                                    </div>
                                                </div>
                                                <?php $yx++;?>
                                            @endforeach
                                        @endif

                                    </div>
                                @endif
                                <?php //$x++?>
                            @endforeach
                        @endif
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
        var questions = 0;
        $(document).ready(function () {
            $(".sel2").select2();
            var token = '{{ csrf_token() }}';
            var wrapper_times = $("#questionsContent"); //title Fields wrapper
            $(document).on('click', '.removeAnswer', function (e) {
                e.preventDefault();
                $(this).parent().parent().parent().remove();
            });
            $(document).on('click', '.addNewQuestionAjax', function (e) { //on add input button click
                e.preventDefault();
                type = $(this).data('type');
                curriculum = {{ $course_question->id }};
                questions_type = "{{ $course_question->questions_type }}";
                //$('#demo').toggleClass('in');
                $.ajax({
                    url: "{{URL("admin/add_new_question")}}",
                    type: "POST",
                    data: {_token: token,type: type,curriculum: curriculum,questions_type: questions_type},
                    dataType: "json",
                    success: function (data) {
                        $(wrapper_times).append(data);
                    }
                });

            });
            $(wrapper_times).on("click", ".remove_question", function (e) { //user click on remove text
                e.preventDefault();
                id = $(this).data("id");
                $.ajax({
                    url: "{{URL("admin/remove_question")}}",
                    type: "POST",
                    data: {_token: token,id: id},
                    dataType: "json",
                    success: function (data) {
                    }
                });
                $(this).parent('div').remove();
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
                        url: "{{URL("admin/get_course_curriculum2")}}/" + courseID,
                        type: "GET",
                        data: {_token: token},
                        dataType: "json",
                        success: function (data) {
                            console.log(data.id);
                            console.log(data.description);
                            if (data) {
                                course_curriculum.empty();
                                question_area.empty();
                                course_curriculum.focus();
                                course_curriculum.append('<option value="">-- Select Curriculum --</option>');
                                $.each(data, function (key, value) {
                                    console.log(value.id);
                                    console.log(value.description);
                                    $('select[name="course_curriculum"]').append('<option value="' + value.id + '">' + value.description + '  ' + value.createdtime + '</option>');
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
                        url: "{{URL("admin/get_curriculum_questions2")}}/" + curriculumID,
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
                $('#overlay2').removeClass('hidden');
                $('#importSelected').attr("disabled", true);
                var data = '';
                var data2 = '';
                var curriculum = {{ $course_question->id }};
                var questions_type = "{{ $course_question->questions_type }}";
                //var id = '';
                var ids=new Array();
                $('#first_opt').prop("selected", true);
                $('#select2-course_id-container').text('-- Select Course --');
                indexs=0;
                length=$('input[name="selectedQuestions"]:checked').length;

                $.each($('input[name="selectedQuestions"]:checked'), function (index) {
                    data = JSON.parse($(this).attr('data-json'));
                    ids.push(data.id);
                });
                $.ajax({
                    url: "{{URL("admin/import_question")}}",
                    type: "POST",
                    data: {_token: token,ids: ids,curriculum: curriculum,questions_type: questions_type},
                    success: function (data2) {
                            $(wrapper_times).append(data2);
                    },
                    complete: function () {
                        $('#closeModal').click();
                        $('#overlay2').addClass('hidden');
                        $('.checkAllQuestion').hide();
                        course_curriculum.empty();
                        question_area.hide();
                        question_area.empty().append('<h5 style="margin-bottom: 20px; font-weight: bold;text-transform: capitalize">select questions as you want</h5>');
                        $('#importSelected').attr("disabled", false);
                    }

                });
                // interval=setInterval(function(){
                //     if(length==indexs){
                //         clearInterval(interval);
                //     }
                // },1000)
                return false;
            });

            $('#searchQuestion').on('keyup', function () {
                $("#checkAllQuestion").prop('checked', false);
                console.log(course_curriculum.val());
                var searchInput = $(this).val();
                $.ajax({
                    url: "{{URL("admin/search_question2")}}",
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

    <script>
        $("[data-toggle='toggle']").click(function() {
            var selector = $(this).data("target");
            $(selector).toggleClass('in');
        });

        function changeImage(obj) {
            //$('input[data-image_id]').click();
            $(obj).parent().next().click();
        }
        function doChangeImage(obj) {
            if ($(obj).val() != '') {
                upload(obj);
            }
        }
        function upload(img) {
            var form_data = new FormData();
            form_data.append('file', img.files[0]);
            form_data.append('_token', '{{csrf_token()}}');
            form_data.append('id', $(img).data("image_id"));
            //$('#loading').css('display', 'block');
            $(img).parent().find('#loading').css('display', 'block');
            $.ajax({
                url: "{{url('admin/question-image-upload')}}",
                data: form_data,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.fail) {
                        $(img).parent().find('#loading').css('display', 'none');
                        alert(data.errors['file']);
                    }
                    else {
                        $(img).parent().find('img').attr('src', '{{ assetURL('exams_question/') }}' + data).parent().next().next().removeClass('hidden').next().css('display', 'none');
                    }
                },
                error: function (xhr, status, error) {
                    $(img).parent().find('#loading').css('display', 'none');
                    alert(xhr.responseText);
                }
            });
        }

        function removeFile(obj) {
            var img = $(obj).parent().next();
            var id = img.data('image_id');
            $(img).parent().find('#loading').css('display', 'block');
            var form_data = new FormData();
            form_data.append('id', id);
            form_data.append('_token', '{{csrf_token()}}');
            $.ajax({
                url: "{{url('admin/remove-question-image')}}",
                data: form_data,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function (data) {
                    $(img).parent().find('img').attr('src', '{{ assetURL('none.png') }}' ).parent().next().removeClass('hidden').next().addClass('hidden').next().css('display', 'none');
                    img.val('');
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
        }

        $(document).on("click", ".image-link", function () {
            $("#image-modal .modal-body img").attr('src', $(this).find('img').attr('src') );
            // As pointed out in comments,
            // it is unnecessary to have to manually call the modal.
            // $('#addBookDialog').modal('show');
        });

        var submitted = false;
        window.onbeforeunload = function(){
            if(!submitted) {
                deleteData();
            }
            return null;
        }
        $("#addmodules_questionsEditForm").submit(function() {
            submitted = true;
        });

        function deleteData() {
            var form_data = new FormData();
            form_data.append('_token', '{{csrf_token()}}');
            $.ajax({
                url: "{{url('admin/delete-unsaved-questions')}}",
                data: form_data,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function (data) {
                },
                error: function (xhr, status, error) {
                }
            });
        }

    </script>

@endsection