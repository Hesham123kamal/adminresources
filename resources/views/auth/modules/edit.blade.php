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
                <a href="{{ URL('/admin/modules') }}">{{ Lang::get('main.modules') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
            <li>
                <span>{{ $module->name }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.modules') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.modules') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/modules/'.$module->id,'id'=>'addModulesForm','class'=>"form-horizontal",'files'=>true]) !!}
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
                        <input type="text" class="form-control" value="{{$module->name}}" id="name" name="name" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                    </div>
                </div>
                <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                    <input type="checkbox" class="make-switch" name="active" value="yes" @if($module->published=="yes") checked @endif data-size="small" data-on-color="success" data-on-text="{{ Lang::get('main.active') }}" data-off-color="default" data-off-text="{{ Lang::get('main.inActive') }}">
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="en_name">{{ Lang::get('main.en_name') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$module->en_name}}" id="en_name" name="en_name" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.en_name') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="code">{{ Lang::get('main.code') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$module->code}}" id="code" name="code" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.code') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="questions_numbers">{{ Lang::get('main.questions_numbers') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="number" min="0" class="form-control" value="{{$module->questions_numbers}}" id="questions_numbers" name="questions_numbers" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.questions_numbers') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="egy_price">{{ Lang::get('main.egy_price') }} </label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="number" min="0" class="form-control" value="{{$module->egy_price}}" id="egy_price" name="egy_price" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.egy_price') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="ksa_price">{{ Lang::get('main.ksa_price') }} </label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="number" min="0" class="form-control" value="{{$module->ksa_price}}" id="ksa_price" name="ksa_price" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.ksa_price') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="short_description">{{ Lang::get('main.short_description') }} </label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$module->short_description}} " id="short_description" name="short_description" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.short_description') }}">
                    </div>
                </div>

                @include('auth/description',['posts' =>[$module->description]])

                <div class="form-group col-lg-12">
                    <label class="control-label">{{ Lang::get('main.pic')}}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <img width="20%" src="{{assetURL($module->image) }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="pic">{{ Lang::get('main.replace')}} {{ Lang::get('main.pic')}}<span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="file" class="form-control" value="" id="pic" accept="image/*"
                               name="pic" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.pic') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="url">{{ Lang::get('main.url') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$module->url}}" id="url" name="url" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.url') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="intro_video">{{ Lang::get('main.intro_video') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" value="{{$module->intro_video}}" class="form-control" name="intro_video" placeholder="{{ Lang::get('main.enter').Lang::get('main.intro_video') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label for="sent">{{ Lang::get('main.sent') }}</label>
                    <select name="sent" class="module_name sel2 form-control form-filter"
                            id="sent">
                        <option value="{{$module->sent}} ">{{$module->sent}}</option>
                        <option value="yes">yes</option>
                        <option value="no">no</option>
                    </select>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="certificate_increment">{{ Lang::get('main.certificate_increment') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$module->certificate_increment}}" id="certificate_increment" name="certificate_increment" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.certificate_increment') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="egy_sale_price">{{ Lang::get('main.egy_sale_price') }} <span
                                class="required"> * </span> </label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="number" min="0" class="form-control" value="{{$module->egy_sale_price}}" id="egy_sale_price" name="egy_sale_price" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.egy_sale_price') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="ksa_sale_price">{{ Lang::get('main.ksa_sale_price') }}  <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="number" min="0" class="form-control" value="{{$module->ksa_sale_price}}" id="ksa_sale_price" name="ksa_sale_price" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.ksa_sale_price') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="tool_eg_price">{{ Lang::get('main.tool_eg_price') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="number" min="0" class="form-control" value="{{$module->tool_eg_price}}" id="tool_eg_price" name="tool_eg_price" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.tool_eg_price') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="tool_ksa_price">{{ Lang::get('main.tool_ksa_price') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="number" min="0" class="form-control" value="{{$module->tool_ksa_price}}" id="tool_ksa_price" name="tool_ksa_price" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.tool_ksa_price') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="sort">{{ Lang::get('main.sort') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="number" min="0" class="form-control" value="{{$module->sort}}" id="sort" name="sort" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.sort') }}">
                    </div>
                </div>

                <div  class="form-group col-lg-12">
                    <label class="control-label" for="question_time">{{ Lang::get('main.question_time') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$module->question_time}}"  name="question_time" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.question_time') }}">
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
            $(document).on('change', '#all_projects', function () {
                if ($(this).is(':checked')) {
                    $("#projects_ids").attr('disabled', 'disabled');
                } else {
                    $("#projects_ids").removeAttr('disabled')
                }
            });
            @if(PerUser('modules_active'))
            $(document).on('change', '.changeStatues', function () {
                var statues = $(this).is(':checked');
                var id = $(this).attr('data-id');
                if (statues) {
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/modules/activation') }}",
                        data: {"active": 1, "id": id, _token: token},
                        success: function (msg) {
                            $("#errors").html(msg);
                        }
                    });
                } else {
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/modules/activation') }}",
                        data: {"active": 0, "id": id, _token: token},
                        success: function (msg) {
                            $("#errors").html(msg);
                        }
                    });
                }
            });
            @endif
            $('.fromToDate').datepicker({
                rtl: App.isRTL(),
                autoclose: true
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
        //                 var percentVal = 'Please try again!';
        //                 bar.css('background-color', '#f8d7da');
        //                 percent.css('color', '#721c24');
        //                 bar.width(percentVal);
        //                 percent.html(percentVal);
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