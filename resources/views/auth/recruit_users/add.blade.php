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
                <a href="{{ URL('/admin/recruit_users') }}">{{ Lang::get('main.recruit_users') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.recruit_users') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.recruit_users') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/recruit_users','class'=>"form-horizontal",'files'=>true]) !!}
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
                            <input type="text" class="form-control" value="" id="name" name="name" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                        <input type="checkbox" class="make-switch" name="published" value="yes" checked data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.unpublished') }}">
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="email">{{ Lang::get('main.email') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="email" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.email') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="mobile">{{ Lang::get('main.mobile') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="mobile" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.mobile') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="image">{{ Lang::get('main.image') }} <span
                                    class="required"> * </span></label>
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
                            <option value="person">Person</option>
                            <option value="company">Company</option>
                        </select>
                    </div>

                    <div id="companies" class="form-group col-lg-12">
                        <label for="company">{{ Lang::get('main.company') }}<span
                                    class="required"> * </span></label>
                        <select name="company" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.company') }}</option>
                            @foreach($companies as $id=>$name)
                                <option value="{{$id}}">{{$name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="country">{{ Lang::get('main.country') }}<span
                                    class="required"> * </span></label>
                        <select name="country" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.country') }}</option>
                            @foreach($countries as $id=>$name)
                                <option value="{{$id}}">{{$name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="type_of_subscribe">{{ Lang::get('main.type_of_subscribe') }}<span
                                    class="required"> * </span></label>
                        <select name="type_of_subscribe" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.type_of_subscribe') }}</option>
                            <option value="default">Default</option>
                            <option value="annual">Annual</option>
                            <option value="percourse">Per course</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="user_type">{{ Lang::get('main.user_type') }}<span
                                    class="required"> * </span></label>
                        <select name="user_type" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.user_type') }}</option>
                            <option value="corporate">Corporate</option>
                            <option value="individual">Individual</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-12">
                        <label for="recruit_id">{{ Lang::get('main.recruit_id') }}<span
                                    class="required"> * </span></label>
                        <select name="recruit_id" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.recruit_id') }}</option>
                            @foreach($recruits as $id)
                                <option value="{{$id}}">{{$id}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="field">{{ Lang::get('main.field') }}<span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="field"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.field') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="facebook">{{ Lang::get('main.facebook') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="facebook"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.facebook') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="linkedin">{{ Lang::get('main.linkedin') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="linkedin"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.linkedin') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="twitter">{{ Lang::get('main.twitter') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="twitter"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.twitter') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="google">{{ Lang::get('main.google') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="google"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.google') }}">
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
        $(document).ready(function(){
            $('#companies').hide();
            $('#type').change(function(){
                if($(this).val()=='company'){
                    $('#companies').show();
                }
                else{
                    $('#companies').hide();
                }
            })
        })

    </script>
@endsection
