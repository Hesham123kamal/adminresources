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
                <a href="{{ URL('/admin/medical_sup_categories') }}">{{ Lang::get('main.medical_sup_categories') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.medical_sup_categories') }}
        <small>{{ Lang::get('main.add') }}</small>
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.medical_sup_categories') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/medical_sup_categories','class'=>"form-horizontal",'files'=>true]) !!}
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
                        <label class="control-label" for="category_id">
                            {{ Lang::get('main.medical_category') }}
                            <span class="required"> * </span>
                        </label>
                        <select class="form-control form-filter select2" id="selectall-course" name="category_id">
                            <option value="">@lang('main.select')@lang('main.medical_category')</option>
                            @foreach($medical_categories as $key=>$value)
                                <option @if(old('category_id')==$key) selected="selected" @endif value="{{$key}}">{{$value}}</option>
                            @endforeach
                        </select>
                    </div>
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

                    <div class="col-lg-12 form-group">
                        <label class="control-label" for="course">{{ Lang::get('main.courses') }}</label>
                        {{--<button type="button" class="btn btn-primary btn-xs" id="selectbtn-tag">--}}
                            {{--{{ Lang::get('main.select_all')}}--}}
                        {{--</button>--}}
                        {{--<button type="button" class="btn btn-primary btn-xs" id="deselectbtn-tag">--}}
                            {{--{{ Lang::get('main.deselect_all')}}--}}
                        {{--</button>--}}
                        {!! Form::select('course[]', $courses, old('course'), ['class' => 'form-control form-filter select2', 'multiple' => 'multiple', 'id' => 'selectall-course']) !!}
                        <p class="help-block"></p>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="picture">{{ Lang::get('main.image') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="file" class="form-control" value="" accept="image/*"
                                   name="picture" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.pic') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="price">{{ Lang::get('main.price') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="price" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.price') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="url">{{ Lang::get('main.url') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="url" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.url') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="short_description">{{ Lang::get('main.short_description') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <textarea style="height:200px;" class="form-control" name="short_description"
                                      placeholder="{{ Lang::get('main.enter').Lang::get('main.short_description') }}"></textarea>
                        </div>
                    </div>

                    @include('auth/description')

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
            $('.fromToDate').datepicker({
                rtl: App.isRTL(),
                autoclose: true,
                orientation:"bottom"
            });
        });

        // $("#selectbtn-tag").click(function(){
        //     $("#selectall-tag > option").prop("selected","selected");
        //     $("#selectall-tag").trigger("change");
        // });
        // $("#deselectbtn-tag").click(function(){
        //     $("#selectall-tag > option").prop("selected","");
        //     $("#selectall-tag").trigger("change");
        // });

        $(document).ready(function () {
            $('.select2').select2();
        });

    </script>
@endsection