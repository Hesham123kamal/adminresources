@extends('auth.layouts.app')
@section('pageTitle')
    <title>{{ Lang::get('main.home_page_title') }}</title>
    <style>
        #report-details{
            font-size: 23px;
        }
        .color1{
            color:#262d49;
        }
        .color2{
            color:#ea4923;
        }
        .bold{
            font-weight: bold;
            font-size: 25px;
        }
        .small{
            font-size: 15px;
        }



        #about-course input[type='checkbox']{
            top:3px;
            right:0px;
            left:auto;
        }
        #about-course .checkbox p{padding-right:20px; }
        input[type="checkbox"] {
            display:none;
        }


        .chose input[type="checkbox"] + label span {
            display:inline-block;
            width:35px;
            height:35px;
            margin:-2px 10px 0 0;
            vertical-align:middle;
            background:url('{{ asset('img/checkbox/checkbox-unchecked.png') }}') center center no-repeat;
            cursor:pointer;
        }

        .chose input[type="checkbox"]:checked + label span {
            background:url('{{ asset('img/checkbox/checkbox-checked.png') }}') center center no-repeat;
        }
        .chose input[type="checkbox"] + label span:hover,.chose input[type="checkbox"] + label:hover span {
            background:url('{{ asset('img/checkbox/checkbox-checked.png') }}') center center no-repeat;
            opacity : 0.5;
        }

        .chose input[type="radio"] + label span {
            display:inline-block;
            width:35px;
            height:35px;
            margin:-2px 10px 0 0;
            vertical-align:middle;
            background:url('{{ asset('img/checkbox/checkbox-unchecked.png') }}') center center no-repeat;
            cursor:pointer;
        }

        .chose input[type="radio"]:checked + label span {
            background:url('{{ asset('img/checkbox/checkbox-checked.png') }}') center center no-repeat;
        }
        .chose input[type="radio"] + label span:hover,.chose input[type="checkbox"] + label:hover span {
            background:url('{{ asset('img/checkbox/checkbox-checked.png') }}') center center no-repeat;
            opacity : 0.5;
        }




        input[type="radio"] {
            display:none;
        }

        .false input[type="radio"]:checked + label span {
            background:url('{{ asset('img/checkbox/radio-wrong-checked.png') }}') center center no-repeat #272E4A;
        }
        .false input[type="radio"] + label span {
            display:inline-block;
            width:35px;
            height:35px;
            margin:-2px 10px 0 0;
            -webkit-border-radius: 25px!important;
            border-radius: 25px!important;
            vertical-align:middle;
            background:url('{{ asset('img/checkbox/radio-wrong-unchecked.png') }}') center center no-repeat #ededed;
            cursor:pointer;
        }

        .false input[type="radio"] + label span:hover {
            background:url('{{ asset('img/checkbox/radio-wrong-checked.png') }}') center center no-repeat #272E4A;
            opacity : 0.5;
        }

        .true input[type="radio"]:checked + label span {
            background:url('{{ asset('img/checkbox/radio-correct-checked.png') }}') center center no-repeat #272E4A;
        }
        .true input[type="radio"] + label span {
            display:inline-block;
            width:35px;
            height:35px;
            margin:-2px 10px 0 0;
            -webkit-border-radius: 25px!important;
            border-radius: 25px!important;
            vertical-align:middle;
            background:url('{{ asset('img/checkbox/radio-correct-unchecked.png') }}') center center no-repeat #ededed;
            cursor:pointer;
        }

        .true input[type="radio"] + label span:hover {
            background:url('{{ asset('img/checkbox/radio-correct-checked.png') }}') center center no-repeat #272E4A;
            opacity : 0.5;
        }
        .true,.false{
            display: inline-block;
        }
        .input-group-addon.chose,.input-group-addon.chose label{
            padding: 0px;
            margin: 0px;
        }
        #coursesQuestions{
            max-height: 200px;
            overflow-x: hidden;
            overflow-y: scroll;
        }
        #customDiv{
            border-right: 2px solid #eef1f5;
            min-height: 400px;
        }
    </style>
