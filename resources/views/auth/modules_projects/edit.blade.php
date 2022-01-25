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
                <a href="{{ URL('/admin/modules_projects') }}">{{ Lang::get('main.modules_projects') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
            <li>
                <span>{{ $module_project->name }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.modules_projects') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.modules_projects') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/modules_projects/'.$module_project->id,'id'=>'addModules_projectsForm','class'=>"form-horizontal",'files'=>true]) !!}
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
                <div class="form-group col-lg-9">
                    <label class="control-label" for="name">{{ Lang::get('main.name') }} <span
                                class="required"> * </span></label>
                    <select id="name" class="form-control sel2 module_name" required name="name">
                        <option value=" ">{{ Lang::get('main.select')}} {{Lang::get('main.mba') }}</option>
                        @foreach($mba as $single_mba)
                            <option @if($single_mba->id == $module_project->module_id ) selected="selected"
                                    @endif value="{{ $single_mba->id}}">{{ $single_mba->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                    <input type="checkbox" class="make-switch" name="active" value="1" @if($module_project->active==1) checked @endif data-size="small" data-on-color="success" data-on-text="{{ Lang::get('main.active') }}" data-off-color="default" data-off-text="{{ Lang::get('main.inActive') }}">
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="title">{{ Lang::get('main.title') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$module_project->title}}" id="title"
                               name="title" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.title') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label">{{ Lang::get('main.file')}}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <a href="{{assetURL($module_project->file) }}">{{$module_project->file}} </a>
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="file">{{ Lang::get('main.replace')}} {{Lang::get('main.file') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="file" class="form-control" value="" id="file"  accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" name="file" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.file') }}">
                        <div class="progress">
                            <div class="bar"></div>
                            <div class="percent">0%</div>
                        </div>
                    </div>
                </div>
                <div class="clearfix" style="height: 30px"></div>
                <div class="text-center col-lg-12">
                    <button type="submit" class="btn green">{{ Lang::get('main.save') }}</button>
                </div>
                <div class="clearfix" style="height: 30px"></div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@section('scriptCode')
    <script>

        $(document).ready(function () {
            $(document).on('change', '#all_projects', function () {
                if ($(this).is(':checked')) {
                    $("#projects_ids").attr('disabled', 'disabled');
                } else {
                    $("#projects_ids").removeAttr('disabled')
                }
            });
        });
        $(document).ready(function () {

            // function validate(formData, jqForm, options) {
            //     var form = jqForm[0];
            //     if (!form.file.value) {
            //         alert('File not found');
            //         return false;
            //     }
            // }

            var bar = $('.bar');
            var percent = $('.percent');
            var status = $('#status');

            $('form').ajaxForm({
                // beforeSubmit: validate,
                beforeSend: function () {
                    status.empty();
                    var percentVal = '0%';
                    bar.width(percentVal);
                    percent.html(percentVal);
                },
                uploadProgress: function (event, position, total, percentCompvare) {
                    var percentVal = percentCompvare + '%';
                    bar.width(percentVal);
                    percent.html(percentVal);
                },
                success: function (data) {
                    console.log('data is: ' + data);
                    if (!data.success) {
                        var percentVal = 'Please try again!';
                        bar.css('background-color', '#f8d7da');
                        percent.css('color', '#721c24');
                        bar.width(percentVal);
                        percent.html(percentVal);
                        $("#messages").html(data.message);
                        $([document.documentElement, document.body]).animate({
                            scrollTop: $("#messages").offset().top
                        }, 2000);
                    } else {
                        var percentVal = 'Wait, Saving';
                        bar.css('background-color', '#B4F5B4');
                        percent.css('color', '#7F98B2');
                        bar.width(percentVal);
                        percent.html(percentVal);
                    }
                },
                complete: function (xhr) {
                    $("#messages").html(xhr.responseJSON.message);
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $("#messages").offset().top
                    }, 2000);

                    // console.log(xhr);
                    // if (xhr.statusText == 'OK' && xhr.responseText == "" && $('input[name=name]').val() != '' && $('input[name=title]').val() != '') {
                    //     window.location.reload();
                    // } else if (xhr.statusText != 'OK') {
                    //     var percentVal = 'Please try again!';
                    //     bar.css('background-color', '#f8d7da');
                    //     percent.css('color', '#721c24');
                    //     bar.width(percentVal);
                    //     percent.html(percentVal);
                    // }
                }
            });
        });
    </script>
@endsection