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
        .progress{
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
                <a href="{{ URL('/admin/become_instructor') }}">{{ Lang::get('main.become_instructor') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
            <li>
                <span>{{ $bi->name }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.become_instructor') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.become_instructor') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/become_instructor/'.$bi->id,'id'=>"form",'class'=>"form-horizontal",'files'=>true]) !!}
                <div class="form-body">

                <div id="message"></div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="name">{{ Lang::get('main.name') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$bi->name}}" id="name" name="name" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                    </div>
                </div>
                {{--<div class="form-group col-lg-3 text-center" style="margin-top:25px;">--}}
                    {{--<input type="checkbox" class="make-switch" name="published" value="yes" data-size="small"--}}
                           {{--@if($bi->published=="yes") checked @endif data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"--}}
                           {{--data-off-text="{{ Lang::get('main.unpublished') }}">--}}
                {{--</div>--}}

                <div class="form-group col-lg-12">
                    <label class="control-label" for="phone">{{ Lang::get('main.phone') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$bi->phone}}" id="phone" name="phone"
                               data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.phone') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="url">{{ Lang::get('main.linkedin') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$bi->linkedin}}" id="linkedin" name="linkedin" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.linkedin') }}">
                    </div>
                </div>

                @include('auth/description',['selectors'=>'.bio','labels'=>[Lang::get('main.bio')],'posts' =>[$bi->bio]])

                <div class="form-group col-lg-12">
                    <label class="control-label">{{Lang::get('main.cv') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <a href="{{assetURL($bi->cv) }}">{{$bi->cv}} </a>
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="cv">{{Lang::get('main.replace') }} {{Lang::get('main.cv') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="file" class="form-control" value="" id="cv"  name="cv"
                               data-required="1">
                        <div class="progress">
                            <div class="bar"></div>
                            <div class="percent">0%</div>
                        </div>
                    </div>
                </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="video">{{ Lang::get('main.video') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{$bi->video}}" id="video" name="video" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.video') }}">
                        </div>
                    </div>
                <div class="form-group col-lg-12">
                    <label class=" control-label" for="courses">{{ Lang::get('main.courses') }}
                        <span class="required"> * </span></label>
                    <textarea class="form-control" style="min-height: 300px;"
                              name="courses" data-required="1"
                              placeholder="{{ Lang::get('main.enter').Lang::get('main.courses') }}">{{$bi->courses}}</textarea>
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
    </div>
@endsection
@section('scriptCode')
    <script>
        $(document).ready(function () {
            var bar = $('.bar');
            var percent = $('.percent');
            var message = $('#message');

            $('form').ajaxForm({
                //beforeSubmit: validate,
                beforeSend: function() {
                    if( $('#cv').val()!='') {
                        message.empty();
                        var percentVal = '0%';
                        var posterValue = $('input[name=file]').fieldValue();
                        bar.width(percentVal);
                        percent.html(percentVal);
                    }
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    if( $('#cv').val()!='') {
                        var percentVal = percentComplete + '%';
                        bar.width(percentVal);
                        percent.html(percentVal);
                    }
                },
                success: function() {
                    if( $('#cv').val()!='') {
                        var percentVal = 'Completed';
                        bar.width(percentVal);
                        percent.html(percentVal);
                    }
                },
                complete: function(xhr) {
                    message.html(xhr.responseJSON);
                    $("html, body").animate({ scrollTop: 0 });
                    return false;
                }
            });
        });


    </script>
@endsection