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
                <a href="{{ URL('/admin/recruitment_job_roles') }}">{{ Lang::get('main.recruitment_job_roles') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.recruitment_job_roles') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.recruitment_job_roles') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/recruitment_job_roles/'.$role->id,'class'=>"form-horizontal"]) !!}
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
                <div class="form-group col-lg-9">
                    <label class="control-label" for="name">{{ Lang::get('main.name') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$role->name}}" id="name" name="name"
                               data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                    </div>
                </div>
                <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                    <input type="checkbox" class="make-switch" name="published" value="yes"
                           @if($role->published=="yes") checked @endif data-size="small" data-on-color="success"
                           data-on-text="{{ Lang::get('main.published') }}" data-off-color="default" data-off-text="{{ Lang::get('main.unpublished') }}">
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