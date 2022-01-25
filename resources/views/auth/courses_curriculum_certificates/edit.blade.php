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
                <a href="{{ URL('/admin/courses_curriculum_certificates') }}">{{ Lang::get('main.courses_curriculum_certificates') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.courses_curriculum_certificates') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.courses_curriculum_certificates') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/courses_curriculum_certificates/'.$certificate->id,'class'=>"form-horizontal"]) !!}
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
                           {{--@if($certificate->published=="yes") checked @endif data-size="small" data-on-color="success"--}}
                           {{--data-on-text="{{ Lang::get('main.published') }}" data-off-color="default" data-off-text="{{ Lang::get('main.unpublished') }}">--}}
                {{--</div>--}}
                <div id="users" class="col-lg-12"></div>

                <div class="form-group col-lg-12">
                    <label for="course">{{ Lang::get('main.course') }}<span
                                class="required"> * </span></label>
                    <select name="course" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.course') }}</option>
                        @foreach($courses as $id=>$name)
                            <option @if($certificate->course_id==$id) selected="selected" @endif value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-12">
                    <label for="curriculum_id">{{ Lang::get('main.curriculum_id') }}<span
                                class="required"> * </span></label>
                    <select name="curriculum_id" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.curriculum_id') }}</option>
                        @foreach($curriculums_ids as $id)
                            <option @if($certificate->curriculum_id==$id) selected="selected" @endif value="{{$id}}">{{$id}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="users_curriculum_answer_id">{{ Lang::get('main.users_curriculum_answer_id') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$certificate->users_curriculum_answer_id}}" id="users_curriculum_answer_id" name="users_curriculum_answer_id" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.users_curriculum_answer_id') }}">
                    </div>
                </div>
                <div id="users_curriculum_answer_ids" class="col-lg-12"></div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="session_user_id">{{ Lang::get('main.session_user_id') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control"  value="{{$certificate->session_user_id}}" name="session_user_id" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.session_user_id') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="serial_number">{{ Lang::get('main.serial_number') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$certificate->serial_number}}" name="serial_number" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.serial_number') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="user_name">{{ Lang::get('main.user_name') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$certificate->user_name}}" name="user_name" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.user_name') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="user_name_en">{{ Lang::get('main.user_name_en') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$certificate->user_name_en}}" name="user_name_en" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.user_name_en') }}">
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
                        url: "{{ URL('admin/courses_curriculum_certificates/autoCompleteUsers') }}",
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

            $('#users_curriculum_answer_id').keyup(function(){
                var query=$(this).val();
                if(query!=''){
                    var token = "{{ csrf_token() }}";
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/courses_curriculum_certificates/autoCompleteAnswersIds') }}",
                        data: {query: query, _token: token},
                        success: function (data) {
                            $("#users_curriculum_answer_ids").fadeIn();
                            $("#users_curriculum_answer_ids").html(data);
                        }
                    })
                }
                else{
                    $("#users_curriculum_answer_ids").fadeOut();
                }
            });
            $(document).on('click','#answers-ids li',function(){
                $('#users_curriculum_answer_id').val($(this).text());
                $('#users_curriculum_answer_ids').fadeOut();
            });

        });
    </script>
@endsection