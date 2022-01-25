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
                <a href="{{ URL('/admin/lite_version_charge_transaction') }}">{{ Lang::get('main.lite_version_charge_transaction') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.lite_version_charge_transaction') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.lite_version_charge_transaction') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/lite_version_charge_transaction','class'=>"form-horizontal",'files'=>true]) !!}
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
                            <input type="text" class="form-control" value="{{ old('user') }}" id="user" name="user" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.user') }}">
                        </div>
                    </div>
                    {{--<div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                        <input type="checkbox" class="make-switch" name="published" value="yes" checked data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.unpublished') }}">
                    </div>--}}
                    <div id="users" class="col-lg-12"></div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="period">{{ Lang::get('main.period') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{ old('period') }}" name="period" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.period') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="start_date">{{ Lang::get('main.start_date') }} <span
                                    class="required"> * </span></label>
                        <div class="input-group date margin-bottom-5" data-date-format="yyyy-mm-dd">
                            <input type="text" class="form-control form-filter input-sm start_date"
                                   name="start_date" value="{{ old('start_date') }}" placeholder="{{ Lang::get('main.start_date') }}">
                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                        </div>
                    </div>

                   {{-- <div class="form-group col-lg-12">
                        <label class="control-label" for="end_date">{{ Lang::get('main.end_date') }} <span
                                    class="required"> * </span></label>
                        <div class="input-group date margin-bottom-5" data-date-format="yyyy-mm-dd">
                            <input type="text" class="form-control form-filter input-sm end_date"
                                   name="end_date" placeholder="{{ Lang::get('main.end_date') }}">
                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                        </div>
                    </div>--}}

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="subscribe_type">{{ Lang::get('main.subscribe_type') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{ old('subscribe_type') }}" name="subscribe_type" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.subscribe_type') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="amount">{{ Lang::get('main.amount') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{ old('amount') }}" name="amount" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.amount') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="subscribe_country">{{ Lang::get('main.subscribe_country') }}<span
                                    class="required"> * </span></label>
                        <select name="subscribe_country" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.subscribe_country') }}</option>
                            <option @if(old('subscribe_country')=='egy') selected="selected" @endif value="egy">Egypt</option>
                            <option @if(old('subscribe_country')=='ksa') selected="selected" @endif value="ksa">KSA</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="currency">{{ Lang::get('main.currency') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{ old('currency') }}" name="currency" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.currency') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="coupon_id">{{ Lang::get('main.coupon_id') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{ old('coupon_id') }}" name="coupon_id" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.coupon_id') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="employee">{{ Lang::get('main.employee') }}</label>
                        <select name="employee" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.employee') }}</option>
                            @foreach($employees as $id=>$name)
                                <option @if(old('employee')==$id) selected="selected" @endif value="{{$id}}">{{$name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-9">
                        <label class="control-label" for="attach">{{ Lang::get('main.attach') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="file" class="form-control" value="" id="image" accept="image/*"
                                   name="attach" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.attach') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                        <input type="checkbox" class="make-switch" name="pending" value="1" data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.pending') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.notpending') }}">
                    </div>
,
                    <div class="form-group col-lg-12" style="margin-top:25px;">
                        <label class="col-lg-2" for="courses">{{ Lang::get('main.courses') }}</label>
                        <input type="checkbox" class="make-switch " name="courses" id="courses" value="1" data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.yes') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.no') }}">
                    </div>

                    <div class="form-group col-lg-12" style="margin-top:25px;">
                        <label class="col-lg-2" for="webinars">{{ Lang::get('main.webinars') }}</label>
                        <input type="checkbox" class="make-switch" name="webinars" id="webinars" value="1" data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.yes') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.no') }}">
                    </div>

                    <div class="form-group col-lg-12" style="margin-top:25px;">
                        <label class="col-lg-2" for="offline_webinars">{{ Lang::get('main.workshops') }}</label>
                        <input type="checkbox" class="make-switch" name="offline_webinars" id="offline_webinars" value="1" data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.yes') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.no') }}">
                    </div>

                    <div class="form-group col-lg-12" style="margin-top:25px;">
                        <label class="col-lg-2" for="books">{{ Lang::get('main.books') }}</label>
                        <input type="checkbox" class="make-switch" name="books" id="books" value="1" data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.yes') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.no') }}">
                    </div>

                    <div class="form-group col-lg-12" style="margin-top:25px;">
                        <label class="col-lg-2" for="successtories">{{ Lang::get('main.successstories') }}</label>
                        <input type="checkbox" class="make-switch" name="successtories" id="successtories" value="1" data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.yes') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.no') }}">
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
            $('.start_date').datepicker({
                rtl: App.isRTL(),
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
            $('.end_date').datepicker({
                rtl: App.isRTL(),
                autoclose: true,
                format: 'yyyy-mm-dd'
            });

            $('#user').keyup(function(){
                var query=$(this).val();
                if(query!=''){
                    var token = "{{ csrf_token() }}";
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/charge_transaction/autoCompleteUsers') }}",
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