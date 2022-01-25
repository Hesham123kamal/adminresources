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
                <a href="{{ URL('/admin/live') }}">{{ Lang::get('main.live') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.live') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.live') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/live','class'=>"form-horizontal",'files'=>true]) !!}
                {!! Form::open(['method'=>'POST','url'=>'admin/live','class'=>"form-horizontal",'files'=>true]) !!}
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
                        <input type="checkbox" class="make-switch" name="active" value="yes" checked data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.active') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.inActive') }}">
                    </div>

                    <div class="form-group col-lg-12">
                        <label class=" control-label" for="short_description">{{ Lang::get('main.short_description') }}
                            <span class="required"> * </span></label>
                        <textarea class="form-control short_description" style="min-height: 300px;"
                                  id="short_description" name="short_description" data-required="1"
                                  placeholder="{{ Lang::get('main.enter').Lang::get('main.short_description') }}"></textarea>
                    </div>

                    @include('auth/description')

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="v_url">{{ Lang::get('main.v_url') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="v_url" name="v_url" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.v_url') }}">
                        </div>
                    </div>

                    {{--<div class="form-group col-lg-12">--}}
                        {{--<label class="control-label" for="audio_link">{{ Lang::get('main.audio_link') }} <span--}}
                                    {{--class="required"> * </span></label>--}}
                        {{--<div class="input-icon right">--}}
                            {{--<i class="fa"></i>--}}
                            {{--<input type="text" class="form-control" value="" name="audio_link" data-required="1"--}}
                                   {{--placeholder="{{ Lang::get('main.enter').Lang::get('main.audio_link') }}">--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="pic">{{ Lang::get('main.pic') }} <span
                                    class="required"> * </span></label>
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
                            <input type="text" class="form-control form-filter input-sm" readonly
                                   name="duetime" placeholder="{{ Lang::get('main.enter').Lang::get('main.duetime') }}">
                            <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                        </div>
                    </div>

                    {{--<div class="form-group col-lg-12">--}}
                        {{--<label for="instructors">{{ Lang::get('main.instructor') }}</label>--}}
                        {{--<select name="instructors" class="module_name sel2 form-control form-filter"--}}
                                {{--id="instructors">--}}
                            {{--<option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.instructor') }}</option>--}}
                            {{--@foreach($instructors as $instructor)--}}
                                {{--<option value="{{$instructor->id}}">{{$instructor->name}}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}
                    {{--</div>--}}

                    <div class="form-group col-lg-12">
                        <label for="location">{{ Lang::get('main.location') }}</label>
                        <select name="location" class="module_name sel2 form-control form-filter"
                                id="location">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.location') }}</option>
                            <option value="egy">EGP</option>
                            <option value="ksa">KSA</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="url">{{ Lang::get('main.url') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" id="url" name="url" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.url') }}">
                        </div>
                    </div>

                    {{--<div class="form-group col-lg-12">--}}
                        {{--<label for="type">{{ Lang::get('main.type') }}</label>--}}
                        {{--<select name="type" class="module_name sel2 form-control form-filter"--}}
                                {{--id="type">--}}
                            {{--<option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.type') }}</option>--}}
                            {{--<option value="online">Online</option>--}}
                            {{--<option value="offline">Offline</option>--}}
                        {{--</select>--}}
                    {{--</div>--}}

                    {{--<div class="form-group col-lg-12">--}}
                        {{--<label for="parent_id">{{ Lang::get('main.parent_id') }}</label>--}}
                        {{--<select name="parent_id" class="module_name sel2 form-control form-filter" id="parent_id">--}}
                            {{--<option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.parent_id') }}</option>--}}
                            {{--@foreach($parent_lives as $parent_live)--}}
                                {{--<option value="{{$parent_live->id}}">{{$parent_live->name}}</option>--}}
                            {{--@endforeach--}}
                        {{--</select>--}}
                    {{--</div>--}}

                    <div class="form-group col-lg-12">
                        <label for="isfree">{{ Lang::get('main.isfree') }}</label>
                        <select name="isfree" class="module_name sel2 form-control form-filter"
                                id="isfree">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.isfree') }}</option>
                            <option value="no">no</option>
                            <option value="yes">yes</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class=" control-label" for="meta_description">{{ Lang::get('main.meta_description') }}
                            <span class="required"> * </span></label>
                        <textarea class="form-control meta_description" style="min-height: 300px;"
                                  id="meta_description" name="meta_description" data-required="1"
                                  placeholder="{{ Lang::get('main.enter').Lang::get('main.meta_description') }}"></textarea>
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
            $('.fromToDate').datepicker({
                rtl: App.isRTL(),
                autoclose: true
            });
            $('.module_name').select2();
            $(document).on('change', '#all_projects', function () {
                if ($(this).is(':checked')) {
                    $("#projects_ids").attr('disabled', 'disabled');
                } else {
                    $("#projects_ids").removeAttr('disabled')
                }
            });
        });
        // $(document).ready(function () {
        //
        //     // function validate(formData, jqForm, options) {
        //     //     var form = jqForm[0];
        //     //     if (!form.file.value) {
        //     //         alert('File not found');
        //     //         return false;
        //     //     }
        //     // }
        //
        //     var bar = $('.bar');
        //     var percent = $('.percent');
        //     var status = $('#status');
        //
        //     $('form').ajaxForm({
        //         // beforeSubmit: validate,
        //         beforeSend: function () {
        //             status.empty();
        //             var percentVal = '0%';
        //             bar.width(percentVal);
        //             percent.html(percentVal);
        //         },
        //         uploadProgress: function (event, position, total, percentCompvare) {
        //             var percentVal = percentCompvare + '%';
        //             bar.width(percentVal);
        //             percent.html(percentVal);
        //         },
        //         success: function (data) {
        //             console.log('data is: ' + data);
        //             if (!data.success) {
        //                 // var percentVal = 'Please try again!';
        //                 // bar.css('background-color', '#f8d7da');
        //                 // percent.css('color', '#721c24');
        //                 // bar.width(percentVal);
        //                 // percent.html(percentVal);
        //                 $("#messages").html(data.message);
        //                 $([document.documentElement, document.body]).animate({
        //                     scrollTop: $("#messages").offset().top
        //                 }, 2000);
        //             } else {
        //                 var percentVal = 'Wait, Saving';
        //                 bar.css('background-color', '#B4F5B4');
        //                 percent.css('color', '#7F98B2');
        //                 bar.width(percentVal);
        //                 percent.html(percentVal);
        //             }
        //         },
        //         complete: function (xhr) {
        //             console.log(xhr);
        //             if (xhr.statusText == 'OK' && xhr.responseText == "" && $('input[name=name]').val() != '' && $('input[name=title]').val() != '') {
        //                 window.location.reload();
        //             } else if (xhr.statusText != 'OK') {
        //                 var percentVal = 'Please try again!';
        //                 bar.css('background-color', '#f8d7da');
        //                 percent.css('color', '#721c24');
        //                 bar.width(percentVal);
        //                 percent.html(percentVal);
        //             }
        //         }
        //     });
        // });
    </script>
@endsection