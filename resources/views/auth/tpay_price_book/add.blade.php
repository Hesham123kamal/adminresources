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
                <a href="{{ URL('/admin/tpay_price_book') }}">{{ Lang::get('main.tpay_price_book') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.tpay_price_book') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.tpay_price_book') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/tpay_price_book','class'=>"form-horizontal"]) !!}
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
                        <label class="control-label" for="sku">{{ Lang::get('main.sku') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="sku" name="sku" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.sku') }}">
                        </div>
                    </div>
                    {{--<div class="form-group col-lg-3 text-center" style="margin-top:25px;">--}}
                        {{--<input type="checkbox" class="make-switch" name="published" value="yes" checked data-size="small"--}}
                               {{--data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"--}}
                               {{--data-off-text="{{ Lang::get('main.unpublished') }}">--}}
                    {{--</div>--}}

                    <div class="form-group col-lg-12">
                        <label for="country">{{ Lang::get('main.country') }}<span
                                    class="required"> * </span></label>
                        <select name="country" class="module_name sel2 form-control form-filter"
                                id="country">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.country') }}</option>
                            @foreach($country as $id=>$name)
                                <option value="{{$id}}">{{$name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="operator_code">{{ Lang::get('main.operator_code') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="operator_code" name="operator_code"
                                   data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.operator_code') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="operator_name">{{ Lang::get('main.operator_name') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="operator_name" name="operator_name"
                                   data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.operator_name') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="currency_symbol">{{ Lang::get('main.currency_symbol') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="currency_symbol" name="currency_symbol"
                                   data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.currency_symbol') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="price">{{ Lang::get('main.price') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="price" name="price"
                                   data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.price') }}">
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
@endsection