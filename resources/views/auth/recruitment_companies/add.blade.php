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
                <a href="{{ URL('/admin/recruitment_companies') }}">{{ Lang::get('main.recruitment_companies') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.recruitment_companies') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.recruitment_companies') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/recruitment_companies','class'=>"form-horizontal",'files'=>true]) !!}
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
                        <label class="control-label" for="name">{{ Lang::get('main.name') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="name" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                        <input type="checkbox" class="make-switch" name="published" value="yes" checked data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.unpublished') }}">
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="country">{{ Lang::get('main.country') }}<span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="country" name="country"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.country') }}">
                        </div>
                    </div>
                    <div id="countries" class="col-lg-12"></div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="city">{{ Lang::get('main.city') }}<span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="city" name="city"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.city') }}">
                        </div>
                    </div>
                    <div id="cities" class="col-lg-12"></div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="state">{{ Lang::get('main.state') }}<span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="state" name="state"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.state') }}">
                        </div>
                    </div>
                    <div id="states" class="col-lg-12"></div>
                    <div class="form-group col-lg-12">
                        <label for="industry">{{ Lang::get('main.industry') }}<span
                                    class="required"> * </span></label>
                        <select name="industry" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.industry') }}</option>
                            @foreach($industries as $id=>$value)
                                <option value="{{$id}}">{{$value}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="logo">{{ Lang::get('main.logo') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="file" class="form-control" value="" accept="image/*"
                                   name="logo" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.logo') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label for="company_size">{{ Lang::get('main.company_size') }}<span
                                    class="required"> * </span></label>
                        <select name="company_size" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.company_size') }}</option>
                            <option value="1_10">1-10</option>
                            <option value="11_50">11-50</option>
                            <option value="51_100">51-100</option>
                            <option value="101_500">101-500</option>
                            <option value="501_1000">501-1000</option>
                                <option value="more_than_1000">more than 1000</option>
                        </select>
                    </div>

                    @include('auth/description')

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="website">{{ Lang::get('main.website') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="website" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.website') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="founded_year">{{ Lang::get('main.founded_year') }}</label>
                        <div class="input-group date margin-bottom-5" data-date-format="yyyy-mm-dd">
                            <input type="text" class="form-control form-filter input-sm founded_year"
                                   name="founded_year" placeholder="{{ Lang::get('main.founded_year') }}">
                            <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="facebook">{{ Lang::get('main.facebook') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="facebook" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.facebook') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="linkedin">{{ Lang::get('main.linkedin') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="linkedin" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.linkedin') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="blog">{{ Lang::get('main.blog') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="blog" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.blog') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="twitter">{{ Lang::get('main.twitter') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="twitter" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.twitter') }}">
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
            $('#country').keyup(function(){
                var query=$(this).val();
                if(query!=''){
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/recruitment_jobs/autoCompleteCountries') }}",
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
                        url: "{{ URL('admin/recruitment_jobs/autoCompleteCities') }}",
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
                        url: "{{ URL('admin/recruitment_jobs/autoCompleteStates') }}",
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

            $('.founded_year').datepicker({
                rtl: App.isRTL(),
                autoclose: true,
                maxDate: new Date(),
                viewMode: "years",
                minViewMode: "years",
                format: 'yyyy',
            });
        });
    </script>
@endsection

