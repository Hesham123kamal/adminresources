<?php
/**
 * Created by PhpStorm.
 * User: Mohammed.Hamza
 * Date: 18-Dec-18
 * Time: 02:54 PM
 */
?>
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
                <a href="{{ URL('/admin/mba_courses_user_plan') }}">{{ Lang::get('main.mba_courses_user_plan') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.mba_courses_user_plan') }}
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
                    <i class="icon-users font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.mba_courses_user_plan') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/mba_courses_user_plan','id'=>'addModulesForm','files'=>true]) !!}
                <div class="form-body">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_error') }}
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_success') }}
                    </div>
                    <div id="messages"></div>

                    <div class="form-group col-lg-6">
                        <label class="control-label" for="module_name">{{ Lang::get('main.module_name') }} <span
                                    class="required"> * </span></label>
                        <select id="module_name" class="form-control sel2 module_name" required name="module_name">
                            <option value=" ">{{ Lang::get('main.select') }} {{ Lang::get('main.module') }}</option>
                            @foreach($modules as $module)
                                <option @if(old('module_name')==$module->id) selected="selected" @endif value="{{$module->id}}">{{$module->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-6">
                        <label class="control-label" for="related_course">{{ Lang::get('main.related_course') }} <span
                                    class="required"> * </span></label>
                        <select id="related_course" class="form-control sel2 module_name" required name="related_course">
                            <option value=" ">{{ Lang::get('main.select') }} {{ Lang::get('main.course') }}</option>
                            @foreach($courses as $course)
                                <option @if(old('related_course')==$course->id) selected="selected" @endif value="{{$course->id}}">{{$course->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-9">
                        <label class="control-label" for="name">{{ Lang::get('main.name') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{ old('name') }}" id="name" name="name" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                        <input type="checkbox" class="make-switch" name="active" value="yes" checked data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.active') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.inActive') }}">
                    </div>


                    {{--@include('auth/description')--}}

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="pic">{{ Lang::get('main.pic') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="file" class="form-control" value="" id="pic" accept="image/*"
                                   name="pic" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.pic') }}">
                        </div>
                    </div>

                   {{-- <div class="form-group col-lg-6">
                        <label for="duetime">{{ Lang::get('main.duetime') }}</label>
                        <div class="input-group date fromToDate margin-bottom-5" data-date-format="yyyy-mm-dd">
                            <input type="text" class="form-control form-filter input-sm" readonly
                                   name="duetime" placeholder="{{ Lang::get('main.enter').Lang::get('main.duetime') }}">
                            <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                        </div>
                    </div>--}}

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="user">{{ Lang::get('main.user') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{ old('user') }}" id="user" name="user" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.user') }}">
                        </div>
                    </div>
                    <div id="users" class="col-lg-12"></div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="sort">{{ Lang::get('main.sort') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="number" min="0" class="form-control" value="{{old('sort')}}" id="sort" name="sort" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.sort') }}">
                        </div>
                    </div>


                    <div class="clearfix"></div>
                    <div class="text-center col-lg-12">
                        <button type="submit" class="btn green">{{ Lang::get('main.add') }}</button>
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
        $(document).ready(function () {
            $('.fromToDate').datepicker({
                rtl: App.isRTL(),
                autoclose: true
            });
            $('.module_name').select2();
            $('#user').keyup(function(){
                var query=$(this).val();
                if(query!=''){
                    var token = "{{ csrf_token() }}";
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/mba_courses_user_plan/autoCompleteUsers') }}",
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