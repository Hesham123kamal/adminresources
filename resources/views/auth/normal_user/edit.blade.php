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
                <a href="{{ URL('/admin/normal_user') }}">{{ Lang::get('main.normal_user') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.normal_user') }}
        <small>{{ Lang::get('main.edit') }}</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
@endsection
@section('content')
<style>
   .form-group{
       margin-left: 0px !important;
       margin-right: 0px !important;
   }
</style>
    <div class="row">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-users font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.normal_user') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/normal_user/'.$user->id,'class'=>"form-horizontal",'files'=>true]) !!}
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
                <div class="form-group col-lg-6">
                    <label class="control-label" for="name">{{ Lang::get('main.name') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$user->FullName}}" id="name" name="name" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                    </div>
                </div>

                <div class="form-group col-lg-6">
                    <label class="control-label" for="name_en">{{ Lang::get('main.en_name') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$user->name_en}}" name="name_en"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.en_name') }}">
                    </div>
                </div>
                {{--<div class="form-group col-lg-3 text-center" style="margin-top:25px;">--}}
                    {{--<input type="checkbox" class="make-switch" name="published" value="yes"--}}
                           {{--@if($user->published=="yes") checked @endif data-size="small" data-on-color="success"--}}
                           {{--data-on-text="{{ Lang::get('main.published') }}" data-off-color="default" data-off-text="{{ Lang::get('main.unpublished') }}">--}}
                {{--</div>--}}
                <div class="form-group col-lg-6">
                    <label class="control-label" for="email">{{ Lang::get('main.email') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$user->Email}}" name="email" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.email') }}">
                    </div>
                </div>

                <div class="form-group col-lg-6">
                    <label class="control-label" for="mobile">{{ Lang::get('main.mobile') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$user->Mobile}}" name="mobile" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.mobile') }}">
                    </div>
                </div>
                @if(PerUser('normal_user_show_password'))
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="password">{{ Lang::get('main.password') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{$user->Password}}" id="password" name="password" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.password') }}">
                        </div>
                    </div>
                @endif

                <div class="form-group col-lg-12">
                    <label class="control-label">{{ Lang::get('main.image') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <img style="width:20%;" src="{{assetURL($user->image) }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="image">{{ Lang::get('main.replace') }} {{ Lang::get('main.image') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="file" class="form-control" value="" id="image" accept="image/*"
                               name="image" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.image') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label for="type">{{ Lang::get('main.type') }}<span
                                class="required"> * </span></label>
                    <select id="type" name="type" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.type') }}</option>
                        <option @if($user->type=='person')  selected="selected" @endif value="person">Person</option>
                        <option @if($user->type=='company')  selected="selected" @endif value="company">Company</option>
                    </select>
                </div>

                <div id="companies" class="form-group col-lg-12">
                    <label for="company">{{ Lang::get('main.company') }}<span
                                class="required"> * </span></label>
                    <select name="company" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.company') }}</option>
                        @foreach($companies as $id=>$name)
                            <option @if($user->company_id==$id)  selected="selected"
                                    @endif value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-lg-4">
                    <label for="country">{{ Lang::get('main.country') }}<span
                                class="required"> * </span></label>
                    <select name="country" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.country') }}</option>
                        @foreach($countries as $id=>$name)
                            <option @if($user->country==$id)  selected="selected"
                                    @endif value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-lg-4">
                    <label for="type_of_subscribe">{{ Lang::get('main.type_of_subscribe') }}<span
                                class="required"> * </span></label>
                    <select name="type_of_subscribe" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.type_of_subscribe') }}</option>
                        <option @if($user->type_of_subscribe=='default')  selected="selected" @endif  value="default">Default</option>
                        <option @if($user->type_of_subscribe=='annual')  selected="selected" @endif value="annual">Annual</option>
                        <option @if($user->type_of_subscribe=='percourse')  selected="selected" @endif value="percourse">Per course</option>
                        <option @if($user->type_of_subscribe=='diplomas')  selected="selected" @endif value="diplomas">Diplomas</option>
                    </select>
                </div>

                <div class="form-group col-lg-4">
                    <label for="user_type">{{ Lang::get('main.user_type') }}<span
                                class="required"> * </span></label>
                    <select name="user_type" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.user_type') }}</option>
                        <option @if($user->user_type=='corporate')  selected="selected" @endif value="corporate">Corporate</option>
                        <option @if($user->user_type=='individual')  selected="selected" @endif value="individual">Individual</option>
                    </select>
                </div>

                <div class="form-group col-lg-4">
                    <label class="control-label" for="facebook">{{ Lang::get('main.facebook') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$user->facebook}}" name="facebook"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.facebook') }}">
                    </div>
                </div>

                <div class="form-group col-lg-4">
                    <label class="control-label" for="linkedin">{{ Lang::get('main.linkedin') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$user->linkedin}}" name="linkedin"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.linkedin') }}">
                    </div>
                </div>

                <div class="form-group col-lg-4">
                    <label class="control-label" for="twitter">{{ Lang::get('main.twitter') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$user->twitter}}" name="twitter"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.twitter') }}">
                    </div>
                </div>

                <div class="form-group col-lg-4">
                    <label class="control-label" for="google">{{ Lang::get('main.google') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$user->google}}" name="google"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.google') }}">
                    </div>
                </div>

                <div class="form-group col-lg-4">
                    <label class="control-label" for="suspend">{{ Lang::get('main.suspend') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->suspend}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="suspend_date">{{ Lang::get('main.suspend_date') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->suspend_date}}">
                    </div>
                </div>

                <div class="form-group col-lg-4">
                    <label class="control-label" for="is_affiliate">{{ Lang::get('main.is_affiliate') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->is_affiliate}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="affiliate_id">{{ Lang::get('main.affiliate_id') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->affiliate_id}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="register_date">{{ Lang::get('main.register_date') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->RegisterDate}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="active_email">{{ Lang::get('main.active_email') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->ActiveEmail}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="active_email_date">{{ Lang::get('main.active_email_date') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->ActiveEmailDate}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="active_mobile">{{ Lang::get('main.active_mobile') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->ActiveMobile}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="active_mobile_date">{{ Lang::get('main.active_mobile_date') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->ActiveMobileDate}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="email_random_key">{{ Lang::get('main.email_random_key') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->EmailRandomKey}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="mobile_random_key">{{ Lang::get('main.mobile_random_key') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->MobileRandomKey}}">
                    </div>
                </div>

                <div class="col-md-4 form-group">
                    <label class="control-label" for="demo_expiration_date">{{ Lang::get('main.demo_expiration_date') }}</label>
                    <div class="input-group date ped" data-date-format="yyyy-mm-dd">
                        <input type="text" class="form-control" name="demo_expiration_date" value="{{$user->DemoExpirationDate}}">
                        <span class="input-group-btn">
                                <button class="btn btn-sm default" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                           </span>
                    </div>
                </div>

                <div class="col-md-4 form-group">
                    <label class="control-label" for="demo_medical_expiration_date">{{ Lang::get('main.demo_medical_expiration_date') }}</label>
                    <div class="input-group date ped" data-date-format="yyyy-mm-dd">
                        <input type="text" class="form-control" name="demo_medical_expiration_date" value="{{$user->DemoMedicalExpirationDate}}">
                        <span class="input-group-btn">
                                <button class="btn btn-sm default" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                           </span>
                    </div>
                </div>

                <div class="form-group col-lg-4">
                    <label class="control-label" for="number_of_demo">{{ Lang::get('main.number_of_demo') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->NumberOfDemo}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="paid">{{ Lang::get('main.paid') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->Paid}}">
                    </div>
                </div>

                <div class="col-md-4 form-group">
                    <label class="control-label" for="payment_expiration_date">{{ Lang::get('main.payment_expiration_date') }}</label>
                    <div class="input-group date ped" data-date-format="yyyy-mm-dd">
                          <input type="text" class="form-control" name="payment_expiration_date" value="{{$user->PaymentExpirationDate}}">
                           <span class="input-group-btn">
                                <button class="btn btn-sm default" type="button">
                                    <i class="fa fa-calendar"></i>
                                </button>
                           </span>
                    </div>
                </div>

                <div class="form-group col-lg-4">
                    <label class="control-label" for="last_payment_date">{{ Lang::get('main.last_payment_date') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->LastPaymentDate}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="register_ip">{{ Lang::get('main.register_ip') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->RegisterIP}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="duplicate_ip">{{ Lang::get('main.duplicate_ip') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->duplicateIP}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="register_referrer">{{ Lang::get('main.register_referrer') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->RegisterReferrer}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="login_type">{{ Lang::get('main.login_type') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->login_type}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="academy_added">{{ Lang::get('main.academy_added') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->academy_added}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="subscribe">{{ Lang::get('main.subscribe') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->subscribe}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="session_user">{{ Lang::get('main.session_user') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->session_user}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="subscribe_country">{{ Lang::get('main.subscribe_country') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->subscrip_country}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="no_of_employees">{{ Lang::get('main.no_of_employees') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->noofemployees}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="affiliate_reseller_id">{{ Lang::get('main.affiliate_reseller_id') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->affiliat_reseller_id}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="affiliate_product_id">{{ Lang::get('main.affiliate_product_id') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->affiliat_product_id}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label" for="last_login_date">{{ Lang::get('main.last_login_date') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->last_login_date}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label">{{ Lang::get('main.en_name') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->name_en}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label">{{ Lang::get('main.register_type') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->register_type}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label">{{ Lang::get('main.medical_type') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->medical_type}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label">{{ Lang::get('main.partner') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input disabled="disabled" type="text" class="form-control" value="{{$user->partner}}">
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <label class="control-label">{{ Lang::get('main.sponsorId') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input @if(PerUser('normal_user_edit_sponserid')) name="sponsorId" @else disabled="disabled" @endif type="text" class="form-control" value="{{$user->sponsorId}}">
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
        $(document).ready(function(){
            @if($user->type!='company')
            $('#companies').hide();
            @endif
            $('#type').change(function(){
                if($(this).val()=='company'){
                    $('#companies').show();
                }
                else{
                    $('#companies').hide();
                }
            })
            $('.ped').datepicker({
                rtl: App.isRTL(),
                autoclose: true
            });

        })

    </script>
@endsection