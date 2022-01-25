<?php
/**
 * Created by PhpStorm.
 * User: Mohammed.Hamza
 * Date: 18-Dec-18
 * Time: 02:54 PM
 */
?>

{{--{{ print_r(json_decode($post->custom_views_projects)).dd() }}--}}
@extends('auth.layouts.app')
@section('pageTitle')
    <title>{{ Lang::get('main.home_page_title') }}</title>
    <style>
        .progress {
            position: relative;
            width: 100%;
            height: 30px !important;
            border: 1px solid #7F98B2;
            padding: 1px;
            border-radius: 3px;
        }

        .bar {
            background-color: #B4F5B4;
            width: 0%;
            height: 25px;
            border-radius: 3px;
        }

        .percent {
            position: absolute;
            display: inline-block;
            top: 3px;
            left: 48%;
            color: #7F98B2;
        }

        input, textarea {
            text-align: right;
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
                <a href="{{ URL('/admin/courses_QandA') }}">{{ Lang::get('main.courses_QandA') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
            <li>
                <span>{{ $question->name }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.courses_QandA') }}
        <small>{{ Lang::get('main.edit') }}</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
@endsection
@section('content')

    <div class="row">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-users font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.courses_QandA') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/courses_QandA/'.$question->id,'id'=>'form','class'=>"form-horizontal",'files'=>true]) !!}
                <div class="form-body">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_error') }}
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_success') }}
                    </div>
                </div>

                <div id="message"></div>

                <div class="row">
                    <div class="form-group col-md-4" style="margin-right: 10px;">
                        <label for="usr">Course Name</label>
                        <input type="text" class="form-control" id="usr" value="{{$course->name}}" readonly>
                    </div>

                    <div class="form-group col-md-4" style="margin-right: 10px;">
                        <label for="usr">Section Name</label>
                        <input type="text" class="form-control" id="usr" value="{{$section->name}}" readonly>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="usr">User name</label>
                        <input type="text" class="form-control" id="usr" value="{{$user->FullName}}" readonly>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="question">{{ Lang::get('main.question') }} </label>
                        <textarea class="form-control" rows="5" id="question"
                                  readonly>{{ $question->question }}</textarea>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="answer">{{ Lang::get('main.answer') }} </label>
                        <textarea class="form-control" rows="5" id="answer" name="answer" placeholder="Add your answer">{{$question->answer}}</textarea>
                    </div>

                    @if($files)
                        <div class="form-group col-lg-12">
                            <label class="control-label">{{Lang::get('main.files')}}</label>
                            @foreach($files as $file)
                                @if(file_exists(coursesQuestionsFilePath().$file->file))
                                <div class="input-icon right">
                                    <i class="fa"></i>
                                    <a download target="_blank" href="{{ mainAssetURL('courses_questions/'.$file->file) }}">{{$file->file}} </a>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <div class="form-group col-lg-12 increment" >
                        <label class="control-label" for="files">{{ Lang::get('main.add') }} {{ Lang::get('main.files') }}</label><br>
                        <div class="control-group input-group" style="margin-top:10px">
                            <input type="file" name="files[]" class="form-control">
                            <div class="input-group-btn">
                                <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-plus"></i>Add</button>
                            </div>
                        </div>
                    </div>
                    <div class="clone hide">
                        <div class="form-group col-lg-12">
                            <div class="control-group input-group" style="margin-top:5px; margin-bottom: 5px;">
                                <input type="file" name="files[]" class="form-control">
                                <div class="input-group-btn">
                                    <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="clearfix"></div>
                <div class="text-center col-lg-12">
                    <button type="submit" class="btn green">{{ Lang::get('main.save') }}</button>
                </div>
                <div class="clearfix"></div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@section('scriptCode')
    {{--<script>--}}
    {{--$(document).ready(function () {--}}
    {{--var bar = $('.bar');--}}
    {{--var percent = $('.percent');--}}
    {{--var message = $('#message');--}}

    {{--$('form').ajaxForm({--}}
    {{--//beforeSubmit: validate,--}}
    {{--beforeSend: function() {--}}
    {{--if( $('#book').val()!='') {--}}
    {{--message.empty();--}}
    {{--var percentVal = '0%';--}}
    {{--var posterValue = $('input[name=file]').fieldValue();--}}
    {{--bar.width(percentVal);--}}
    {{--percent.html(percentVal);--}}
    {{--}--}}
    {{--},--}}
    {{--uploadProgress: function(event, position, total, percentComplete) {--}}
    {{--if( $('#book').val()!='') {--}}
    {{--var percentVal = percentComplete + '%';--}}
    {{--bar.width(percentVal);--}}
    {{--percent.html(percentVal);--}}
    {{--}--}}
    {{--},--}}
    {{--success: function() {--}}
    {{--if( $('#book').val()!='') {--}}
    {{--var percentVal = 'Completed';--}}
    {{--bar.width(percentVal);--}}
    {{--percent.html(percentVal);--}}
    {{--}--}}
    {{--},--}}
    {{--complete: function(xhr) {--}}
    {{--message.html(xhr.responseJSON);--}}
    {{--$("html, body").animate({ scrollTop: 0 });--}}
    {{--return false;--}}
    {{--}--}}
    {{--});--}}
    {{--});--}}
    {{--</script>--}}
    <script type="text/javascript">
        $(document).ready(function() {
            $(".btn-success").click(function(){
                var lsthmtl = $(".clone").html();
                $(".increment").after(lsthmtl);
            });
            $("body").on("click",".btn-danger",function(){
                $(this).parent().parent().parent().remove();
            });
        });
    </script>
@endsection