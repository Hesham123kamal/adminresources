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
                <a href="{{ URL('/admin/courses') }}">{{ Lang::get('main.courses') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.courses') }}
        <small>{{ Lang::get('main.add') }}</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
@endsection
@section('content')
    <style>
        .form-group {
            margin-left: 0px !important;
            margin-right: 0px !important;
        }
    </style>
    <div class="row">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-users font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.courses') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/courses','class'=>"form-horizontal",'files'=>true]) !!}
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
                    <div class="form-group col-lg-7">
                        <label class="control-label" for="name">{{ Lang::get('main.name') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="name" name="name" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-2">
                        <label for="direction">{{ Lang::get('main.direction') }}</label>
                        <select id="direction" name="direction" class="form-control">
                            <option value="rtl" selected>Arabic</option>
                            <option value="ltr">English</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                        <input type="checkbox" class="make-switch" name="published" value="yes" checked
                               data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.unpublished') }}">
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-lg-6">
                        <label class="control-label"
                               for="short_description">{{ Lang::get('main.short_description') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="short_description"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.short_description') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label class="control-label" for="lectures">{{ Lang::get('main.lectures') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="lectures"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.lectures') }}">
                        </div>
                    </div>

                    @include('auth/description',['selectors'=>'.description,.get_from_course,.references', 'labels'=>[Lang::get('main.description'),Lang::get('main.get_from_course'),Lang::get('main.references')] ])

                    <div class="form-group col-lg-6">
                        <label class="control-label" for="image">{{ Lang::get('main.image') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="file" class="form-control" value="" id="image" accept="image/*"
                                   name="image" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.image') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label class="control-label" for="length">{{ Lang::get('main.length') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="length"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.length') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label class="control-label" for="url">{{ Lang::get('main.url') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="url" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.url') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label class="control-label" for="code">{{ Lang::get('main.code') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="code" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.code') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label class="control-label" for="egy_price">{{ Lang::get('main.egy_price') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="egy_price" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.egy_price') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label class="control-label" for="ksa_price">{{ Lang::get('main.ksa_price') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="ksa_price" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.ksa_price') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label class="control-label" for="intro_video">{{ Lang::get('main.intro_video') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="intro_video" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.intro_video') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label class="control-label" for="meta_description">{{ Lang::get('main.meta_description') }}
                            <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="meta_description" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.meta_description') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-6">
                        <label class="control-label"
                               for="curriculum_number">{{ Lang::get('main.curriculum_number') }}</label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="curriculum_number"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.curriculum_number') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-6">
                        <label class="control-label" for="en_name">{{ Lang::get('main.en_name') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="en_name" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.en_name') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-6">
                        <label for="instructor">{{ Lang::get('main.instructor') }}<span
                                    class="required"> * </span></label>
                        <select name="instructor" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.instructor') }}</option>
                            @foreach($instructors as $id=>$name)
                                <option value="{{$id}}">{{$name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-6">
                        <label for="type">{{ Lang::get('main.isclose') }}<span
                                    class="required"> * </span></label>
                        <select name="isclose" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.isclose') }}</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    {{--<div class="form-group col-lg-6">--}}
                        {{--<label for="active">{{ Lang::get('main.active') }}<span--}}
                                    {{--class="required"> * </span></label>--}}
                        {{--<select name="active" class="module_name sel2 form-control form-filter">--}}
                            {{--<option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.active') }}</option>--}}
                            {{--<option value="1">Yes</option>--}}
                            {{--<option value="0">No</option>--}}
                        {{--</select>--}}
                    {{--</div>--}}
                    <div class="form-group col-lg-6">
                        <label for="location">{{ Lang::get('main.location') }}</label>
                        <select name="location" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.location') }}</option>
                            <option value="egy">egy</option>
                            <option value="ksa">ksa</option>
                            <option value="onlyeg">onlyeg</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-6">
                        <label for="course_type">{{ Lang::get('main.course_type') }}</label>
                        <select name="course_type" class="sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.course_type') }}</option>
                            <option value="paid">paid</option>
                            <option value="free">free</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <label for="category">{{ Lang::get('main.all_category') }}</label>
                        <select id="category" name="category" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.all_category') }}</option>
                            @foreach($categories as $id=>$name)
                                <option value="{{$id}}">{{$name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <label for="sub_category">{{ Lang::get('main.sub_category') }}</label>
                        <select id="sub_category" name="sub_category" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.sub_category') }}</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-6">
                        <label for="show_on">{{ Lang::get('main.show_on') }}</label>
                        <select name="show_on" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.show_on') }}</option>
                            <option value="courses">courses</option>
{{--                            <option value="diplomas">diplomas</option>--}}
                            <option value="diplomas_mba">diplomas_mba</option>
{{--                            <option value="mba">mba</option>--}}
                            @if(!PerUser('remove_medical')){
                            <option value="medical">medical</option>
                            @endif
                            <option value="all">all</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-6">
                        <label class="control-label" for="sort">{{ Lang::get('main.sort') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="sort" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.sort') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label for="statues">{{ Lang::get('main.statues') }} <span
                                    class="required"> * </span></label>
                        <select name="statues" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.statues') }}</option>
                            <option selected="selected" value="new">new</option>
                            <option value="old">old</option>
                        </select>
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
            $('#category').change(function () {
                $.ajax({
                    type: "POST",
                    url: "{{ URL('admin/courses/getSubCategoriesByCategoryId') }}",
                    data: {"category_id": $(this).val(), "_token": "{{ csrf_token() }}"},
                    success: function (options) {
                        $('#sub_category').empty().append(options);
                    }
                });
            })

        })

    </script>
@endsection
