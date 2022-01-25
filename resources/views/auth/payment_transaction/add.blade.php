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
                <a href="{{ URL('/admin/payment_transaction') }}">{{ Lang::get('main.payment_transaction') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.payment_transaction') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.payment_transaction') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/payment_transaction','class'=>"form-horizontal"]) !!}
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
                        <label class="control-label" for="email">{{ Lang::get('main.email') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="email" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.email') }}">
                        </div>
                    </div>
                    {{--<div class="form-group col-lg-3 text-center" style="margin-top:25px;">--}}
                        {{--<input type="checkbox" class="make-switch" name="published" value="yes" checked data-size="small"--}}
                               {{--data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"--}}
                               {{--data-off-text="{{ Lang::get('main.unpublished') }}">--}}
                    {{--</div>--}}

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="package">{{ Lang::get('main.package') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="package" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.package') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="amount">{{ Lang::get('main.amount') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="amount" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.amount') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="paymentstatus">{{ Lang::get('main.paymentstatus') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="paymentstatus" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.paymentstatus') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="customer_id">{{ Lang::get('main.customer_id') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="customer_id" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.customer_id') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="coupon_id">{{ Lang::get('main.coupon_id') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="coupon_id" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.coupon_id') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="price">{{ Lang::get('main.price') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="price" data-required="1"
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