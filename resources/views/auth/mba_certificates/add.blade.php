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
                <a href="{{ URL('/admin/mba_certificates') }}">{{ Lang::get('main.mba_certificates') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.mba_certificates') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.mba_certificates') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/mba_certificates','class'=>"form-horizontal"]) !!}
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
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="user">{{ Lang::get('main.user') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{old('user')}}" id="user" name="user" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.user') }}">
                        </div>
                    </div>
                    <div id="users" class="col-lg-12"></div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="serial_number">{{ Lang::get('main.serial_number') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{old('serial_number')}}" name="serial_number" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.serial_number') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="user_name">{{ Lang::get('main.user_name') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{old('user_name')}}" name="user_name" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.user_name') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="user_name_en">{{ Lang::get('main.user_name_en') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{old('user_name_en')}}" name="user_name_en"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.user_name_en') }}">
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
            var token = "{{ csrf_token() }}";
            $('#user').keyup(function(){
                var query=$(this).val();
                if(query!=''){
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/mba_certificates/autoCompleteUsers') }}",
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