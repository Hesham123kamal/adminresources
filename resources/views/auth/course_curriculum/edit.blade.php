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
                <a href="{{ URL('/admin/course_curriculum') }}">{{ Lang::get('main.course_curriculum') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.course_curriculum') }}
        <small>{{ Lang::get('main.edit') }}</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
@endsection
@section('content')
    <style>
        .form-group{
            margin-left: 0px !important;
            margin-right: 0px !important;
        }
    </style>

    <div class="row">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-users font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.course_curriculum') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/course_curriculum/'.$curriculum->id]) !!}
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
                <div id="messages"></div>
                <div class="form-group col-lg-4">
                    <label for="company">{{ Lang::get('main.course') }}<span
                                class="required"> * </span></label>
                    <select name="course" id="course" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.course') }}</option>
                        @foreach($courses as $id=>$value)
                            <option @if($id==$curriculum->course_id) selected="selected"
                                    @endif value="{{$id}}">{{$value}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <label for="company">{{ Lang::get('main.section') }}<span
                                class="required"> * </span></label>
                    <select name="section" id="section" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.section') }}</option>
                        @foreach($sections as $id=>$value)
                            <option @if($id==$curriculum->section_id) selected="selected"
                                    @endif value="{{$id}}">{{$value}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-lg-4 text-center" style="margin-top:25px;">
                    <input @if($curriculum->published=="yes") checked @endif type="checkbox" class="make-switch" name="published" data-size="small"
                           data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"
                           data-off-text="{{ Lang::get('main.unpublished') }}">
                </div>

                <div class="form-group col-lg-6">
                    <label class="control-label" for="name">{{ Lang::get('main.name') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$curriculum->name}}" id="name" name="name" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label class="control-label" for="description">{{ Lang::get('main.description') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$curriculum->description}}" id="description" name="description" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.description') }}">
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label for="questions_type">{{ Lang::get('main.question_type') }}<span
                                class="required"> * </span></label>
                    <select name="questions_type" id="questions_type" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.question_type') }}</option>
                        <option @if($curriculum->questions_type=='arabic_or_english') selected="selected" @endif value="arabic_or_english">{{ Lang::get('main.arabic_or_english') }}</option>
                        <option @if($curriculum->questions_type=='arabic_and_english') selected="selected" @endif value="arabic_and_english">{{ Lang::get('main.arabic_and_english') }}</option>
                    </select>
                </div>
                <div class="form-group col-lg-6">
                    <label for="type">{{ Lang::get('main.type') }}<span
                                class="required"> * </span></label>
                    <select id="type" name="type" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.type') }}</option>
                        <option @if($curriculum->type=='default')  selected="selected" @endif value="default">Default</option>
                        <option @if($curriculum->type=='exam')  selected="selected" @endif value="exam">Exam</option>
                        <option @if($curriculum->type=='training')  selected="selected" @endif value="training">Training</option>
                    </select>
                </div>
                <div id="question_time" class="form-group col-lg-12">
                    <label class="control-label" for="question_time">{{ Lang::get('main.question_time') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$curriculum->question_time}}"  name="question_time" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.question_time') }}">
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label for="type">{{ Lang::get('main.language') }}<span
                                class="required"> * </span></label>
                    <select name="language" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.language') }}</option>
                        <option @if($curriculum->language=='english')  selected="selected" @endif value="english">English</option>
                        <option @if($curriculum->language=='arabic')  selected="selected" @endif value="arabic">Arabic</option>
                    </select>
                </div>
                <div class="form-group col-lg-6">
                    <label class="control-label" for="questions_numbers">{{ Lang::get('main.questions_numbers') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$curriculum->questions_numbers}}" id="questions_numbers" name="questions_numbers" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.questions_numbers') }}">
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label class="control-label" for="sort">{{ Lang::get('main.sort') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$curriculum->sort}}" id="sort" name="sort" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.sort') }}">
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label class="control-label" for="audio_link">{{ Lang::get('main.audio_link') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$curriculum->audio_link}}" id="audio_link" name="audio_link" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.audio_link') }}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="duration">{{ Lang::get('main.duration') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$curriculum->duration}}" id="duration" name="duration" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.duration') }}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="link">{{ Lang::get('main.link') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$curriculum->link}}" id="link" name="link" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.link') }}">
                    </div>
                </div>

                <div class="form-group col-lg-4 text-center" style="margin-top:25px;">
                    <input @if($curriculum->isfree=='yes') checked @endif type="checkbox" class="make-switch" name="isfree"  data-size="small"
                           data-on-color="success" data-on-text="{{Lang::get('main.free') }}" data-off-color="default"
                           data-off-text="{{Lang::get('main.notfree') }}">
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
    <script>
        $(document).ready(function(){
            @if($curriculum->type!='exam')
            $('#question_time').hide();
            @endif
            $('#type').change(function(){
                if($(this).val()=='exam'){
                    $('#question_time').show();
                }
                else{
                    $('#question_time').hide();

                }
            })

            $('#course').change(function(){
                $.ajax({
                    type: "POST",
                    url: "{{ URL('admin/course_curriculum/getSectionsByCourseId') }}",
                    data: {"course_id": $(this).val(),"_token": "{{ csrf_token() }}"},
                    success: function(options){
                        $('#section').empty().append(options);
                    }
                });
            })

        })

    </script>
@endsection