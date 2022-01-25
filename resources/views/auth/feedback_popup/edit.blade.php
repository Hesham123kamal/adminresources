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
                <a href="{{ URL('/admin/feedback') }}">{{ Lang::get('main.feedback') }}</a>
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
    <h1 class="page-title"> {{ Lang::get('main.feedback') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.feedback') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/feedback/'.$feedback->id,'id'=>'addAppSettingsForm','class'=>"form-horizontal",'files'=>true]) !!}
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

                <div class="form-group col-lg-6" style="margin-right: 10px;">
                    <label class="control-label" for="user_id">{{ Lang::get('main.user_id') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$feedback->user_id}}" id="user_id"
                               name="user_id" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.user_id') }}" readonly>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label class="control-label" for="name">{{ Lang::get('main.name') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{((count($feedback->User))?$feedback->User->FullName:'')}}" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}" readonly>
                    </div>
                </div>

                <div class="form-group col-lg-6" style="margin-right: 10px;">
                    <label class="control-label" for="phone">{{ Lang::get('main.phone') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$feedback->phone}}" id="phone" name="phone"
                               data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.phone') }}" readonly>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label class="control-label" for="page">{{ Lang::get('main.page') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$feedback->page}}" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.page') }}" readonly>
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="feedback">{{ Lang::get('main.feedback') }}</label>
                    <textarea class="form-control" id="feedback" rows="6" name="feedback" readonly
                              style="resize: none;">{{$feedback->feedback}}</textarea>
                </div>

                <div class="form-group col-lg-6" style="margin-right: 10px;">
                    <label class="control-label" for="answered">{{ Lang::get('main.answered') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <select name="feedback_answered" id="answered" class="form-control form-filter"
                                id="feedback_answered">
                            <option value=" ">{{ Lang::get('main.select') }} {{  Lang::get('main.answer_status') }}</option>
                            <option value="0" @if($feedback->feedback_answered == '0') selected @endif>0</option>
                            <option value="1" @if($feedback->feedback_answered == '1') selected @endif>1</option>
                        </select>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label class="control-label" for="displayed">{{ Lang::get('main.displayed') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$feedback->answer_displayed}}"
                               data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.displayed') }}" readonly>
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="answer">{{ Lang::get('main.answer') }}</label>
                    <textarea class="form-control" id="answer" rows="6" name="answer" style="resize: none;"
                              placeholder="{{ Lang::get('main.enter').Lang::get('main.answer') }}">{{$feedback->answer}}</textarea>
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