@endsection
@section('contentHeader')
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ URL('admin') }}">{{ Lang::get('main.dashboard') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <a href="{{ URL('admin/modules_trainings_questions') }}">{{ Lang::get('main.modules_trainings_questions') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.modules_trainings_questions') }}
        <small>{{ Lang::get('main.add') }}</small>
    </h1>
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
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">


                {!! Form::open(['method'=>'POST','url'=>'admin/modules_trainings_questions','id'=>'addmodules_trainings_questionsForm','files'=>true]) !!}
                <div class="form-body">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_error') }}
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_success') }}
                    </div>
                    <div class="form-group form-group-select2 col-lg-12">
                        <label for="module_id">{{ Lang::get('main.module_name') }}</label>
                        <select style="width: 100%" id="module_id" class="sel2" name="module_id">
                            @foreach($modules as $module)
                                <option @if(old('module_id')==$module->id) selected="selected" @endif value="{{ $module->id }}">{{ $module->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-lg-3" >
                        <label for="type-1">{{ Lang::get('main.type') }}</label>
                        <div class="form-group form-group-select2">
                            <select style="width:100%" class="sel2" name="type" id="type-1">
                                <option selected="selected" value="true_false">{{ Lang::get('main.true_false') }}</option>
                                <option  value="chose_single">{{ Lang::get('main.chose_answer') }}</option>
                                <option  value="chose_multiple">{{ Lang::get('main.chose_answers') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1" style="margin-top: 22px;">
                        <button data-id="type-1" style="margin-top: 2px;" class="btn btn-primary addNewQuestion"><i class="glyphicon glyphicon-plus"></i></button>
                    </div>
                    <div class="clearfix"></div>
                    <div id="questionsContent">
                        @if(old('questions')&&is_array(old('questions')))

                            <?php
                            $x = 0;
                            $trueFalseCount = 0;
                            $choseSingleCount = 0;
                            $choseMultipleCount = 0;
                            ?>
                            @foreach(old('questions')['name_ar'] as $name_ar)
                                @if(old('questions')['type'][$x]=='true_false')
                                    <div class="row">
                                        <div class="col-lg-10">
                                            <div class="form-group col-lg-8">
                                                <label for="questions_name_{{ $x }}">{{ Lang::get('main.question') }}</label>
                                                <input type="text" class="form-control" name="questions[name_ar][]" value="{{ old('questions')['name_ar'][$x] }}" id="questions_name_ar_{{ $x }}" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_ar') }}">
                                                <input type="text" class="form-control" name="questions[name_en][]" value="{{ old('questions')['name_en'][$x] }}" id="questions_name_en_{{ $x }}" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                                <input type="hidden" name="questions[type][]" value="true_false">
                                            </div>
                                            <div class="col-lg-2">
                                                <label for="">{{ Lang::get('main.difficulty_type') }}</label>
                                                <select style="width:100%" class="sel2" name="questions[difficulty_type][]" id="difficulty_type">
                                                    <option @if(isset(old('questions')['difficulty_type'][$x])&&old('questions')['difficulty_type'][$x]=='easy') selected="selected" @endif value="easy">{{ Lang::get('main.easy') }}</option>
                                                    <option @if(isset(old('questions')['difficulty_type'][$x])&&old('questions')['difficulty_type'][$x]=='normal') selected="selected" @endif value="normal">{{ Lang::get('main.normal') }}</option>
                                                    <option @if(isset(old('questions')['difficulty_type'][$x])&&old('questions')['difficulty_type'][$x]=='hard') selected="selected" @endif value="hard">{{ Lang::get('main.hard') }}</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-2" style="margin-top: 24px;">
                                                <div class="true">
                                                    <input type="radio" name="questions[answers][{{ $x }}]" @if((isset(old('questions')['answers'][$x])&& old('questions')['answers'][$x] == 1)) checked="checked" @endif value="1" id="questions_answers_{{ $x }}_true">
                                                    <label for="questions_answers_{{ $x }}_true">
                                                        <span></span>
                                                    </label>
                                                </div>

                                                <div class="false">
                                                    <input type="radio" name="questions[answers][{{ $x }}]" @if((isset(old('questions')['answers'][$x]) && old('questions')['answers'][$x] == 0))  checked="checked" @endif value="0" id="questions_answers_{{ $x }}_false">
                                                    <label for="questions_answers_{{ $x }}_false">
                                                            <span></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div> <button class="btn btn-danger col-lg-2 remove_question" data-type="true_false" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button>
                                    </div>
                                    <?php $trueFalseCount++;?>
                                @elseif(old('questions')['type'][$x]=='chose_single')
                                    <div>
                                        <div class="form-group col-lg-8">
                                            <label for="questions_name_{{ $x }}">{{ Lang::get('main.question') }}
                                            </label>
                                            <input type="text" class="form-control" name="questions[name_ar][]" value="{{ old('questions')['name_ar'][$x] }}" id="questions_name_ar_{{ $x }}" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_ar') }}">
                                            <input type="text" class="form-control" name="questions[name_en][]" value="{{ old('questions')['name_en'][$x] }}" id="questions_name_en_{{ $x }}" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                            <input type="hidden" name="questions[type][]" value="chose_single">
                                        </div>
                                        <div class="col-lg-2">
                                            <label for="">{{ Lang::get('main.difficulty_type') }}</label>
                                            <select style="width:100%" class="sel2" name="questions[difficulty_type][]" id="difficulty_type">
                                                <option @if(isset(old('questions')['difficulty_type'][$x])&&old('questions')['difficulty_type'][$x]=='easy') selected="selected" @endif value="easy">{{ Lang::get('main.easy') }}</option>
                                                <option @if(isset(old('questions')['difficulty_type'][$x])&&old('questions')['difficulty_type'][$x]=='normal') selected="selected" @endif value="normal">{{ Lang::get('main.normal') }}</option>
                                                <option @if(isset(old('questions')['difficulty_type'][$x])&&old('questions')['difficulty_type'][$x]=='hard') selected="selected" @endif value="hard">{{ Lang::get('main.hard') }}</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_single" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button>

                                        <div class="form-group col-lg-6">
                                            <label for="chose_single_{{ $x }}_{{ $choseSingleCount }}_1">{{ Lang::get('main.choice') }} 1
                                            </label>
                                            <div class="input-group">
                                                    <span class="input-group-addon chose">
                                                    <input type="radio" id="chose_single_{{ $x }}_{{ $choseSingleCount }}_1" @if((isset(old('chose_question_answer')[$x]) && old('chose_question_answer')[$x] == 1)) checked="checked" @endif value="1" name="chose_question_answer[{{ $x }}]">
                                                    <label for="chose_single_{{ $x }}_{{ $choseSingleCount }}_1">

                                                    <span>
                                                    </span>
                                                    </label>
                                                    </span>
                                                <input type="text" class="form-control" name="questions_ar[answers][{{ $x }}][]" value="@if((isset(old('questions_ar')['answers'][$x][0]))){{ old('questions_ar')['answers'][$x][0] }}@endif" id="chose_single_{{ $x }}_{{ $choseSingleCount }}_1" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 1">
                                                <input type="text" class="form-control" name="questions_en[answers][{{ $x }}][]" value="@if((isset(old('questions_en')['answers'][$x][0]))){{ old('questions_en')['answers'][$x][0] }}@endif" id="chose_single_{{ $x }}_{{ $choseSingleCount }}_1" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 1">

                                                <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a>
                                                    </span>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-6">
                                            <label for="chose_single_{{ $x }}_{{ $choseSingleCount }}_2">{{ Lang::get('main.choice') }} 2
                                            </label>

                                            <div class="input-group">
                                                    <span class="input-group-addon chose">
                                                    <input type="radio" id="chose_single_{{ $x }}_{{ $choseSingleCount }}_2" @if((isset(old('chose_question_answer')[$x]) && old('chose_question_answer')[$x] == 2)) checked="checked" @endif value="2" name="chose_question_answer[{{ $x }}]">
                                                    <label for="chose_single_{{ $x }}_{{ $choseSingleCount }}_2">

                                                    <span>
                                                    </span>
                                                    </label>
                                                    </span>
                                                <input type="text" class="form-control" name="questions_ar[answers][{{ $x }}][]" value="@if((isset(old('questions_ar')['answers'][$x][1]))){{ old('questions_ar')['answers'][$x][1] }}@endif" id="chose_single_{{ $x }}_{{ $choseSingleCount }}_2" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 2">
                                                <input type="text" class="form-control" name="questions_en[answers][{{ $x }}][]" value="@if((isset(old('questions_en')['answers'][$x][1]))){{ old('questions_en')['answers'][$x][1] }}@endif" id="chose_single_{{ $x }}_{{ $choseSingleCount }}_2" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 2">

                                                <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a>
                                                    </span>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-6">
                                            <label for="chose_single_{{ $x }}_{{ $choseSingleCount }}_3">Choice 3
                                            </label>
                                            <div class="input-group">
                                                    <span class="input-group-addon chose">
                                                    <input type="radio" id="chose_single_{{ $x }}_{{ $choseSingleCount }}_3" @if((isset(old('chose_question_answer')[$x]) && old('chose_question_answer')[$x] == 3)) checked="checked" @endif value="3" name="chose_question_answer[{{ $x }}]">
                                                    <label for="chose_single_{{ $x }}_{{ $choseSingleCount }}_3">

                                                    <span>
                                                    </span>
                                                    </label>
                                                    </span>
                                                <input type="text" class="form-control" name="questions_ar[answers][{{ $x }}][]" value="@if((isset(old('questions_ar')['answers'][$x][2]))){{ old('questions_ar')['answers'][$x][2] }}@endif" id="chose_single_{{ $x }}_{{ $choseSingleCount }}_3" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 3">
                                                <input type="text" class="form-control" name="questions_en[answers][{{ $x }}][]" value="@if((isset(old('questions_en')['answers'][$x][2]))){{ old('questions_en')['answers'][$x][2] }}@endif" id="chose_single_{{ $x }}_{{ $choseSingleCount }}_3" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 3">

                                                <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a>
                                                    </span>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-6">
                                            <label for="chose_single_{{ $x }}_{{ $choseSingleCount }}_4">{{ Lang::get('main.choice') }} 4
                                            </label>

                                            <div class="input-group">
                                            <span class="input-group-addon chose">
                                            <input type="radio" id="chose_single_{{ $x }}_{{ $choseSingleCount }}_4" @if((isset(old('chose_question_answer')[$x]) && old('chose_question_answer')[$x] == 4)) checked="checked" @endif value="4" name="chose_question_answer[{{ $x }}]">
                                            <label for="chose_single_{{ $x }}_{{ $choseSingleCount }}_4">

                                            <span>
                                            </span>
                                            </label>
                                            </span>
                                                <input type="text" class="form-control" name="questions_ar[answers][{{ $x }}][]" value="@if((isset(old('questions_ar')['answers'][$x][3]))){{ old('questions_ar')['answers'][$x][3] }}@endif" id="chose_single_{{ $x }}_{{ $choseSingleCount }}_4" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 4">
                                                <input type="text" class="form-control" name="questions_en[answers][{{ $x }}][]" value="@if((isset(old('questions_en')['answers'][$x][3]))){{ old('questions_en')['answers'][$x][3] }}@endif" id="chose_single_{{ $x }}_{{ $choseSingleCount }}_4" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 4">
                                                <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a>
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $choseSingleCount++;?>
                                @elseif(old('questions')['type'][$x]=='chose_multiple')
                                    <div>
                                        <div class="form-group col-lg-8">
                                            <label for="questions_name_{{ $x }}">{{ Lang::get('main.question') }}
                                            </label>
                                            <input type="text" class="form-control" name="questions[name_ar][]" value="{{ old('questions')['name_ar'][$x] }}" id="questions_name_ar_{{ $x }}" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_ar') }}">
                                            <input type="text" class="form-control" name="questions[name_en][]" value="{{ old('questions')['name_en'][$x] }}" id="questions_name_en_{{ $x }}" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">
                                            <input type="hidden" name="questions[type][]" value="chose_multiple">
                                        </div>
                                        <div class="col-lg-2">
                                            <label for="">{{ Lang::get('main.difficulty_type') }}</label>
                                            <select style="width:100%" class="sel2" name="questions[difficulty_type][]" id="difficulty_type">
                                                <option @if(isset(old('questions')['difficulty_type'][$x])&&old('questions')['difficulty_type'][$x]=='easy') selected="selected" @endif value="easy">{{ Lang::get('main.easy') }}</option>
                                                <option @if(isset(old('questions')['difficulty_type'][$x])&&old('questions')['difficulty_type'][$x]=='normal') selected="selected" @endif value="normal">{{ Lang::get('main.normal') }}</option>
                                                <option @if(isset(old('questions')['difficulty_type'][$x])&&old('questions')['difficulty_type'][$x]=='hard') selected="selected" @endif value="hard">{{ Lang::get('main.hard') }}</option>
                                            </select>
                                        </div>
                                        <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_multiple" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button>
                                        <div class="form-group col-lg-6">
                                            <label for="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_1">{{ Lang::get('main.choice') }} 1
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-addon chose">
                                                <input type="checkbox" id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_1" @if((isset(old('chose_question_answer')[$x][0])))  checked="checked" @endif value="1" name="chose_question_answer[{{ $x }}][0]">
                                                <label for="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_1">

                                                <span>
                                                </span>
                                                </label>
                                                </span>
                                                <input type="text" class="form-control" name="questions_ar[answers][{{ $x }}][]" value="@if((isset(old('questions_ar')['answers'][$x][0]))){{ old('questions_ar')['answers'][$x][0] }}@endif" id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_1" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 1">
                                                <input type="text" class="form-control" name="questions_en[answers][{{ $x }}][]" value="@if((isset(old('questions_en')['answers'][$x][0]))){{ old('questions_en')['answers'][$x][0] }}@endif" id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_1" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 1">
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label for="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_2">{{ Lang::get('main.choice') }} 2
                                            </label>
                                            <div class="input-group">
                                            <span class="input-group-addon chose">
                                            <input type="checkbox" id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_2" @if((isset(old('chose_question_answer')[$x][1]))) checked="checked" @endif value="1" name="chose_question_answer[{{ $x }}][1]">
                                            <label for="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_2">

                                            <span>
                                            </span>
                                            </label>
                                            </span>
                                                <input type="text" class="form-control" name="questions_ar[answers][{{ $x }}][]" value="@if((isset(old('questions_ar')['answers'][$x][1]))){{ old('questions_ar')['answers'][$x][1] }}@endif" id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_2" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 2">
                                                <input type="text" class="form-control" name="questions_en[answers][{{ $x }}][]" value="@if((isset(old('questions_en')['answers'][$x][1]))){{ old('questions_en')['answers'][$x][1] }}@endif" id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_2" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 2">
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <label for="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_3">Choice 3
                                            </label>

                                            <div class="input-group">
                                            <span class="input-group-addon chose">
                                            <input type="checkbox" id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_3" @if((isset(old('chose_question_answer')[$x][2]))) checked="checked" @endif value="1" name="chose_question_answer[{{ $x }}][2]">
                                            <label for="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_3">

                                            <span>
                                            </span>
                                            </label>
                                            </span>
                                                <input type="text" class="form-control" name="questions_ar[answers][{{ $x }}][]" value="@if((isset(old('questions_ar')['answers'][$x][2]))){{ old('questions_ar')['answers'][$x][2] }}@endif" id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_3" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 3">
                                                <input type="text" class="form-control" name="questions_en[answers][{{ $x }}][]" value="@if((isset(old('questions_en')['answers'][$x][2]))){{ old('questions_en')['answers'][$x][2] }}@endif" id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_3" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 3">
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-6">
                                            <label for="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_4">{{ Lang::get('main.choice') }} 4</label>
                                            <div class="input-group">
                                            <span class="input-group-addon chose">
                                            <input type="checkbox" id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_4" @if((isset(old('chose_question_answer')[$x][3]))) checked="checked" @endif value="1" name="chose_question_answer[{{ $x }}][3]">
                                            <label for="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_4">

                                            <span>
                                            </span>
                                            </label>
                                            </span>
                                                <input type="text" class="form-control" name="questions_ar[answers][{{ $x }}][]" value="@if((isset(old('questions_ar')['answers'][$x][3]))) {{ old('questions_ar')['answers'][$x][3] }} @endif" id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_4" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 4">
                                                <input type="text" class="form-control" name="questions_en[answers][{{ $x }}][]" value="@if((isset(old('questions_en')['answers'][$x][3]))) {{ old('questions_en')['answers'][$x][3] }} @endif" id="chose_multiple_{{ $x }}_{{ $choseMultipleCount }}_4" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 4">
                                            </div>
                                        </div>
                                    </div>
                                    <?php $choseMultipleCount++;?>
                                @endif
                                <?php $x++?>
                            @endforeach

                        @endif

                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-lg-3" >
                        <label for="type-2">{{ Lang::get('main.type') }}</label>
                        <div class="form-group form-group-select2">
                            <select style="width:100%" class="sel2" name="type" id="type-2">
                                <option selected="selected" value="true_false">{{ Lang::get('main.true_false') }}</option>
                                <option  value="chose_single">{{ Lang::get('main.chose_answer') }}</option>
                                <option  value="chose_multiple">{{ Lang::get('main.chose_answers') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-1" style="margin-top: 22px;">
                        <button data-id="type-2" style="margin-top: 2px;"  class="btn btn-primary addNewQuestion"><i class="glyphicon glyphicon-plus"></i></button>
                    </div>
                    <div class="clearfix"></div>


                    <div class="pull-right">
                        <button class="btn btn-primary" name="btnAction" id="btnAction" type="submit">{{ Lang::get('main.add') }}</button>
                    </div>
                </div>
                <div class="clearfix" style="height: 30px"></div>
                {!! Form::close() !!}


            </div>
        </div>
    </div>
@endsection
@section('scriptCode')
    <script>
        $(document).ready(function(){
            $(".sel2").select2()
            var token= '{{ csrf_token() }}';
            var wrapper_times        = $("#questionsContent"); //title Fields wrapper
            var add_button_times     = $(".addNewQuestion"); //title Add button ID
            var trueFalseAnswersCount={{ (isset($trueFalseCount))?$trueFalseCount+1:0 }};
            var choiceMultipleAnswersCount={{ (isset($choseMultipleCount))?$choseMultipleCount+1:0 }};
            var choiceSingleAnswersCount={{ (isset($choseSingleCount))?$choseSingleCount+1:0 }};
            var questions={{ isset($x)?$x:0 }};
            $(document).on('click','.removeAnswer',function(e){
                e.preventDefault();
                $(this).parent().parent().parent().remove();
            });
            $(document).on('click','.addNewQuestion',function(e){ //on add input button click
                e.preventDefault();
                type=$("#"+$(this).data('id')).val();
                console.log(type);
                questions=(questions>0)?questions:0;
                $('.sel2').each(function(){
                    $(this).select2('destroy');
                });
                if(type=='true_false'){
                    $(wrapper_times).append('<div class="row"> <div class="col-lg-10"> <div class="form-group col-lg-8"> <label for="questions_name_ar_'+questions+'">{{ Lang::get('main.question') }}</label> <input type="text" class="form-control" name="questions[name_ar]['+questions+']" id="questions_name_ar_'+questions+'" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_ar') }}"> <input type="text" class="form-control" name="questions[name_en]['+questions+']" id="questions_name_en_'+questions+'" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}"> <input type="hidden" name="questions[type]['+questions+']" value="true_false"> </div><div class="col-lg-2"><label for="">{{ Lang::get('main.difficulty_type') }}</label><select style="width:100%" class="sel2" name="questions[difficulty_type][]" id="difficulty_type"><option  value="easy">{{ Lang::get('main.easy') }}</option><option  value="normal">{{ Lang::get('main.normal') }}</option><option  value="hard">{{ Lang::get('main.hard') }}</option></select></div> <div class="col-lg-2" style="margin-top: 24px;"> <div class="true"> <input type="radio" name="questions[answers]['+questions+']" checked="checked" value="1" id="questions_answers_'+questions+'_true"> <label for="questions_answers_'+questions+'_true"> <span></span> </label> </div> <div class="false"> <input type="radio" name="questions[answers]['+questions+']" value="0" id="questions_answers_'+questions+'_false"> <label for="questions_answers_'+questions+'_false"> <span></span> </label> </div> </div> </div> <button class="btn btn-danger col-lg-2 remove_question" data-type="true_false" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> </div>');//add input box
                    trueFalseAnswersCount++;
                }else if(type=='chose_multiple'){
                    $(wrapper_times).append('<div class=""> <div class="form-group col-lg-8"> <label for="questions_name_'+questions+'">{{ Lang::get('main.question') }}</label> <input type="text" class="form-control" name="questions[name_ar]['+questions+']" id="questions_name_ar_'+questions+'" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_ar') }}"> <input type="text" class="form-control" name="questions[name_en]['+questions+']" id="questions_name_en_'+questions+'" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}"> <input type="hidden" name="questions[type]['+questions+']" value="chose_multiple"> </div><div class="col-lg-2"><label for="">{{ Lang::get('main.difficulty_type') }}</label><select style="width:100%" class="sel2" name="questions[difficulty_type][]" id="difficulty_type"><option  value="easy">{{ Lang::get('main.easy') }}</option><option  value="normal">{{ Lang::get('main.normal') }}</option><option  value="hard">{{ Lang::get('main.hard') }}</option></select></div> <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_multiple" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> <div class="form-group col-lg-6"> <label for="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_1">{{ Lang::get('main.choice') }} 1</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_1" value="1" name="chose_question_answer['+questions+'][0]"> <label for="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_1"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers]['+questions+'][]" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_1" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 1"> <input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_1" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 1"> </div> </div> <div class="form-group col-lg-6"> <label for="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_2">{{ Lang::get('main.choice') }} 2</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_2" value="1" name="chose_question_answer['+questions+'][1]"> <label for="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_2"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers]['+questions+'][]" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_2" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 2"> <input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_2" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 2"> </div> </div> <div class="form-group col-lg-6"> <label for="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_3">{{ Lang::get('main.choice') }} 3</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_3" value="1" name="chose_question_answer['+questions+'][2]"> <label for="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_3"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers]['+questions+'][]" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_3" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 3"> <input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_3" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 3"> </div> </div> <div class="form-group col-lg-6"> <label for="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_4">{{ Lang::get('main.choice') }} 4</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_4" value="1" name="chose_question_answer['+questions+'][3]"> <label for="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_4"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers]['+questions+'][]" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_4" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 4"> <input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_4" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 4"> </div> </div> </div>');//add input box
                    choiceMultipleAnswersCount++
                }else if(type=='chose_single'){
                    $(wrapper_times).append('<div class=""> <div class="form-group col-lg-8"> <label for="questions_name_'+questions+'">{{ Lang::get('main.question') }}</label> <input type="text" class="form-control" name="questions[name_ar]['+questions+']" id="questions_name_ar_'+questions+'" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_ar') }}"><input type="text" class="form-control" name="questions[name_en]['+questions+']" id="questions_name_en_'+questions+'" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}"> <input type="hidden" name="questions[type]['+questions+']" value="chose_single"> </div><div class="col-lg-2"><label for="">{{ Lang::get('main.difficulty_type') }}</label><select style="width:100%" class="sel2" name="questions[difficulty_type][]" id="difficulty_type"><option  value="easy">{{ Lang::get('main.easy') }}</option><option  value="normal">{{ Lang::get('main.normal') }}</option><option  value="hard">{{ Lang::get('main.hard') }}</option></select></div> <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_single" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> <div class="form-group col-lg-6"> <label for="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_1">{{ Lang::get('main.choice') }} 1</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_1" value="1" name="chose_question_answer['+questions+']"> <label for="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_1"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers]['+questions+'][]" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_1" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 1"> <input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_1" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 1"> <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_2">{{ Lang::get('main.choice') }} 2</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_2" value="2" name="chose_question_answer['+questions+']"> <label for="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_2"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers]['+questions+'][]" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_2" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 2"> <input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_2" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 2"> <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_3">{{ Lang::get('main.choice') }} 3</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_3" value="3" name="chose_question_answer['+questions+']"> <label for="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_3"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers]['+questions+'][]" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_3" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 3"> <input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_3" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 3"> <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> <div class="form-group col-lg-6"> <label for="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_4">{{ Lang::get('main.choice') }} 4</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_4" value="4" name="chose_question_answer['+questions+']"> <label for="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_4"> <span></span> </label> </span> <input type="text" class="form-control" name="questions_ar[answers]['+questions+'][]" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_4" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_ar') }} 4"> <input type="text" class="form-control" name="questions_en[answers]['+questions+'][]" id="chose_single_'+questions+'_'+choiceSingleAnswersCount+'_4" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en') }} 4"> <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> </div>');//add input box
                    choiceSingleAnswersCount++
                }
                questions++;
               $('.sel2').select2()
            });
            $(wrapper_times).on("click",".remove_question", function(e) { //user click on remove text
                e.preventDefault();
                type=$(this).data("type");
                if(type=='true_false'){
                    $(this).parent('div').remove();
                    trueFalseAnswersCount--;
                }else if(type=='chose_multiple'){
                    $(this).parent('div').remove();
                    choiceMultipleAnswersCount--;
                }else if(type=='chose_single'){
                    $(this).parent('div').remove();
                    choiceSingleAnswersCount--;
                }
                questions--;
            });
            $(document).on('click','#addFromCoursesExam',function(){
                //$("body").toggleClass('page-sidebar-closed');

                t=$(".page-sidebar"),
                    i=$(".page-sidebar-menu"),
                    e=$("body");
                $(".sidebar-search",t).removeClass("open")
                    ,e.hasClass("page-sidebar-closed")?(e.removeClass("page-sidebar-closed")
                    ,i.removeClass("page-sidebar-menu-closed")
                    ,$.cookie&&$.cookie("sidebar_closed","0")):(e.addClass("page-sidebar-closed")
                    ,i.addClass("page-sidebar-menu-closed"),e.hasClass("page-sidebar-fixed")&&i.trigger("mouseleave")
                    ,$.cookie&&$.cookie("sidebar_closed","1"))
                    ,$(window).trigger("resize");
                if(e.hasClass("page-sidebar-closed")){
                    $("#formDiv").removeClass('col-lg-12');
                    $("#formDiv").addClass('col-lg-8');
                    $("#customDiv").removeClass('hidden');
                    $("#addFromCoursesExam").text('{{ Lang::get('main.close_course_exam') }}').toggleClass('btn-danger btn-success');
                }else{
                    $("#formDiv").removeClass('col-lg-8');
                    $("#formDiv").addClass('col-lg-12');
                    $("#customDiv").addClass('hidden');
                    $("#addFromCoursesExam").text('{{ Lang::get('main.add_from_courses_exam') }}').toggleClass('btn-danger btn-success');
                }
            });
            $(document).on('change','#selectAll',function(){
                if($(this).is(':checked')){
                    $(".questionsCheckBox").prop('checked',true);
                }else{
                    $(".questionsCheckBox").prop('checked',false);
                }
            })
            $(document).on('change','#courses_exam_id',function(){
                courses_exam_id=$(this).val();
                if(courses_exam_id){
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/exams/getCoursesExamQuestions') }}",
                        data: {"courses_exam_id": courses_exam_id,_token:token},
                        success: function (msg) {
                            html='';
                            msg.result.forEach(function(item){
                                html+='<li> <div class="task-checkbox"> <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline"> <input type="checkbox" class="checkboxes questionsCheckBox" data-type="'+item.type+'" data-name="'+item.name+'" data-details=\''+JSON.stringify(item.details)+'\' value="1" /> <span></span> </label> </div> <div class="task-title"> <span class="task-title-sp"> '+item.name+' </span> </div> </li>';
                            });
                            $("#selectAll").parent().parent().parent().parent().removeClass('hidden');
                            $("#addSelectedQuestions").removeClass('hidden')
                            $("#coursesQuestions").html(html);
                        }
                    });
                }

            });

            $(document).on('click','#addSelectedQuestions',function(){
                $(".questionsCheckBox:checked").each(function(){
                    name=$(this).data('name');
                    type=$(this).data('type');
                    details=JSON.parse($(this).attr('data-details'));
                    console.log(details);
                    questions=(questions>0)?questions:0;
                    if(type=='true_false'){
                        dataHTML='<div class="row"> <div class="col-lg-10"> <div class="form-group col-lg-10"> <label for="questions_name_'+questions+'">{{ Lang::get('main.question') }}</label> <input type="text" class="form-control" name="questions[name]['+questions+']" id="questions_name_'+questions+'" value="'+name+'" placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}"> <input type="hidden" name="questions[type]['+questions+']" value="true_false"> </div> <div class="col-lg-2" style="margin-top: 24px;"> <div class="true"> <input type="radio" name="questions[answers]['+questions+']" '+((details.answer==1)?'checked="checked"':'')+' value="1" id="questions_answers_'+questions+'_true"> <label for="questions_answers_'+questions+'_true"> <span></span> </label> </div> <div class="false"> <input type="radio"  name="questions[answers]['+questions+']" '+((details.answer==0)?'checked="checked"':'')+' value="0" id="questions_answers_'+questions+'_false"> <label for="questions_answers_'+questions+'_false"> <span></span> </label> </div> </div> </div> <button class="btn btn-danger col-lg-2 remove_question" data-type="true_false" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> </div>';
                        $(wrapper_times).append(dataHTML);//add input box
                        trueFalseAnswersCount++;
                    }else if(type=='chose_multiple'){
                        dataHTML='<div class=""> <div class="form-group col-lg-10"> <label for="questions_name_'+questions+'">{{ Lang::get('main.question') }}</label> <input type="text" class="form-control" name="questions[name]['+questions+']" id="questions_name_'+questions+'" value="'+name+'" placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}"> <input type="hidden" name="questions[type]['+questions+']" value="chose_multiple"> </div> <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_multiple" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button>' ;
                        xx=1;
                        details.forEach(function(dd){
                            dataHTML+='<div class="form-group col-lg-6"><label for="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_'+xx+'">{{ Lang::get('main.choice') }} 1</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_'+xx+'" '+((dd.answer)?'checked="checked"':'')+' value="1" name="chose_question_answer['+questions+']['+(x-1)+']"> <label for="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_'+xx+'"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers]['+questions+'][]" value="'+dd.name+'" id="chose_multiple_'+questions+'_'+choiceMultipleAnswersCount+'_'+xx+'" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice') }} 1"> </div> </div> ';
                            xx++;
                        });
                        dataHTML+='</div>';
                        $(wrapper_times).append(dataHTML);//add input box
                        choiceMultipleAnswersCount++
                    }else if(type=='chose_single'){
                        dataHTML='<div class=""> <div class="form-group col-lg-10"> <label for="questions_name_'+questions+'">{{ Lang::get('main.question') }}</label> <input type="text" class="form-control" name="questions[name]['+questions+']" id="questions_name_'+questions+'" value="'+name+'" placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}"> <input type="hidden" name="questions[type]['+questions+']" value="chose_single"> </div> <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_single" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> ';
                        xx=1;
                        details.forEach(function(dd) {
                            dataHTML += '<div class="form-group col-lg-6"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_'+xx+'">{{ Lang::get('main.choice') }} 1</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_'+xx+'" '+((dd.answer)?'checked="checked"':'')+' value="'+xx+'" name="chose_question_answer[' + questions + ']"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_'+xx+'"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][' + questions + '][]" value="'+dd.name+'" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_'+xx+'" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice') }} 1"> <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> ';
                            xx++;
                        });
                        dataHTML+='</div>';
                        $(wrapper_times).append(dataHTML);//add input box
                        choiceSingleAnswersCount++
                    }
                    questions++;
                });
            });
        });
    </script>
@endsection