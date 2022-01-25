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
                <a href="{{ URL('/admin/diploma_user_courses') }}">{{ Lang::get('main.diploma_user_courses') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.diploma_user_courses') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.diploma_user_courses') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/diploma_user_courses/'.$diploma_user_course->id,'class'=>"form-horizontal"]) !!}
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
                    <label for="exam">{{ Lang::get('main.exam') }}<span
                                class="required"> * </span></label>
                    <select name="exam" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.exam') }}</option>
                        <option @if($diploma_user_course->exam=='not exam') selected="selected" @endif value="not exam">not exam</option>
                        <option @if($diploma_user_course->exam=='pass') selected="selected" @endif value="pass">pass</option>
                        <option @if($diploma_user_course->exam=='fail') selected="selected" @endif value="fail">fail</option>
                    </select>
                </div>

                {{--<div class="form-group col-lg-3 text-center" style="margin-top:25px;">--}}
                    {{--<input @if($diploma_user_course->published=="yes") checked @endif type="checkbox" class="make-switch" name="published" data-size="small"--}}
                           {{--data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"--}}
                           {{--data-off-text="{{ Lang::get('main.unpublished') }}">--}}
                {{--</div>--}}

                <div class="form-group col-lg-12">
                    <label for="diploma">{{ Lang::get('main.diploma') }}<span
                                class="required"> * </span></label>
                    <select name="diploma" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.diploma') }}</option>
                        @foreach($diplomas as $id=>$name)
                            <option @if($diploma_user_course->diploma_id==$id) selected="selected" @endif value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-lg-12">
                    <label for="course">{{ Lang::get('main.course') }}<span
                                class="required"> * </span></label>
                    <select name="course" class="sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.course') }}</option>
                        @foreach($courses as  $id=>$name)
                            <option @if($diploma_user_course->course_id==$id) selected="selected" @endif value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="user">{{ Lang::get('main.user') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$user}}" id="user" name="user" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.user') }}">
                    </div>
                </div>
                <div id="users" class="col-lg-12"></div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="sort">{{ Lang::get('main.sort') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$diploma_user_course->sort}}" name="sort" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.sort') }}">
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
            $('.eventdate').datepicker({
                rtl: App.isRTL(),
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            $('#user').keyup(function(){
                var query=$(this).val();
                if(query!=''){
                    var token = "{{ csrf_token() }}";
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/session_courses_views/autoCompleteUsers') }}",
                        data: {query: query, _token: token},
                        success: function (data) {
                            $("#users").fadeIn();
                            $("#users").html(data);
                        }
                    })
                }
                else{
                    $('#users').fadeOut();
                }
            });
            $(document).on('click','#users-emails li',function(){
                $('#user').val($(this).text());
                $('#users').fadeOut();
            });
        });
    </script>
@endsection
