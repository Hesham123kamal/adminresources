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
                <a href="{{ URL('/admin/employee') }}">{{ Lang::get('main.employee') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.employee') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.employee') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/employee/'.$employee->id,'class'=>"form-horizontal"]) !!}
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
                    <label class="control-label" for="username">{{ Lang::get('main.username') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$employee->username}}" name="username" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.username') }}">
                    </div>
                </div>
                {{--<div class="form-group col-lg-3 text-center" style="margin-top:25px;">--}}
                    {{--<input type="checkbox" class="make-switch" name="published" value="yes"--}}
                           {{--@if($employee->published=="yes") checked @endif data-size="small" data-on-color="success"--}}
                           {{--data-on-text="{{ Lang::get('main.published') }}" data-off-color="default" data-off-text="{{ Lang::get('main.unpublished') }}">--}}
                {{--</div>--}}
                <div class="form-group col-lg-12">
                    <label class="control-label" for="password">{{ Lang::get('main.password') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$employee->password}}" name="password" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.password') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label for="user_level">{{ Lang::get('main.user_level') }}<span
                                class="required"> * </span></label>
                    <select name="user_level" class="module_name form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.user_level') }}</option>
                        @foreach($levels as $id=>$name)
                            <option @if($employee->UserLevel==$id)  selected="selected"
                                    @endif value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="login_type">{{ Lang::get('main.login_type') }} </label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$employee->logintype}}" name="login_type" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.login_type') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="support_level">{{ Lang::get('main.support_level') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$employee->support_level}}" name="support_level" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.support_level') }}">
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
