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
                <a href="{{ URL('/admin/diploma_courses') }}">{{ Lang::get('main.diploma_courses') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
            <li>
                <span>{{ $course->name }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.diploma_courses') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.diploma_courses') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/diploma_courses/'.$course->id,'class'=>"form-horizontal",'files'=>true]) !!}
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
                    <label class="control-label" for="title">{{ Lang::get('main.name') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$course->name}}" id="name" name="name"
                               data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                    </div>
                </div>
                {{--<div class="form-group col-lg-3 text-center" style="margin-top:25px;">--}}
                    {{--<input type="checkbox" class="make-switch" name="published" value="yes"--}}
                           {{--@if($course->published=="yes") checked @endif data-size="small" data-on-color="success"--}}
                           {{--data-on-text="{{ Lang::get('main.published') }}" data-off-color="default" data-off-text="{{ Lang::get('main.unpublished') }}">--}}
                {{--</div>--}}

                @include('auth/description',['posts' =>[$course->description]])

                <div class="form-group col-lg-12">
                    <label class="control-label">{{ Lang::get('main.image') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <img style="width:20%;" src="{{assetURL($course->image) }}">
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
                    <label for="diploma">{{ Lang::get('main.diploma') }}</label>
                    <select name="diploma" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.diploma') }}</option>
                        @foreach($diplomas as $id=>$name)
                            <option @if($course->diploma_id==$id)  selected="selected"
                                    @endif value="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-12">
                    <label for="type">{{ Lang::get('main.type') }}<span
                                class="required"> * </span></label>
                    <select name="type" id="type" class="module_name sel2 form-control form-filter">
                        <option @if($course->type=='normal') selected="selected" @endif value="normal">{{ Lang::get('main.normal') }}</option>
                        <option @if($course->type=='according_to_country') selected="selected" @endif value="according_to_country">{{ Lang::get('main.according_to_country') }}</option>
                    </select>
                </div>

                <div class="form-group col-lg-12">
                    <label for="related_course">{{ Lang::get('main.related_course') }}</label>
                    <select name="related_course" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.related_course') }}</option>
                        @foreach($courses as $related_course)
                            <option @if($related_course->id==$course->related_course)  selected="selected"
                                    @endif value="{{$related_course->id}}">{{$related_course->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-12 @if($course->type=='normal'){{ 'hidden' }}@endif" id="related_course_ksa_div">
                    <label for="related_course_ksa">{{ Lang::get('main.related_course_ksa') }}</label>
                    <select name="related_course_ksa" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.related_course_ksa') }}</option>
                        @foreach($courses as $related_course)
                            <option @if($related_course->id==$course->related_course_ksa)  selected="selected"
                                    @endif value="{{$related_course->id}}">{{$related_course->name}}</option>
                        @endforeach
                    </select>
                </div>


                <div class="form-group col-lg-12">
                    <label class="control-label" for="order">{{ Lang::get('main.sort') }} (?????????? ???????? ???????????????? ???????? ??????????????????) <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$course->order}}" id="order"
                               name="order" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.sort') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="sort">{{ Lang::get('main.order') }} (?????????? ???????? ???????????????? ???? ????????????????)<span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$course->sort}}" id="sort"
                               name="sort" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.order') }}">
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
            $('.eventdate').datepicker({
                rtl: App.isRTL(),
                autoclose: true,
                format: 'yyyy-mm-dd'
            });
        });
        $(document).on('change','#type',function(){
            type=$("#type").val()
            if(type=='according_to_country'){
                $("#related_course_ksa_div").removeClass('hidden')
            }else{
                $("#related_course_ksa_div").addClass('hidden');

            }
            $('.sel2').each(function(){
                $(this).select2('destroy')
            });
            $('.sel2').select2();
        })
    </script>
@endsection
