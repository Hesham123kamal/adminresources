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
                <a href="{{ URL('/admin/apple_users_charge_transactions') }}">{{ Lang::get('main.apple_users_charge_transactions') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.apple_users_charge_transactions') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.apple_users_charge_transactions') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/apple_users_charge_transactions/'.$transaction->id,'class'=>"form-horizontal"]) !!}
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
                <div id="users" class="col-lg-12"></div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="start_date">{{ Lang::get('main.start_date') }} <span
                                class="required"> * </span></label>
                    <div class="input-group date margin-bottom-5" data-date-format="yyyy-mm-dd">
                        <input type="text" value="{{ $transaction->start_date }}" class="form-control form-filter input-sm dd"
                               name="start_date" placeholder="{{ Lang::get('main.start_date') }}">
                        <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="end_date">{{ Lang::get('main.end_date') }} <span
                                class="required"> * </span></label>
                    <div class="input-group date margin-bottom-5" data-date-format="yyyy-mm-dd">
                        <input type="text" value="{{ $transaction->end_date }}" class="form-control form-filter input-sm dd"
                               name="end_date" placeholder="{{ Lang::get('main.end_date') }}">
                        <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="quantity">{{ Lang::get('main.quantity') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{  $transaction->quantity }}" name="quantity" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.quantity') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="type">{{ Lang::get('main.type') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <select class="form-control sel2" name="type" id="type">
                            <option value="">@lang('main.select')@lang('main.type')</option>
                            <option @if($transaction->type=='charge_transaction') selected="selected" @endif value="charge_transaction">charge_transaction</option>
                            <option @if($transaction->type=='diplomas_charge_transaction') selected="selected" @endif value="diplomas_charge_transaction">diplomas_charge_transaction</option>
                            <option @if($transaction->type=='mba_charge_transaction') selected="selected" @endif value="mba_charge_transaction">mba_charge_transaction</option>
                            <option @if($transaction->type=='diplomas') selected="selected" @endif value="diplomas">diplomas</option>
                            <option @if($transaction->type=='courses') selected="selected" @endif value="courses">courses</option>
                        </select>

                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="type_id">{{ Lang::get('main.type_id') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{  $transaction->type_id }}" name="type_id" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.type_id') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="product_id">{{ Lang::get('main.product_id') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{  $transaction->product_id }} " name="product_id" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.product_id') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="transaction_id">{{ Lang::get('main.transaction_id') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{ $transaction->transaction_id }}" name="transaction_id" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.transaction_id') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="original_transaction_id">{{ Lang::get('main.original_transaction_id') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{ $transaction->original_transaction_id }}" name="original_transaction_id" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.original_transaction_id') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="purchase_date">{{ Lang::get('main.purchase_date') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{ $transaction->purchase_date }}" name="purchase_date" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.purchase_date') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="purchase_date_ms">{{ Lang::get('main.purchase_date_ms') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{ $transaction->purchase_date_ms }}" name="purchase_date_ms" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.purchase_date_ms') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="purchase_date_pst">{{ Lang::get('main.purchase_date_pst') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{ $transaction->purchase_date_pst }}" name="purchase_date_pst" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.purchase_date_pst') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="original_purchase_date">{{ Lang::get('main.original_purchase_date') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{ $transaction->original_purchase_date }}" name="original_purchase_date" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.original_purchase_date') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="original_purchase_date_ms">{{ Lang::get('main.original_purchase_date_ms') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{ $transaction->original_purchase_date_ms }}" name="original_purchase_date_ms" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.original_purchase_date_ms') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="original_purchase_date_pst">{{ Lang::get('main.original_purchase_date_pst') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{ $transaction->original_purchase_date_pst }}" name="original_purchase_date_pst" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.original_purchase_date_pst') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="is_trial_period">{{ Lang::get('main.is_trial_period') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <select class="form-control sel2" name="is_trial_period" id="is_trial_period">
                            <option value="">@lang('main.select')@lang('main.is_trial_period')</option>
                            <option @if($transaction->is_trial_period =='true') selected="selected" @endif value="true">@lang('main.true')</option>
                            <option @if($transaction->is_trial_period=='false') selected="selected" @endif value="false">@lang('main.false')</option>
                        </select>

                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label for="apple_user_id">{{ Lang::get('main.apple_user_id') }}</label>
                    <select name="apple_user_id" class="module_name sel2 form-control form-filter"
                            id="apple_user_id">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.apple_user_id') }}</option>
                        @foreach($apple_users as $apple_user)
                            <option @if($transaction->apple_user_id==$apple_user) selected="selected" @endif value="{{$apple_user}}">{{$apple_user}}</option>
                        @endforeach
                    </select>
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
            $('.dd').datepicker({
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
                        url: "{{ URL('admin/apple_users_charge_transactions/autoCompleteUsers') }}",
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