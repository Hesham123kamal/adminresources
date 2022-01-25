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
        .question_body:not(:first-child) {
            margin-top: 30px;
        }
        .question_body:not(:last-child) {
            border-bottom: 1px solid #D3D3D3;
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
                <a href="{{ URL('/admin/modules_trainings_questions2') }}">{{ Lang::get('main.modules_trainings_questions') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
            <li>
                <span>{{ $trainings->name }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
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
                <div class="modal-body">
                    <div class="form-group form-group-select2">
                        <label for="training_id">{{ Lang::get('main.training_name') }}</label>
                        <select style="width: 100%" id="training_id" class="sel2" name="training_id">
                            <option value="" id="first_opt">{{ Lang::get('main.select') }} {{ Lang::get('main.training') }}</option>
                            @foreach($allTrainings as $training)
                                <option value="{{ $training->id }}">{{ $training->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row checkAllQuestion"
                         style="display: none;box-shadow: 0 7px 7px -6px #999; margin-bottom: 5px; padding-bottom: 10px;">
                        <div class="col-sm-6">
                            <span style="margin-left: 19px;">{{ Lang::get('main.true_or_false') }}
                            <i style="color: #f0ad4e;" class="fa fa-check"></i></span>
                            <span>{{ Lang::get('main.single_choice') }}
                            <i style="color: #337ab7;" class="fa fa-check-square-o"></i></span>
                            <span>{{ Lang::get('main.multiple_choices') }}
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
                                       placeholder="{{ Lang::get('main.search') }}">
                            </div>
                        </div>
                    </div>

                    <div class="questions-area"
                         style="display: none;height: 230px; overflow-x: hidden; overflow-y: scroll;padding: 5px 19px;position: relative;z-index: 55">
                        <div class="overlay">
                            <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
                            <span class="sr-only">{{ Lang::get('main.loading') }}</span>
                        </div>
                        <h5 style="margin-bottom: 20px; font-weight: bold;text-transform: capitalize">{{ Lang::get('main.select_questions_as_you_want') }} </h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeModal" data-dismiss="modal">{{ Lang::get('main.cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="importSelected">{{ Lang::get('main.import_selected') }}</button>
                    <button type="button" class="btn btn-primary" id="importingSelected" style="display: none;" disabled>{{ Lang::get('main.loading') }}</button>
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
    <div class="portlet light col-md-6 bordered">
        <div class="portlet-title">
            <div class="caption font-dark">
                <i class="icon-modules_trainings_questions font-dark"></i>
                <span class="caption-subject bold uppercase">{{ Lang::get('main.new_question') }}</span>
            </div>
            <div class="tools"></div>
        </div>
        <div class="portlet-body" style="overflow-y: auto;">
            {!! Form::open(['method'=>'PUT','url'=>'admin/modules_trainings_questions2/'.$trainings->id,'id'=>'addmodules_trainings_questionsEditForm']) !!}
            <input type="hidden" name="module_id" value="{{$modules->id}}">
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
                    <select style="width: 100%" id="module_id_question" class="sel2" name="module_id" disabled>
                        @foreach($allModules as $module)
                            <option @if($modules->id == $module->id) selected="selected" @endif value="{{ $module->id }}">{{ $module->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group form-group-select2 col-lg-12">
                    <label for="training_id">{{ Lang::get('main.training_name') }}</label>
                    <select style="width: 100%" id="training_id_question" class="sel2" name="training_id" disabled>
                        @foreach($allTrainings as $training)
                            <option @if($trainings->id == $training->id) selected="selected" @endif value="{{ $training->id }}">{{ $training->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-lg-6">
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
                    <button data-id="type-1" style="margin-top: 2px;" class="btn btn-primary addNewQuestion"><i class="glyphicon glyphicon-plus"></i></button>
                </div>
                <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target=".import-course-question-modal" style="margin-top: 25px;">
                    {{ Lang::get('main.import_questions') }}
                </button>
                <div class="clearfix"></div>
                <div class="new-question-message text-center" style="margin: 10px auto;">
                    <p class="bg-warning" style="padding: 15px;">{{ Lang::get('main.add_or_update_question') }}</p>
                </div>
                <div class="" id="newQuestions">

                </div>
            </div>
            <div class="clearfix"></div>
            <div class="text-center" style="margin-top: 30px">
                <button type="submit" class="btn btn-primary add-save-button" id="btnAction" name="btnAction" style="padding: 10px 40px; display: none;">{{ Lang::get('main.save') }}</button>
                <button class="btn btn-success disabled-save-button" type="submit" style="padding: 10px 60px; display: none;border-radius:8px !important;" disabled>{{ Lang::get('main.loading') }}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <div class="portlet light col-md-6 bordered">
        <div class="portlet-title">
            <div class="caption font-dark">
                <i class="icon-modules_trainings_questions font-dark"></i>
                <span class="caption-subject bold uppercase">{{ Lang::get('main.modules_trainings_questions') }}</span>
            </div>
            <div class="tools"></div>
        </div>
        <div class="portlet-body overflow" id="questionsContent" style="overflow: auto;">
            @include('auth.modules_trainings_questions2.questions.question_body', ['modules_trainings_questions' => $modules_trainings_questions])
        </div>
    </div>
</div>
@endsection
@section('scriptCode')
    <script>
        $(document).ready(function () {
            $('.menu-toggler').trigger( "click" );
            $('#questionsContent').height($(window).height() - ($('.page-header').height() + $('.page-bar').height() + $('.page-footer').height() + 80));
            $(".sel2").select2();
            var token = '{{ csrf_token() }}';
            var wrapper_times = $("#questionsContent"); //title Fields wrapper
            var New_Questions_wrapper = $("#newQuestions");
            var add_button_times = $(".addNewQuestion"); //title Add button ID

            $(document).on('click', '.removeAnswer', function (e) {
                e.preventDefault();
                $(this).parent().parent().parent().remove();
            });

            $(document).on('click', '.addNewQuestion', function (e) { //on add input button click
                e.preventDefault();
                var type = $("#" + $(this).data('id')).val();
                var id = $('#module_id_question').val();

                $.ajax({
                    type: 'GET',
                    url: '{{URL("admin/modules_trainings_questions2")}}/' + type + '/' + id,
                    success: function(response) {
                        $('.new-question-message').hide();
                        $('.add-save-button').show();
                        $(New_Questions_wrapper).html(response['html']);
                    }
                });
            });

            $(document).on('click', '.edit_question', function (e) { //on add input button click
                $('.new-question-message').hide();
                $('.add-save-button').show();
                $("html, body").animate({ scrollTop: 0 }, "slow");
                var here = $(this);
                var type = here.data('type');

                var question_id = here.closest('li').find('input[name="question_id"]').val();

                var question_arCont = here.closest('li').find('p:eq(0)').html();
                var question_enCont = here.closest('li').find('p:eq(1)').html();
                var question_diffeculty = here.closest('li').find('span.difficulty').html();
                var chosen1 , chosen2, chosen3;
                if(question_diffeculty=="easy") {chosen1 = "selected"}
                if(question_diffeculty=="normal") {chosen2 = "selected"}
                if(question_diffeculty=="hard") {chosen3 = "selected"}
                var length = here.closest('li').find('.choices_ar').length;
                var html = '<div class="question-container"><input type="hidden" name="edit" value="'+question_id+'" />';

                if (type != 'true_false') {
                    html += '<div class="col-md-12">';
                }

                html += '<div class="form-group col-md-6"><label for="question_ar">{{ Lang::get("main.question") }}</label><input type="text" class="form-control" name="question_ar" value="'+question_arCont+'" placeholder="{{ Lang::get("main.enter").Lang::get("main.question_ar") }}"><input type="text" class="form-control" name="question_en" value="'+question_enCont+'" placeholder="{{ Lang::get("main.enter").Lang::get("main.question_en") }}"><input type="hidden" name="type" value="'+type+'"></div><div class="col-lg-3" style="margin-top:24px;"><label for="difficulty_type">{{ Lang::get("main.difficulty_type") }}</label><select style="width:100%" class="sel2" id="difficulty_type" name="difficulty"><option value="easy" '+chosen1+'>{{ Lang::get("main.easy") }}</option><option value="normal" '+chosen2+'>{{ Lang::get("main.normal") }}</option><option value="hard" '+chosen3+'>{{ Lang::get("main.hard") }}</option></select></div>';

                if (type != 'true_false') {
                    html += '<div class="form-group text-center col-md-1 col-md-offset-2" style="padding: 0;"><button class="btn btn-danger col-lg-1 col-lg-offset-2 remove_question" data-type="'+type+'" style="margin-top: 24px;height: 40px;width: 44px;border-radius: 50% !important;font-size: 18px;"><i class="glyphicon glyphicon-remove"></i></button></div></div>';
                }
                if (type == 'true_false') {
                    var trueChoice = here.closest('li').find('.true input').attr('checked') == undefined ? '' : 'checked';
                    var falseChoice = here.closest('li').find('.false input').attr('checked') == undefined ? '' : 'checked';

                    html += '<div class="form-group text-center col-md-2" style="margin-top:20px;padding: 0;"><div class="true"><input type="radio" name="answers" '+trueChoice+' value="1" id="question_true"> <label for="question_true"><span></span></label></div><div class="false"> <input type="radio" name="answers" '+falseChoice+' value="0" id="question_false"><label for="question_false"><span></span></label></div></div><button class="btn btn-danger col-lg-1 remove_question" data-type="'+type+'" style="margin-top: 24px;height: 40px;width: 44px;border-radius: 50% !important;font-size: 18px;"><i class="glyphicon glyphicon-remove"></i></button>';

                }
                else {
                    var choicesAr = here.closest('li').find('.choices_ar');
                    var choicesEn = here.closest('li').find('.choices_en');
                    var choicesArValues = new Array();
                    var choicesEnValues = new Array();
                    choicesRadio = here.closest('li').find('.choices_values');
                    var choicesTruth = new Array();
                    var inputArNames = new Array();
                    var inputEnNames = new Array();
                    var inputValues = new Array();
                    for(var i = 0; i < choicesRadio.length; i++){
                        choicesTruth[i] = $(choicesRadio[i]).attr('checked') == undefined? '' : 'checked';
                        inputValues[i] = $(choicesRadio[i]).val();
                    }
                    if (type == 'chose_single') {
                        for(var i = 0; i < choicesAr.length; i++){
                            choicesArValues[i] = $(choicesAr[i]).val();
                            inputArNames[i] = $(choicesAr[i]).attr('name');
                        }
                        for(var i = 0; i < choicesEn.length; i++){
                            choicesEnValues[i] = $(choicesEn[i]).val();
                            inputEnNames[i] = $(choicesEn[i]).attr('name');
                        }

                        for (var i = 0; i < length; i++) {
                            html += '<div class="form-group col-lg-6"><label for="chose_single_'+(i)+'">Choice '+(i+1)+'</label><div class="input-group"><span class="input-group-addon chose"><input type="radio" id="chose_'+(i)+'" value="'+(inputValues[i])+'" '+choicesTruth[i]+' name="chose_single"><label for="chose_'+i+'"><span></span></label></span><input type="text" class="form-control choices_ar" name="'+inputArNames[i]+'" value="'+choicesArValues[i]+'"><input type="text" class="form-control choices_en" name="'+inputEnNames[i]+'" value="'+choicesEnValues[i]+'"><span class="input-group-addon"><a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a></span></div></div> ';
                        }
                    }
                    else if (type == 'chose_multiple') {
                        for(var i = 0; i < choicesAr.length; i++){
                            choicesArValues[i] = $(choicesAr[i]).val();
                            inputArNames[i] = $(choicesAr[i]).attr('name');
                        }
                        for(var i = 0; i < choicesEn.length; i++){
                            choicesEnValues[i] = $(choicesEn[i]).val();
                            inputEnNames[i] = $(choicesEn[i]).attr('name');
                        }

                        for (var i = 0; i < length; i++) {
                            html += '<div class="form-group col-lg-6"><label for="chose_multiple_'+(i)+'">Choice '+(i+1)+'</label><div class="input-group"><span class="input-group-addon chose"><input type="checkbox" id="chose_'+(i)+'" value="'+inputValues[i]+'" '+choicesTruth[i]+' name="chose_multiple['+inputValues[i]+']"><label for="chose_'+i+'"><span></span></label></span><input type="text" class="form-control choices_ar" name="'+inputArNames[i]+'" value="'+choicesArValues[i]+'"><input type="text" class="form-control choices_en" name="'+inputEnNames[i]+'" value="'+choicesEnValues[i]+'"><span class="input-group-addon"><a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a></span></div></div> ';
                        }
                    }
                }

                $(New_Questions_wrapper).html(html);

            });

            $(document).on("click", ".remove_question", function (e) { //user click on remove text
                e.preventDefault();
                var id = $(this).closest('li').data("id");
                var training_id = $('#training_id_question').val();
                if(id == undefined) {
                    $(this).closest('.question_body').remove();
                    $(this).closest('.question-container').remove();
                    $('.add-save-button').hide();
                    $('.new-question-message').show();
                } else {
                    $(this).closest('li').remove();
                    $.ajax({
                        type: 'POST',
                        url: "{{ URL('admin/modules_trainings_questions2') }}/"+training_id,
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

                if($('#newQuestions').find('input[name="edit"]').val() == id) {
                    $('#newQuestions').html('');
                    $('.add-save-button').hide();
                    $('.new-question-message').show();
                }
            });

            $(document).on('submit', '#addmodules_trainings_questionsEditForm', function (e) {
                e.preventDefault();
                var id = $('#training_id_question').val();
                var here = $(this);
                $('.add-save-button').hide();
                $('.disabled-save-button').show();
                var dataForm = new FormData(this);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    method: 'POST',
                    url: '{{URL("admin/modules_trainings_questions2")}}/' + id,
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

            $(document).on('change', '#selectAll', function () {
                if ($(this).is(':checked')) {
                    $(".questionsCheckBox").prop('checked', true);
                } else {
                    $(".questionsCheckBox").prop('checked', false);
                }
            });

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
                    } else if (type == 'chose_multiple') {
                        dataHTML = '<div class=""> <div class="form-group col-lg-10"> <label for="questions_name_' + questions + '">{{ Lang::get('main.question') }}</label> <input type="text" class="form-control" name="questions[name][' + questions + ']" id="questions_name_' + questions + '" value="' + name + '" placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}"> <input type="hidden" name="questions[type][' + questions + ']" value="chose_multiple"> </div> <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_multiple" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button>';
                        xx = 1;
                        details.forEach(function (dd) {
                            console.log('dd');
                            console.log(dd);
                            dataHTML += '<div class="form-group col-lg-6"><label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_' + xx + '">{{ Lang::get('main.choice') }} 1</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="checkbox" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_' + xx + '" ' + ((dd.answer) ? 'checked="checked"' : '') + ' value="1" name="chose_question_answer[' + questions + '][' + (x - 1) + ']"> <label for="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_' + xx + '"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][' + questions + '][]" value="' + dd.name + '" id="chose_multiple_' + questions + '_' + choiceMultipleAnswersCount + '_' + xx + '" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice') }} 1"> </div> </div> ';
                            xx++;
                        });
                        dataHTML += '</div>';
                        $(wrapper_times).append(dataHTML);//add input box
                    } else if (type == 'chose_single') {
                        dataHTML = '<div class=""> <div class="form-group col-lg-10"> <label for="questions_name_' + questions + '">{{ Lang::get('main.question') }}</label> <input type="text" class="form-control" name="questions[name][' + questions + ']" id="questions_name_' + questions + '" value="' + name + '" placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}"> <input type="hidden" name="questions[type][' + questions + ']" value="chose_single"> </div> <button class="btn btn-danger col-lg-2 remove_question" data-type="chose_single" style="margin-top: 24px;"><i class="glyphicon glyphicon-trash"></i></button> ';
                        xx = 1;
                        details.forEach(function (dd) {
                            dataHTML += '<div class="form-group col-lg-6"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_' + xx + '">{{ Lang::get('main.choice') }} 1</label> <div class="input-group"> <span class="input-group-addon chose"> <input type="radio" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_' + xx + '" ' + ((dd.answer) ? 'checked="checked"' : '') + ' value="' + xx + '" name="chose_question_answer[' + questions + ']"> <label for="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_' + xx + '"> <span></span> </label> </span> <input type="text" class="form-control" name="questions[answers][' + questions + '][]" value="' + dd.name + '" id="chose_single_' + questions + '_' + choiceSingleAnswersCount + '_' + xx + '" placeholder="{{ Lang::get('main.enter').Lang::get('main.choice') }} 1"> <span class="input-group-addon "> <a href="#" class="removeAnswer"><i class="glyphicon glyphicon-trash"></i></a> </span> </div> </div> ';
                            xx++;
                        });
                        dataHTML += '</div>';
                        $(wrapper_times).append(dataHTML);//add input box
                    }
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
            var training_questions = $('#training_id');
            training_questions.on('change', function () {
                $("#checkAllQuestion").prop('checked', false);
                question_area.show().append('<div class="overlay"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">{{ Lang::get('main.loading') }}</span></div>');
                var trainingID = $(this).val();
                if (trainingID) {
                    $.ajax({
                        url: "{{URL("admin/get_module_training_questions2")}}/" + trainingID,
                        type: "GET",
                        data: {_token: token},
                        dataType: "json",
                        success: function (data) {
                            if (data) {
                                if (data.length !== 0) {
                                    question_area.empty().append('<h5 style="margin-bottom: 20px; font-weight: bold;text-transform: capitalize">{{ Lang::get('main.select_questions_as_you_want') }}</h5>');
                                    $.each(data, function (key, value2) {
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
                                    question_area.empty().append('<h3>{{ Lang::get('main.no_questions_found') }}</h3>');
                                }
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
                e.preventDefault();
                //$('#importSelected').hide();
                $(this).attr('disabled',true);
                $('#importingSelected').show();
                var module_id = $('#module_id_question').val();
                var training_id = $('#training_id_question').val();
                var QuestionsIds = new Array();
                $.each($('input[name="selectedQuestions"]:checked'), function (index) {
                    QuestionsIds[index] = $(this).val();
                });
                var here = $(this);
                $.ajax({
                    type: 'POST',
                    url: '{{URL("admin/module_training_questions2_fetch")}}',
                    data: {
                        _token: token,
                        module: module_id,
                        training: training_id,
                        ids: QuestionsIds
                    },
                    success: function(response) {
                        here.closest('.modal').modal('toggle');
                        $('#questionsContent').html(response['html']);
                        $('#questionsContent').show(300);
                        //$('#importSelected').show();
                        $('#importSelected').attr('disabled',false);
                        $('#importingSelected').hide();
                        var n = $('.overflow')[0].scrollHeight;
                        $('.overflow').animate({ scrollTop: n }, "slow");
                    }
                });
            });

            $('#searchQuestion').on('keyup', function () {
                $("#checkAllQuestion").prop('checked', false);
                var searchInput = $(this).val();
                $.ajax({
                    url: "{{URL("admin/search_module_training_question2")}}",
                    type: "GET",
                    data: {_token: token, contains: searchInput, training_id: training_questions.val()},
                    dataType: "json",
                    success: function (data) {
                        console.log(data);
                        if (data != 'No search result') {
                            console.log(data);
                            question_area.empty().append('<h5 style="margin-bottom: 20px; font-weight: bold;text-transform: capitalize">{{ Lang::get('main.select_questions_as_you_want') }}</h5>');
                            $.each(data, function (key, value2) {
                                console.log(key);
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
