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
                <a href="{{ URL('/admin/recruitment_jobs') }}">{{ Lang::get('main.recruitment_jobs') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.recruitment_jobs') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.recruitment_jobs') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/recruitment_jobs','class'=>"form-horizontal"]) !!}
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
                        <label for="company">{{ Lang::get('main.company') }}<span
                                    class="required"> * </span></label>
                        <select name="company" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.company') }}</option>
                            @foreach($company as $id=>$value)
                                <option value="{{$id}}">{{$value}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                        <input type="checkbox" class="make-switch" name="published" value="yes" checked data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.unpublished') }}">
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="title">{{ Lang::get('main.title') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="title" name="title" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.title') }}">
                        </div>
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
                        <label for="career">{{ Lang::get('main.career_level') }}<span
                                    class="required"> * </span></label>
                        <select name="career" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.career_level') }}</option>
                            <option value="student">Student</option>
                            <option value="entry_level">Entry Level</option>
                            <option value="experienced_non_manager">Experienced (non-manager)</option>
                            <option value="manager">Manager</option>
                            <option value="senior_management">Senior Management</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="experience_years">{{ Lang::get('main.experience_years') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="experience_years" name="experience_years" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.experience_years') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="salary_min">{{ Lang::get('main.salary_min') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="salary_min" name="salary_min" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.salary_min') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="salary_max">{{ Lang::get('main.salary_max') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="salary_max" name="salary_max" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.salary_max') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="currency">{{ Lang::get('main.currency') }}<span
                                    class="required"> * </span></label>
                        <select name="currency" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.currency') }}</option>
                            @foreach($currency as $id=>$value)
                                <option value="{{$id}}">{{$value}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-12">
                        <label for="time_period">{{ Lang::get('main.time_period') }}<span
                                    class="required"> * </span></label>
                        <select name="time_period" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.time_period') }}</option>
                            <option value="month">Month</option>
                            <option value="hour">Hour</option>
                            <option value="day">Day</option>
                            <option value="week">Week</option>
                            <option value="year">Year</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="hidden_salary">{{ Lang::get('main.hidden_salary') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="hidden_salary" name="hidden_salary" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.hidden_salary') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="salary_info">{{ Lang::get('main.salary_info') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="salary_info" name="salary_info" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.salary_info') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="num_vacancies">{{ Lang::get('main.num_vacancies') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="num_vacancies" name="num_vacancies" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.num_vacancies') }}">
                        </div>
                    </div>

                    @include('auth/description',['selectors'=>'.description,.requirements', 'labels'=>[Lang::get('main.description'),Lang::get('main.requirements')] ])

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

            $('.birthdate').datepicker({
                rtl: App.isRTL(),
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
        });
    </script>
@endsection

