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
                <a href="{{ URL('/admin/recruit') }}">{{ Lang::get('main.recruit') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.recruit') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.recruit') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/recruit','class'=>"form-horizontal",'files'=>true]) !!}
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
                    <div class="form-group col-lg-9">
                        <label class="control-label" for="user">{{ Lang::get('main.user') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="user" name="user"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.user') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                        <input type="checkbox" class="make-switch" name="published" value="yes" checked data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.unpublished') }}">
                    </div>
                    <div id="users" class="col-lg-12"></div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="country">{{ Lang::get('main.country') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="country" name="country"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.country') }}">
                        </div>
                    </div>
                    <div id="countries" class="col-lg-12"></div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="city">{{ Lang::get('main.city') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="city" name="city"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.city') }}">
                        </div>
                    </div>
                    <div id="cities" class="col-lg-12"></div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="state">{{ Lang::get('main.state') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="state" name="state"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.state') }}">
                        </div>
                    </div>
                    <div id="states" class="col-lg-12"></div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="company_name">{{ Lang::get('main.company_name') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="company_name"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.company_name') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="email">{{ Lang::get('main.email') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="email"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.email') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="phone">{{ Lang::get('main.phone') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="phone"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.phone') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="wanted_job">{{ Lang::get('main.wanted_job') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="wanted_job"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.wanted_job') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label for="salary_type">{{ Lang::get('main.salary_type') }}<span
                                    class="required"> * </span></label>
                        <select name="salary_type" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.salary_type') }}</option>
                            <option value="salary">salary</option>
                            <option value="under_negotiation">under negotiation</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="salary_from">{{ Lang::get('main.salary_from') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="salary_from"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.salary_from') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="salary_to">{{ Lang::get('main.salary_to') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="salary_to"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.salary_to') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="experience_years_from">{{ Lang::get('main.experience_years_from') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="experience_years_from"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.experience_years_from') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="experience_years_to">{{ Lang::get('main.experience_years_to') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="experience_years_to"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.experience_years_to') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="address">{{ Lang::get('main.address') }}<span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <textarea class="form-control" style="min-height: 300px;" name="address"
                                      placeholder="{{ Lang::get('main.enter').Lang::get('main.address') }}"></textarea>
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="logo">{{ Lang::get('main.logo') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="file" class="form-control" value="" accept="image/*" name="logo"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.logo') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="field">{{ Lang::get('main.field') }} </label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="field"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.field') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="company_description">{{ Lang::get('main.company_description') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <textarea class="form-control" style="min-height: 300px;" name="company_description"
                                      placeholder="{{ Lang::get('main.enter').Lang::get('main.company_description') }}"></textarea>
                        </div>
                    </div>
                    @include('auth/description',['not_required'=>true])

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="responsible_name">{{ Lang::get('main.responsible_name') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="responsible_name"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.responsible_name') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="url">{{ Lang::get('main.url') }} </label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="url"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.url') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="company_url">{{ Lang::get('main.company_url') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="company_url"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.company_url') }}">
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
    <script>
        $(document).ready(function () {
            var token = "{{ csrf_token() }}";
            $('#user').keyup(function(){
                var query=$(this).val();
                if(query!=''){
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/recruit/autoCompleteUsers') }}",
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
                        url: "{{ URL('admin/recruit/autoCompleteCountries') }}",
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
                        url: "{{ URL('admin/recruit/autoCompleteCities') }}",
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
                        url: "{{ URL('admin/recruit/autoCompleteStates') }}",
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