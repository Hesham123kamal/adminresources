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
                <a href="{{ URL('/admin/books_playlist') }}">{{ Lang::get('main.books_playlist') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.books_playlist') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.books_playlist') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/books_playlist','id'=>'addbooks_playlistForm','class'=>"form-horizontal",'files'=>true]) !!}
                <div class="form-body">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_error') }}
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_success') }}
                    </div>
                    <div id="message"></div>
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
                        <label for="book">{{ Lang::get('main.book') }}<span
                                    class="required"> * </span></label>
                        <select name="book" class=" sel2 form-control form-filter"
                                id="book">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.book') }}</option>
                            @foreach($books as $book)
                                <option value="{{$book->id}}">{{$book->title}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="audio">{{ Lang::get('main.audio') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="file" class="form-control" value="" id="audio" accept=".mp3,.wav"
                                   name="audio" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.audio') }}">
                            <div class="progress">
                                <div class="bar"></div>
                                <div class="percent">0%</div>
                            </div>
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
        function validate(formData, jqForm, options) {
            var form = jqForm[0];
            if (!form.audio.value) {
                alert(" {{ Lang::get('main.select') }}{{ Lang::get('main.audio') }}");
                return false;
            }
        }
        $(document).ready(function () {

            var bar = $('.bar');
            var percent = $('.percent');
            var message = $('#message');
            $('form').ajaxForm({
                beforeSubmit: validate,
                beforeSend: function() {
                    message.empty();
                    var percentVal = '0%';
                    var posterValue = $('input[name=file]').fieldValue();
                    bar.width(percentVal);
                    percent.html(percentVal);
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    var percentVal = percentComplete + '%';
                    bar.width(percentVal);
                    percent.html(percentVal);
                },
                success: function() {
                    var percentVal = "{{ Lang::get('main.completed') }}";
                    bar.width(percentVal);
                    percent.html(percentVal);
                },
                complete: function(xhr) {
                    message.html(xhr.responseJSON.msg);
                    $("html, body").animate({ scrollTop: 0 });
                    if(xhr.responseJSON.success==true){
                        $('#name').val("");
                        $("#book").val($("#book option:first").val()).select2();
                        $('#audio').val("");
                    }
                    return false;
                }
            });
        });
    </script>
@endsection