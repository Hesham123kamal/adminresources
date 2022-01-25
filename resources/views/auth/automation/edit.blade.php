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
                <a href="{{ URL('/admin/automation') }}">{{ Lang::get('main.automation') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.automation') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.automation') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/automation/'.$automation->id,'class'=>"form-horizontal"]) !!}
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
                    <label class="control-label" for="user">{{ Lang::get('main.user') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$user}}" id="user" name="user" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.user') }}">
                    </div>
                </div>
                {{--<div class="form-group col-lg-3 text-center" style="margin-top:25px;">--}}
                    {{--<input type="checkbox" class="make-switch" name="published" value="yes"--}}
                           {{--@if($automation->published=="yes") checked @endif data-size="small" data-on-color="success"--}}
                           {{--data-on-text="{{ Lang::get('main.published') }}" data-off-color="default" data-off-text="{{ Lang::get('main.unpublished') }}">--}}
                {{--</div>--}}
                <div id="users" class="col-lg-12"></div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="package">{{ Lang::get('main.package') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$automation->package}}" name="package" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.package') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="payment_method">{{ Lang::get('main.payment_method') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$automation->payment_method}}" name="payment_method" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.payment_method') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="shipping_address">{{ Lang::get('main.shipping_address') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$automation->shipping_address}}" name="shipping_address" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.shipping_address') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="bank_transfer">{{ Lang::get('main.bank_transfer') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$automation->bank_transfer}}" name="bank_transfer" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.bank_transfer') }}">
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
@section('scriptCode')
    <script>
        $(document).ready(function () {
            $('#user').keyup(function(){
                var query=$(this).val();
                if(query!=''){
                    var token = "{{ csrf_token() }}";
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/automation/autoCompleteUsers') }}",
                        data: {query: query, _token: token},
                        success: function (data) {
                            $("#users").fadeIn();
                            $("#users").html(data);
                        }
                    })
                }
                else{
                    $('#users').fadeOut();
                }
            });

            $(document).on('click','#users-emails li',function(){
                $('#user').val($(this).text());
                $('#users').fadeOut();
            });

            $('.expire_date').datepicker({
                rtl: App.isRTL(),
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
        });
    </script>
@endsection