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
                <a href="{{ URL('/admin/app_settings') }}">{{ Lang::get('main.app_settings') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
            <li>
                <span></span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.app_settings') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.app_settings') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/app_settings','id'=>'addAppSettingsForm','class'=>"form-horizontal",'files'=>true]) !!}
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
                <div class="form-group col-lg-12">
                    <label for="easy_question_percentage">{{ Lang::get('main.easy_question_percentage') }} <span
                                class="required"> * </span></label>
                    <div class="input-group">
                        <input type="number" min="0" max="100" maxlength="3" class="form-control"
                               id="easy_question_percentage" value="{{$app_settings->easy_question_percentage}}" name="easy_question_percentage"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.easy_question_percentage') }}">
                        <div class="input-group-addon">%</div>
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label for="normal_question_percentage">{{ Lang::get('main.normal_question_percentage') }} <span
                                class="required"> * </span></label>
                    <div class="input-group">
                        <input type="number" min="0" max="100" maxlength="3" class="form-control"
                               id="normal_question_percentage" value="{{$app_settings->normal_question_percentage}}" name="normal_question_percentage"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.normal_question_percentage') }}">
                        <div class="input-group-addon">%</div>
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label for="hard_question_percentage">{{ Lang::get('main.hard_question_percentage') }} <span
                                class="required"> * </span></label>
                    <div class="input-group">
                        <input type="number" min="0" max="100" maxlength="3" class="form-control"
                               id="hard_question_percentage" value="{{$app_settings->hard_question_percentage}}" name="hard_question_percentage"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.hard_question_percentage') }}">
                        <div class="input-group-addon">%</div>
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label for="dollar_rate">{{ Lang::get('main.dollar_rate') }} <span
                                class="required"> * </span></label>
                    <div class="input-group">
                        <input type="number" step=".0001" class="form-control" id="dollar_rate" value="{{$app_settings->dollar_rate}}" name="dollar_rate"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.dollar_rate') }}">
                        <div class="input-group-addon">EGY</div>
                    </div>
                </div>

                <div class="clearfix"></div>
                <div class="text-center">
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
            $(document).on('change', '#all_projects', function () {
                if ($(this).is(':checked')) {
                    $("#projects_ids").attr('disabled', 'disabled');
                } else {
                    $("#projects_ids").removeAttr('disabled')
                }
            });
        });
    </script>
@endsection