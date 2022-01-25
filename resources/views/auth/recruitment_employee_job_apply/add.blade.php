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
                <a href="{{ URL('/admin/recruitment_employee_job_apply') }}">{{ Lang::get('main.recruitment_employee_job_apply') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.recruitment_employee_job_apply') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.recruitment_employee_job_apply') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/recruitment_employee_job_apply','class'=>"form-horizontal"]) !!}
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
                    <div class="form-group col-lg-9">
                        <label for="job">{{ Lang::get('main.job') }}<span
                                    class="required"> * </span></label>
                        <select name="job" class="module_name form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.job') }}</option>
                            @foreach($jobs as $id=>$title)
                                <option value="{{$id}}">{{$title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                        <input type="checkbox" class="make-switch" name="published" value="yes" checked data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.unpublished') }}">
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="employee">{{ Lang::get('main.employee') }}<span
                                    class="required"> * </span></label>
                        <select name="employee" class="module_name form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.employee') }}</option>
                            @foreach($employees as $id=>$email)
                                <option value="{{$id}}">{{$email}}</option>
                            @endforeach
                        </select>
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
