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
                <a href="{{ URL('/admin/company') }}">{{ Lang::get('main.company') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.company') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.company') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/company/'.$company->id,'class'=>"form-horizontal"]) !!}
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
                    <label class="control-label" for="name">{{ Lang::get('main.name') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$company->name}}" id="name" name="name" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                    </div>
                </div>

                {{--<div class="form-group col-lg-3 text-center" style="margin-top:25px;">--}}
                    {{--<input @if($company->published=="yes") checked @endif type="checkbox" class="make-switch" name="published" data-size="small"--}}
                           {{--data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"--}}
                           {{--data-off-text="{{ Lang::get('main.unpublished') }}">--}}
                {{--</div>--}}

                <div class="form-group col-lg-12">
                    <label class="control-label" for="employees_numbers">{{ Lang::get('main.employees_numbers') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$company->employees_numbers}}" id="employees_numbers" name="employees_numbers" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.employees_numbers') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="amount">{{ Lang::get('main.amount') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$company->amount}}" id="amount" name="amount" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.amount') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="period">{{ Lang::get('main.period') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$company->period}}" id="period" name="period" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.period') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="expiredDate">{{ Lang::get('main.expired_date') }} <span
                                class="required"> * </span></label>
                    <div class="input-group date margin-bottom-5" data-date-format="yyyy-mm-dd">
                        <input value="{{$company->expiredDate}}" type="text" class="form-control form-filter input-sm expiredDate"
                               name="expiredDate" placeholder="{{ Lang::get('main.expired_date') }}">
                        <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                    </div>
                </div>
                <div class="form-group col-lg-9">
                    <label for="type">{{ Lang::get('main.type') }}<span
                                class="required"> * </span></label>
                    <select name="type" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.type') }}</option>
                        <option @if($company->type=='live') selected="selected" @endif value="live">Live</option>
                        <option @if($company->type=='demo') selected="selected" @endif value="demo">Demo</option>
                        <option @if($company->type=='test') selected="selected" @endif value="test">Test</option>
                    </select>
                </div>

                <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                    <input @if($company->isfree) checked @endif type="checkbox" class="make-switch" name="isfree"  data-size="small"
                           data-on-color="success" data-on-text="{{ Lang::get('main.free') }}" data-off-color="default"
                           data-off-text="{{ Lang::get('main.notfree') }}">
                </div>

                @include('auth/description',['selectors'=>'.address,.description', 'labels'=>[Lang::get('main.address'),Lang::get('main.description')], 'posts' =>[$company->address,$company->description] ])

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
            $('.expiredDate').datepicker({
                rtl: App.isRTL(),
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
        });
    </script>
@endsection