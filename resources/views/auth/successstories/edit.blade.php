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
    <style>
        .progress {
            position: relative;
            width: 100%;
            height: 30px !important;
            border: 1px solid #7F98B2;
            padding: 1px;
            border-radius: 3px;
        }

        .bar {
            background-color: #B4F5B4;
            width: 0%;
            height: 25px;
            border-radius: 3px;
        }

        .percent {
            position: absolute;
            display: inline-block;
            top: 3px;
            left: 48%;
            color: #7F98B2;
        }
    </style>
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
                <a href="{{ URL('/admin/successstories') }}">{{ Lang::get('main.successstories') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
            <li>
                <span>{{ $successstory->name }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.successstories') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.successstories') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/successstories/'.$successstory->id,'id'=>'addSuccessstoriesForm','class'=>"form-horizontal",'files'=>true]) !!}
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

                <div class="form-group col-lg-9">
                    <label class="control-label" for="name">{{ Lang::get('main.name') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$successstory->name}}" id="name" name="name" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                    </div>
                </div>
                <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                    <input type="checkbox" class="make-switch" name="published" value="yes"
                           @if($successstory->published=="yes") checked @endif data-size="small" data-on-color="success"
                           data-on-text="{{ Lang::get('main.published') }}" data-off-color="default" data-off-text="{{ Lang::get('main.unpublished') }}">
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="short_description">{{ Lang::get('main.short_description') }} </label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$successstory->short_description}} " id="short_description" name="short_description" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.short_description') }}">
                    </div>
                </div>

                @include('auth/description',['posts' =>[$successstory->description]])

                <div class="form-group col-lg-12">
                    <label class="control-label" for="v_url">{{ Lang::get('main.v_url') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$successstory->link}}" id="v_url" name="v_url" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.v_url') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label">{{ Lang::get('main.pic') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <img style="width:20%;" src="{{assetURL($successstory->image) }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="pic">{{ Lang::get('main.replace') }} {{ Lang::get('main.pic') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="file" class="form-control" value="" id="pic" accept="image/*"
                               name="pic" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.pic') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label for="duetime">{{ Lang::get('main.duetime') }}</label>
                    <div class="input-group date fromToDate margin-bottom-5" data-date-format="yyyy-mm-dd">
                        <input type="text" class="form-control form-filter input-sm" readonly value="{{$successstory->duetime}}"
                               name="duetime" placeholder="{{ Lang::get('main.enter').Lang::get('main.duetime') }}">
                        <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label for="instructor">{{ Lang::get('main.instructor') }}<span
                                class="required"> * </span></label>
                    <select name="instructor" class="module_name sel2 form-control form-filter"
                            id="instructor">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.instructor') }}</option>
                        @foreach($instructors as $instructor)
                            <option @if($instructor->id==$successstory->instractor)  selected="selected" @endif value="{{$instructor->id}}">{{$instructor->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="level">{{ Lang::get('main.level') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$successstory->level}}" id="level" name="level" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.level') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label for="location">{{ Lang::get('main.location') }}</label>
                    <select name="location" class="module_name sel2 form-control form-filter"
                            id="location">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.location') }}</option>
                        <option @if($successstory->location=='egy') selected="selected" @endif value="egy">EGP</option>
                        <option @if($successstory->location=='ksa') selected="selected" @endif value="ksa">KSA</option>
                    </select>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="url">{{ Lang::get('main.url') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$successstory->url}}" id="url" name="url" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.url') }}">
                    </div>
                </div>

                {{--<div class="form-group col-lg-12">--}}
                {{--<label class="control-label" for="rating">{{ Lang::get('main.rating') }} <span--}}
                {{--class="required"> * </span></label>--}}
                {{--<div class="input-icon right">--}}
                {{--<i class="fa"></i>--}}
                {{--<input type="number" min="1" maxlength="5" class="form-control" value="" id="rating" name="rating" data-required="1"--}}
                {{--placeholder="{{ Lang::get('main.enter').Lang::get('main.rating') }}">--}}
                {{--</div>--}}
                {{--</div>--}}

                {{--<div class="form-group col-lg-12">--}}
                {{--<label class="control-label" for="rating_count">{{ Lang::get('main.rating_count') }} <span--}}
                {{--class="required"> * </span></label>--}}
                {{--<div class="input-icon right">--}}
                {{--<i class="fa"></i>--}}
                {{--<input type="number" min="1" class="form-control" value="" id="rating_count" name="rating_count" data-required="1"--}}
                {{--placeholder="{{ Lang::get('main.enter').Lang::get('main.rating_count') }}">--}}
                {{--</div>--}}
                {{--</div>--}}

                <div class="form-group col-lg-12">
                    <label for="ispublic">{{ Lang::get('main.ispublic') }}<span
                                class="required"> * </span></label>
                    <select name="ispublic" class="module_name sel2 form-control form-filter"
                            id="ispublic">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.ispublic') }}</option>
                        <option @if($successstory->ispublic ==0) selected="selected" @endif value="0">No</option>
                        <option @if($successstory->ispublic ==1) selected="selected" @endif value="1">Yes</option>
                    </select>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="meta_description">{{ Lang::get('main.meta_description') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$successstory->meta_description}} " id="meta_description" name="meta_description" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.meta_description') }}">
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
            $('.fromToDate').datepicker({
                rtl: App.isRTL(),
                autoclose: true
            });
        });
    </script>
@endsection