@extends('auth.layouts.app')
@section('pageTitle')
    <title>{{ Lang::get('main.home_page_title') }}</title>
    <style>
        body {
            max-height: 100%;
        }
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
        .question-object:not(:first-child) {
            margin-top: 35px;
        }
        .question-object:not(:last-child) {
            border-bottom: 1px solid #e7ecf1;
        }
        .input-group-addon {
            border: 0 !important;
        }
        .input-group {
            border: 1px solid #ccc !important;
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
    <div class="modal fade import-course-question-modal" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="margin-top: 50px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ Lang::get('main.import_questions') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {!! Form::open(['url'=>'admin/import_course_questions']) !!}
                <div class="modal-body">
                    <div class="form-group form-group-select2">
                        <label for="course_id">{{ Lang::get('main.course_name') }}</label>
                        <select style="width: 100%" id="course_id" class="sel2" name="course_id">
                            <option value="" id="first_opt">{{ Lang::get('main.select') }} {{ Lang::get('main.course') }}</option>
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
                            <span style="margin-left: 19px;">
                                {{ Lang::get('main.true_or_false') }}
                                <i style="color: #f0ad4e;" class="fa fa-check"></i>
                            </span>
                            <span>
                                {{ Lang::get('main.single_choice') }}
                                <i style="color: #337ab7;" class="fa fa-check-square-o"></i>
                            </span>
                            <span>
                                {{ Lang::get('main.multiple_choices') }}
                                <i style="color: #ed6b75;" class="fa fa-check-square-o"></i>
                                <i style="color: #ed6b75;" class="fa fa-check-square-o"></i>
                            </span>
                        </div>
                        <div class="col-sm-3">
                            <div class="md-checkbox checkAllQuestion">
                                <input name="checkAllQuestion" type="checkbox" id="checkAllQuestion" class="md-check ">
                                <label for="checkAllQuestion">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> {{ Lang::get('main.check_all') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="text" class="form-control" id="searchQuestion" name="search_question" placeholder="{{ Lang::get('main.search') }}">
                            </div>
                        </div>
                    </div>

                    <div class="questions-area" style="display: none;height: 230px; overflow-x: hidden; overflow-y: scroll;padding: 5px 19px;position: relative;z-index: 55">
                        <div class="overlay">
                            <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                            <span class="sr-only">{{ Lang::get('main.loading') }}</span>
                        </div>
                        <h5 style="margin-bottom: 20px; font-weight: bold;text-transform: capitalize">{{ Lang::get('main.select_questions_as_you_want') }} </h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeModal" data-dismiss="modal">{{ Lang::get('main.cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="importSelected">{{ Lang::get('main.import_selected') }}</button>
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
        <div class="portlet light bordered col-md-6 window-height" style="height: 100%;overflow-y: auto;">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-modules_questions font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.add_or_update') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <input type="hidden" class="form-control" name="questions_course" value="{{$course_question->id}}">
                <input type="hidden" class="form-control" name="image_url" value="{{ assetURL('/') }}">
                {!! Form::open(['method'=>'PUT','url'=>'admin/courses_questions3/'.$course_question->id,'id'=>'addmodules_questionsEditForm','files'=>true, 'enctype'=>'multipart/form-data']) !!}
                    <input type="hidden" class="form-control" name="questions_type" value="{{ $course_question->questions_type }}">
                <div class="form-body">
                    <div class="form-group form-group-select2 col-lg-6">
                        <label for="module_id">{{ Lang::get('main.course_name') }}</label>
                        <select style="width: 100%" id="module_id" class="sel2" name="module_id" disabled>
                            @foreach($courses as $course)
                                <option @if($course->id==$course_question->course_id) selected="selected" @endif value="{{ $course_question->id }}">{{ $course->name }}</option>
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

                    <div class="portlet light bordered">
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button>
                            {{ Lang::get('main.form_validation_success') }}
                        </div>
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button>
                            {{ Lang::get('main.form_validation_error') }}
                        </div>

                        <div class="portlet-body">
                            <div class="form-group col-lg-6">
                                <label for="type-1">{{ Lang::get('main.type') }}</label>
                                <div class="form-group form-group-select2">
                                    <select style="width:100%" class="sel2" name="type" id="type-1">
                                        <option selected="selected" value="true_false">{{ Lang::get('main.true_false') }}</option>
                                        <option value="chose_single">{{ Lang::get('main.chose_answer') }}</option>
                                        <option value="chose_multiple">{{ Lang::get('main.chose_answers') }}</option>
                                        <option value="chose_single_with_images">{{ Lang::get('main.chose_answer_with_images') }}</option>
                                        <option value="chose_multiple_with_images">{{ Lang::get('main.chose_answers_with_images') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-1" style="margin-top: 22px;">
                                <button type="button" data-id="type-1" style="margin-top: 2px;" class="btn btn-primary addNewQuestion"><i class="glyphicon glyphicon-plus"></i></button>
                            </div>

                            <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target=".import-course-question-modal" style="margin-top: 22px;">{{ Lang::get('main.import_questions') }}</button>

                            <div class="clearfix"></div>

                            <div class="new-question-message text-center" style="margin: 10px auto;">
                                <p class="bg-warning" style="padding: 15px;">{{ Lang::get('main.add_or_update_question') }}</p>
                            </div>

                            <div id="newQuestions"></div>

                            <div class="clearfix"></div>

                            <div class="" style="margin: auto; width: 200px">
                                <button class="btn btn-success add-save-button" name="btnAction" id="btnAction" type="submit" style="padding: 10px 60px; display: none;border-radius:8px !important;">{{ Lang::get('main.save') }}</button>
                                <button class="btn btn-success disabled-save-button" type="submit" style="padding: 10px 60px; display: none;border-radius:8px !important;" disabled>{{ Lang::get('main.loading') }}</button>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div> <!-- form body -->
                {!! Form::close() !!}
                <div class="clearfix"></div>
            </div>
        </div>
        <div class="portlet light bordered col-md-6 overflow window-height" style="margin:0;overflow-y: auto;">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-modules_questions font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.questions') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body row" id="questionsContent">
                @include('auth.courses_questions3.questions.question_body', ['courses_questions' => $courses_questions])
            </div>
        </div>
    </div>
@endsection
@section('scriptCode')
    <script>
        $(document).ready(function () {
            $('.menu-toggler').trigger( "click" );
            $('.window-height').height($(window).height() - ($('.page-header').height() + $('.page-bar').height() + $('.page-footer').height() + 80));
            $(".sel2").select2();
            var token = '{{ csrf_token() }}';
            var New_Questions_wrapper = $('#newQuestions');
            var add_button_times = $(".addNewQuestion"); //title Add button ID

            $(document).on('click', '.removeAnswer', function (e) {
                e.preventDefault();
                $(this).parent().parent().parent().remove();
            });

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                      $(input).parent().find('img').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $(document).on('click', '.edit_question', function (e) {
                e.preventDefault();
                $('.new-question-message').hide();
                $('.add-save-button').show();
                $("html, body").animate({ scrollTop: 0 }, "slow");
                var here = $(this);
                var length = here.closest('li').find('input[type="text"]').length;
                var objects = here.closest('li').find('img[class="choices_name"]').length;
                type = here.data('type');
                var question_id = here.closest('li').find('input[name="question_id"]').val();
                var english_question = '';
                @if($course_question->questions_type=='arabic_and_english')
                    english_question ='<input type="text" class="form-control" name="question_en" id="question" placeholder="{{ Lang::get('main.enter').Lang::get('main.question_en') }}">';
                @endif

                var questionCont = here.closest('li').find('p').html();
                var imageCont = here.closest('li').find('.question_image').attr('src');

                var html = '<div class="question-container"><input type="hidden" name="edit" value="edit" />';
                if (type != 'true_false') {
                    html += '<div class="row">';
                }

                html += '<div class="form-group col-lg-6"> <label for="question">{{ Lang::get('main.question') }}</label><input type="text" class="form-control" name="question" value="'+questionCont+'">'+english_question+'<input type="hidden" name="type" value="'+type+'"><input type="hidden" name="question_id" value="'+question_id+'"></div><div class="form-group col-lg-3" id="hamada">';

                if(imageCont != undefined) {
                    html += '<label for="file-input"><img src="'+imageCont+'" style="width: 100%;height: 100px;" title="{{ Lang::get('main.click_to_choose_different_image') }}" alt="{{ Lang::get('main.click_to_choose_different_image') }}"/></label><input class="change_image_choice" id="file-input" name="image" type="file" style="display: none;" /></div>';

                    //html += '<img style="width: 50%;height: 100px; display: inline;" src="'+imageCont+'" alt=""><input type="file" class="change_image_choice" style="width: 50%;display:inline;" name="image"></div>';
                } else {
                    html += '<input type="file" class="change_image_choice" style="width: 100%;margin-top: 30px;" name="image"></div>';
                }

                if (type != 'true_false') {
                    html += '<button class="btn btn-danger col-lg-1 col-lg-offset-2 remove_question" data-type="'+type+'" style="margin-top: 24px;height: 40px;width: 44px;border-radius: 50% !important;font-size: 18px;"><i class="glyphicon glyphicon-remove"></i></button></div>';
                }
                if (type == 'true_false') {
                    var trueChoice = here.closest('li').find('.true input').attr('checked') == undefined ? '' : 'checked';
                    var falseChoice = here.closest('li').find('.false input').attr('checked') == undefined ? '' : 'checked';

                    html += '<div class="col-lg-2 text-center" style="margin-top: 24px;"> <div class="true"> <input type="radio" name="answers" '+trueChoice+' value="1" id="question_true"> <label for="question_true"><span></span></label></div><div class="false"> <input type="radio" name="answers" '+falseChoice+' value="0" id="question_false"><label for="question_false"><span></span></label></div></div><button class="btn btn-danger col-lg-1 remove_question" data-type="'+type+'" style="margin-top: 24px;height: 40px;width: 44px;border-radius: 50% !important;font-size: 18px;"><i class="glyphicon glyphicon-remove"></i></button>';

                }
                else {
                    var choicesName = here.closest('li').find('.choices_name');
                    var choicesValues = new Array();
                    choicesRadio = here.closest('li').find('.choices_values');
                    var choicesTruth = new Array();
                    var inputNames = new Array();
                    var inputValues = new Array();
                    for(var i = 0; i < choicesRadio.length; i++){
                        choicesTruth[i] = $(choicesRadio[i]).attr('checked') == undefined? '' : 'checked';
                        inputValues[i] = $(choicesRadio[i]).val();
                    }
                    if (type == 'chose_single') {
                        for(var i = 0; i < choicesName.length; i++){
                            choicesValues[i] = $(choicesName[i]).val();
                            inputNames[i] = $(choicesName[i]).attr('name');
                        }

                        english_detail1='';
                        english_detail2='';
                        english_detail3='';
                        english_detail4='';

                        @if($course_question->questions_type=='arabic_and_english')
                            english_detail1='<input type="text" class="form-control" name="answers_text_en['+inputValues[i]+']" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en').' 1' }}">';
                            english_detail2='<input type="text" class="form-control" name="answers_text_en['+inputValues[i]+']" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en').' 2' }}">';
                            english_detail3='<input type="text" class="form-control" name="answers_text_en['+inputValues[i]+']" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en').' 3' }}">';
                            english_detail4='<input type="text" class="form-control" name="answers_text_en['+inputValues[i]+']" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice_en').' 4' }}">';
                        @endif

                        for (var i = 0; i < length; i++) {
                            html += '<div class="form-group col-lg-6"><label for="chose_single_'+(i)+'">{{ Lang::get('main.choice') }} '+(i+1)+'</label><div class="input-group"><span class="input-group-addon chose"><input type="radio" id="chose_'+(i)+'" value="'+(inputValues[i])+'" '+choicesTruth[i]+' name="chose_single"><label for="chose_'+i+'"><span></span></label></span><input type="text" class="form-control" name="'+inputNames[i]+'" value="'+choicesValues[i]+'">'+english_detail1+'<span class="input-group-addon "><a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a></span></div></div> '
                        }
                    }
                    else if (type == 'chose_multiple') {
                        for(var i = 0; i < choicesName.length; i++){
                            choicesValues[i] = $(choicesName[i]).val();
                        }
                        english_detail1='';
                        english_detail2='';
                        english_detail3='';
                        english_detail4='';

                        @if($course_question->questions_type=='arabic_and_english')
                            english_detail1='<input type="text" class="form-control" name="answers_text_en['+inputValues[i]+']" id="chose_multiple[0]" placeholder="{{Lang::get('main.enter').Lang::get('main.choice_en').' 1'}}">';
                            english_detail2='<input type="text" class="form-control" name="answers_text_en['+inputValues[i]+']" id="chose_multiple[1]" placeholder="{{Lang::get('main.enter').Lang::get('main.choice_en').' 2'}}">';
                            english_detail3='<input type="text" class="form-control" name="answers_text_en['+inputValues[i]+']" id="chose_multiple[2]" placeholder="{{Lang::get('main.enter').Lang::get('main.choice_en').' 3'}}">';
                            english_detail4='<input type="text" class="form-control" name="answers_text_en['+inputValues[i]+']" id="chose_multiple[3]" placeholder="{{Lang::get('main.enter').Lang::get('main.choice_en').' 4'}}">';
                        @endif

                        for (var i = 0; i < length; i++) {
                            html += '<div class="form-group col-lg-6"><label for="chose_multiple_'+(i)+'">{{ Lang::get('main.choice') }} '+(i+1)+'</label><div class="input-group"><span class="input-group-addon chose"><input type="checkbox" id="chose_multiple_'+(i)+'" '+choicesTruth[i]+' value="'+inputValues[i]+'" name="chose_multiple['+inputValues[i]+']"><label for="chose_multiple_'+(i)+'"><span></span></label></span><input type="text" class="form-control" name="answers_text['+inputValues[i]+']" value="'+choicesValues[i]+'" placeholder="{{Lang::get('main.enter_choice')}} '+(i+1)+'">'+english_detail1+'<span class="input-group-addon "><a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a></span></div></div>';
                        }
                    }
                    else if (type == 'chose_single_with_images') {
                        for(var i = 0; i < choicesName.length; i++){
                            choicesValues[i] = $(choicesName[i]).attr('src');
                        }
                        for (var i = 0; i < objects; i++) {
                            html += '<div class="form-group col-lg-6"> <label for="chose_'+(i)+'">{{ Lang::get('main.choice') }} '+(i+1)+'</label><div class="input-group" style="border: 1px solid #ccc;"><span class="input-group-addon chose"><input type="radio" id="chose_'+(i)+'" '+choicesTruth[i]+' value="'+inputValues[i]+'" name="chose_single"><label for="chose_'+(i)+'"><span></span></label></span><img class="choices_name" style="width:120px;height:100px;" src="'+choicesValues[i]+'"><input type="hidden" name="answers_images_edit['+inputValues[i]+']" value="'+choicesValues[i]+'"><input type="file" class="form-control answers_images change_image_choice" name="answers_images['+inputValues[i]+']" id="chose_'+(i)+'" placeholder="{{Lang::get('main.enter_choice')}} '+(i+1)+'"><span class="input-group-addon"><a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a></span></div></div>';
                        }
                    }
                    else if (type == 'chose_multiple_with_images') {
                        for(var i = 0; i < choicesName.length; i++){
                            choicesValues[i] = $(choicesName[i]).attr('src');
                        }

                        for (var i = 0; i < objects; i++) {
                            html += '<div class="form-group col-lg-6"><label for="chose_'+(i)+'">{{ Lang::get('main.choice') }} '+(i+1)+'</label><div class="input-group" style="border: 1px solid #ccc;"><span class="input-group-addon chose"><input type="checkbox" id="chose_'+(i)+'" '+choicesTruth[i]+' value="'+inputValues[i]+'" name="chose_multiple['+inputValues[i]+']"> <label for="chose_'+(i)+'"><span></span></label></span><img class="choices_name" style="width:120px;height:100px;" src="'+choicesValues[i]+'"><input type="hidden" name="answers_images_edit['+inputValues[i]+']" value="'+choicesValues[i]+'"><input type="file" class="form-control change_image_choice" name="answers_images['+inputValues[i]+']" id="chose_'+(i)+'" placeholder="{{Lang::get('main.enter_choice')}} '+(i+1)+'"><span class="input-group-addon "><a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a></span></div></div>';
                        }
                    }
                }

                $(New_Questions_wrapper).html(html);

                $('.change_image_choice').change(function() {
                    readURL(this);
                });

            });

            $(document).on('click', '.addNewQuestion', function (e) { //on add input button click
                e.preventDefault();
                var type = $("#" + $(this).data('id')).val();
                var id = $('#curriculum_name').val();

                $.ajax({
                    type: 'GET',
                    url: '{{URL("admin/courses_questions3")}}/' + type + '/' + id,
                    success: function(response) {
                        $('.new-question-message').hide();
                        $('.add-save-button').show();
                        $(New_Questions_wrapper).html(response['html']);
                    }
                });
            });

            $(document).on("click", ".remove_question", function (e) { //user click on remove text
                e.preventDefault();
                var id = $(this).closest('li').data("id");
                if(id == undefined) {
                    $(this).closest('.question-container').remove();
                    $('.add-save-button').hide();
                    $('.new-question-message').show();
                } else {
                    $(this).closest('li').remove();
                    $.ajax({
                        type: 'POST',
                        url: "{{ URL('admin/courses_questions3') }}/"+id,
                        data: {
                            _method: 'DELETE',
                            _token: token,
                            id: id
                        },
                        success: function(response) {
                        }
                    });
                }
                $('.alert-danger').hide();
                $('.alert-success').hide();
            });

            $(document).on("submit", "#addmodules_questionsEditForm", function (e) {
                e.preventDefault();
                var here = $(this);
                var course_id = here.parent().find('input[name="questions_course"]').val();
                var image_url = here.parent().find('input[name="image_url"]').val();
                var question_id = here.parent().find('input[name="question_id"]').val();
                $('.add-save-button').hide();
                $('.disabled-save-button').show();
                var dataForm = new FormData(this);
                if($('.answers_images').length != 0) {
                    var countImages = $('.answers_images').length;
                    dataForm.append('image_count', countImages);
                }
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    method: 'POST',
                    url: '{{URL("/admin/courses_questions3")}}/' + course_id,
                    data: dataForm,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if(!here.find('input[name="edit"]').length) {
                            var n = $('.overflow')[0].scrollHeight;
                            $('.overflow').animate({ scrollTop: n }, "slow");
                        }
                        $('#newQuestions').html('');
                        $('.add-save-button').hide();
                        $('.disabled-save-button').hide();
                        $('#currentErros').remove();
                        $('.alert-danger').hide();
                        $('.alert-success').show(300).delay(3000).hide();
                        $('.new-question-message').show(300);
                        $('#questionsContent').html(response['html']);
                        $('#questionsContent').show(300);
                    },
                    error: function(response) {
                        $('.add-save-button').show();
                        $('.disabled-save-button').hide();
                        $('#currentErros').remove();
                        html = '<ul id="currentErros">';
                        $.each(response.responseJSON, function(key, value) {
                            html += '<li>' + value + '</li>';
                        });
                        html += '</ul>';
                        $('.alert-danger').append(html);
                        $('.alert-danger').show();
                        $('html, body').animate({ scrollTop: 0 }, "slow");
                    }
                });
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
                                course_curriculum.append('<option value="">{{Lang::get('main.select')}} {{Lang::get('main.curriculum')}}</option>');
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
                                question_area.empty().append('<h5 style="margin-bottom: 20px; font-weight: bold;text-transform: capitalize">{{ Lang::get('main.select_questions_as_you_want') }}</h5>');
                                $.each(data, function (key, value2) {
                                    if (value2.type === 'true_false') {
                                        question_area.show().append('<div class="md-checkbox"><input data-json=\'' + JSON.stringify(value2) + '\' name="selectedQuestions"  data-type="' + value2.type + '" data-name="' + value2.name + '" type="checkbox" id="checkbox-' + value2.id + '" value="' + value2.id + '" class="md-check all"><label for="checkbox-' + value2.id + '"><span class="inc"></span><span class="check"></span><span class="box"></span> ' + value2.name + '</label><i style="color: #f0ad4e; margin-left: 5px;" class="fa fa-check"></i> </div>');
                                    }
                                    else if (value2.type === 'chose_single' || value2.type=='chose_single_with_images') {
                                        question_area.show().append('<div class="md-checkbox"><input data-json=\'' + JSON.stringify(value2) + '\' name="selectedQuestions" data-type="' + value2.type + '" data-name="' + value2.name + '" type="checkbox" id="checkbox-' + value2.id + '" value="' + value2.id + '" class="md-check all"><label for="checkbox-' + value2.id + '"><span class="inc"></span><span class="check"></span><span class="box"></span> ' + value2.name + '</label><i style="color: #337ab7; margin-left: 5px;" class="fa fa-check-square-o"></i> </div>');
                                    }
                                    else if (value2.type === 'chose_multiple' || value2.type === 'chose_multiple_with_images') {
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

            $('#importSelected').on('click', function (e) {
                $(this).attr("disabled", true);
                e.preventDefault();
                $('#first_opt').prop("selected", true);
                $('#select2-course_id-container').text('{{Lang::get('main.select')}} {{Lang::get('main.course')}}');
                var course_id = $('input[name="questions_course"]').val();
                var QuestionsIds = new Array();
                $.each($('input[name="selectedQuestions"]:checked'), function (index) {
                    QuestionsIds[index] = $(this).val();
                });
                var here = $(this);
                $.ajax({
                    type: 'POST',
                    url: '{{URL("admin/courses_questions3_fetch")}}',
                    data: {
                        _token: token,
                        course: course_id,
                        ids: QuestionsIds
                    },
                    success: function(response) {
                        here.closest('.modal').modal('toggle');
                        $('#questionsContent').html(response['html']);
                        $('#questionsContent').show(300);
                        var n = $('.overflow').height();
                        $('.overflow').animate({ scrollTop: n }, "slow");
                        $('#importSelected').attr("disabled", false);
                    }
                });
            });

            $('#searchQuestion').on('keyup', function () {
                $("#checkAllQuestion").prop('checked', false);
                var searchInput = $(this).val();
                $.ajax({
                    url: "{{URL("admin/search_question")}}",
                    type: "GET",
                    data: {_token: token, contains: searchInput, curriculum_id: course_curriculum.val()},
                    dataType: "json",
                    success: function (data) {
                        if (data != '{{Lang::get('main.no_search_results')}}') {
                            question_area.empty().append('<h5 style="margin-bottom: 20px; font-weight: bold;text-transform: capitalize">{{Lang::get('main.select_questions_as_you_want')}}</h5>');
                            $.each(data, function (key, value2) {
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
