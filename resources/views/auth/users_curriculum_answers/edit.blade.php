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
                <a href="{{ URL('/admin/users_curriculum_answers') }}">{{ Lang::get('main.users_curriculum_answers') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.users_curriculum_answers') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.users_curriculum_answers') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/users_curriculum_answers/'.$answer->id,'class'=>"form-horizontal"]) !!}
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
                <div class="form-group col-lg-12">
                    <label class="control-label" for="user">{{ Lang::get('main.user') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$user}}" id="user" name="user" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.user') }}">
                    </div>
                </div>
                {{--<div class="form-group col-lg-3 text-center" style="margin-top:25px;">--}}
                    {{--<input type="checkbox" class="make-switch" name="published" value="yes"--}}
                           {{--@if($answer->published=="yes") checked @endif data-size="small" data-on-color="success"--}}
                           {{--data-on-text="{{ Lang::get('main.published') }}" data-off-color="default" data-off-text="{{ Lang::get('main.unpublished') }}">--}}
                {{--</div>--}}
                <div id="users" class="col-lg-12"></div>

                <div class="form-group col-lg-12">
                    <label for="course">{{ Lang::get('main.course') }}<span
                                class="required"> * </span></label>
                    <select name="course" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.course') }}</option>
                        @foreach($courses as $id=>$name)
                            <option @if($answer->course_id==$id) selected="selected" @endif value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-12">
                    <label for="curriculum_id">{{ Lang::get('main.curriculum_id') }}<span
                                class="required"> * </span></label>
                    <select name="curriculum_id" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.curriculum_id') }}</option>
                        @foreach($curriculums_ids as $id)
                            <option @if($answer->curriculum_id==$id) selected="selected" @endif value="{{$id}}">{{$id}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="session_user_id">{{ Lang::get('main.session_user_id') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control"  value="{{$answer->session_user_id}}" name="session_user_id" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.session_user_id') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label for="curriculum_type">{{ Lang::get('main.curriculum_type') }}<span
                                class="required"> * </span></label>
                    <select name="curriculum_type" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.curriculum_type') }}</option>
                        <option @if($answer->curriculum_type=='exam') selected="selected" @endif value="exam">exam</option>
                        <option @if($answer->curriculum_type=='training') selected="selected" @endif value="training">training</option>
                    </select>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="right_answers">{{ Lang::get('main.right_answers') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$answer->right_answers}}" name="right_answers" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.right_answers') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="wrong_answers">{{ Lang::get('main.wrong_answers') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$answer->wrong_answers}}" name="wrong_answers" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.wrong_answers') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="duration_time">{{ Lang::get('main.duration_time') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$answer->duration_time}}" name="duration_time" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.duration_time') }}">
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
    <script>
        $(document).ready(function () {
            $('#user').keyup(function(){
                var query=$(this).val();
                if(query!=''){
                    var token = "{{ csrf_token() }}";
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/users_curriculum_answers/autoCompleteUsers') }}",
                        data: {query: query, _token: token},
                        success: function (data) {
                            $("#users").fadeIn();
                            $("#users").html(data);
                        }
                    })
                }
                else{
                    $("#users").fadeOut();
                }
            });
            $(document).on('click','#users-emails li',function(){
                $('#user').val($(this).text());
                $('#users').fadeOut();
            });

        });
    </script>
@endsection