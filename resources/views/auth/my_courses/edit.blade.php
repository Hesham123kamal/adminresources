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
                <a href="{{ URL('/admin/my_courses') }}">{{ Lang::get('main.my_courses') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.my_courses') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.my_courses') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/my_courses/'.$my_course->id,'class'=>"form-horizontal"]) !!}
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
                           {{--@if($my_course->published=="yes") checked @endif data-size="small" data-on-color="success"--}}
                           {{--data-on-text="{{ Lang::get('main.published') }}" data-off-color="default" data-off-text="{{ Lang::get('main.unpublished') }}">--}}
                {{--</div>--}}
                <div id="users" class="col-lg-12"></div>
                <div class="form-group col-lg-9">
                    <label for="course">{{ Lang::get('main.course') }}<span
                                class="required"> * </span></label>
                    <select name="course" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.course') }}</option>
                        @foreach($courses as $id=>$name)
                            <option @if($my_course->course_id==$id) selected="selected" @endif value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                    <input type="checkbox" class="make-switch" name="old" value=1
                           @if($my_course->old==1) checked @endif data-size="small" data-on-color="success"
                           data-on-text="{{ Lang::get('main.old') }}" data-off-color="default" data-off-text="{{ Lang::get('main.new') }}">
                </div>
                <div class="form-group col-lg-12">
                    <label for="company">{{ Lang::get('main.company') }}</label>
                    <select name="company" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.company') }}</option>
                        @foreach($companies as $id=>$name)
                            <option @if($my_course->company_id==$id) selected="selected" @endif value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="expire_date">{{ Lang::get('main.expire_date') }}</label>
                    <div class="input-group date margin-bottom-5" data-date-format="yyyy-mm-dd">
                        <input type="text" value="{{$my_course->expire_date}}" class="form-control form-filter input-sm expire_date"
                               name="expire_date" placeholder="{{ Lang::get('main.expire_date') }}">
                        <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
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
                        url: "{{ URL('admin/my_courses/autoCompleteUsers') }}",
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

            $('.expire_date').datepicker({
                rtl: App.isRTL(),
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
        });
    </script>
@endsection