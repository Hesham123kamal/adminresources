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
                <a href="{{ URL('/admin/recruitment_employees') }}">{{ Lang::get('main.recruitment_employees') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.recruitment_employees') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.recruitment_employees') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/recruitment_employees/'.$employee->id,'class'=>"form-horizontal",'files'=>true]) !!}
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
                    <label class="control-label" for="user">{{ Lang::get('main.user') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$user}}" id="user" name="user"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.user') }}">
                    </div>
                </div>
                <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                    <input type="checkbox" class="make-switch" name="published" value="yes"
                           @if($employee->published=="yes") checked @endif data-size="small" data-on-color="success"
                           data-on-text="{{ Lang::get('main.published') }}" data-off-color="default" data-off-text="{{ Lang::get('main.unpublished') }}">
                </div>
                <div id="users" class="col-lg-12"></div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="country">{{ Lang::get('main.country') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$country}}" id="country" name="country"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.country') }}">
                    </div>
                </div>
                <div id="countries" class="col-lg-12"></div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="city">{{ Lang::get('main.city') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$city}}" id="city" name="city"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.city') }}">
                    </div>
                </div>
                <div id="cities" class="col-lg-12"></div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="state">{{ Lang::get('main.state') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$state}}" id="state" name="state"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.state') }}">
                    </div>
                </div>
                <div id="states" class="col-lg-12"></div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="email">{{ Lang::get('main.email') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$employee->email}}" name="email"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.email') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="password">{{ Lang::get('main.password') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$employee->password}}" name="password"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.password') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="fullname">{{ Lang::get('main.fullname') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$employee->fullname}}" name="fullname"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.fullname') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label for="gender">{{ Lang::get('main.gender') }}<span
                                class="required"> * </span></label>
                    <select name="gender" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.gender') }}</option>
                        <option @if($employee->gender=='male') selected="selected" @endif value="male">male</option>
                        <option @if($employee->gender=='female') selected="selected" @endif value="female">female</option>
                    </select>
                </div>
                <div class="form-group col-lg-12">
                    <label for="marital_status">{{ Lang::get('main.marital_status') }}<span
                                class="required"> * </span></label>
                    <select name="marital_status" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.marital_status') }}</option>
                        <option @if($employee->marital_status=='single') selected="selected" @endif value="single">single</option>
                        <option @if($employee->marital_status=='married') selected="selected" @endif value="married">married</option>
                        <option @if($employee->marital_status=='unspecified') selected="selected" @endif value="unspecified">unspecified</option>
                    </select>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="mobile">{{ Lang::get('main.mobile') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$employee->mobile}}" name="mobile"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.mobile') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="mobile2">{{ Lang::get('main.mobile2') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$employee->mobile_1}}" name="mobile_1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.mobile2') }}">
                    </div>
                </div>

                @if($employee->cv)
                <div class="form-group col-lg-12">
                    <label class="control-label">{{ Lang::get('main.cv') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <a href="{{assetURL($employee->cv) }}">{{$employee->cv}} </a>
                    </div>
                </div>
                @endif

                <div class="form-group col-lg-12">
                    <label class="control-label" for="image">@if($employee->cv) {{ Lang::get('main.replace') }} {{ Lang::get('main.cv') }}  @else {{ Lang::get('main.cv') }} @endif</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="file" class="form-control" value="" name="cv"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.cv') }}">
                    </div>
                </div>

                @if($employee->image)
                <div class="form-group col-lg-12">
                    <label class="control-label">{{ Lang::get('main.image') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <img style="width:20%;" src="{{assetURL($employee->image) }}">
                    </div>
                </div>
                @endif

                <div class="form-group col-lg-12">
                    <label class="control-label" for="image">@if($employee->image) {{ Lang::get('main.replace') }} {{ Lang::get('main.image') }} @else {{ Lang::get('main.image') }} @endif </label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="file" class="form-control" value="" accept="image/*" name="image"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.image') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="address">{{ Lang::get('main.address') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <textarea class="form-control" style="min-height: 300px;" name="address"
                                  placeholder="{{ Lang::get('main.enter').Lang::get('main.address') }}">{{$employee->address}}</textarea>
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="birthdate">{{ Lang::get('main.birthdate') }}</label>
                    <div class="input-group date margin-bottom-5" data-date-format="yyyy-mm-dd">
                        <input type="text" value="{{$employee->birthdate}}" class="form-control form-filter input-sm birthdate"
                               name="birthdate" placeholder="{{ Lang::get('main.birthdate') }}">
                        <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="number_dependants">{{ Lang::get('main.number_dependants') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$employee->number_dependants}}" name="number_dependants"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.number_dependants') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="postal_code">{{ Lang::get('main.postal_code') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$employee->postal_code}}" name="postal_code"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.postal_code') }}">
                    </div>
                </div>

                @include('auth/description',['not_required'=>true,'posts'=>[$employee->description]])

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
            var token = "{{ csrf_token() }}";
            $('#user').keyup(function(){
                var query=$(this).val();
                if(query!=''){
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/recruitment_employees/autoCompleteUsers') }}",
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

            $('#country').keyup(function(){
                var query=$(this).val();
                if(query!=''){
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/recruitment_employees/autoCompleteCountries') }}",
                        data: {query: query, _token: token},
                        success: function (data) {
                            $("#countries").fadeIn();
                            $("#countries").html(data);
                        }
                    })
                }
                else{
                    $("#countries").fadeOut();
                }
            });
            $(document).on('click','#countries-names li',function(){
                $('#country').val($(this).text());
                $('#countries').fadeOut();
            });

            $('#city').keyup(function(){
                var query=$(this).val();
                if(query!=''){
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/recruitment_employees/autoCompleteCities') }}",
                        data: {query: query, _token: token},
                        success: function (data) {
                            $("#cities").fadeIn();
                            $("#cities").html(data);
                        }
                    })
                }
                else{
                    $("#cities").fadeOut();
                }
            });
            $(document).on('click','#cities-names li',function(){
                $('#city').val($(this).text());
                $('#cities').fadeOut();
            });

            $('#state').keyup(function(){
                var query=$(this).val();
                if(query!=''){
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/recruitment_employees/autoCompleteStates') }}",
                        data: {query: query, _token: token},
                        success: function (data) {
                            $("#states").fadeIn();
                            $("#states").html(data);
                        }
                    })
                }
                else{
                    $("#states").fadeOut();
                }
            });
            $(document).on('click','#states-names li',function(){
                $('#state').val($(this).text());
                $('#states').fadeOut();
            });

            $('.birthdate').datepicker({
                rtl: App.isRTL(),
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
        });
    </script>
@endsection