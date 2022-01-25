@extends('auth.layouts.app')
@section('pageTitle')
    <title>{{ Lang::get('main.home_page_title') }}</title>
    <style>
        /*.progress {*/
            /*position: relative;*/
            /*width: 100%;*/
            /*height: 30px !important;*/
            /*border: 1px solid #7F98B2;*/
            /*padding: 1px;*/
            /*border-radius: 3px;*/
        /*}*/

        /*.bar {*/
            /*background-color: #B4F5B4;*/
            /*width: 0%;*/
            /*height: 25px;*/
            /*border-radius: 3px;*/
        /*}*/

        /*.percent {*/
            /*position: absolute;*/
            /*display: inline-block;*/
            /*top: 3px;*/
            /*left: 48%;*/
            /*color: #7F98B2;*/
        /*}*/
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
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.mba_progress') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.mba_progress') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/mba_progress/export','class'=>"form-horizontal",'files'=>true]) !!}
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
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="csv_file">{{ Lang::get('main.csv_file') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="file" class="form-control" value="" id="csv_file" accept=".csv"
                                   name="csv_file" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.csv_file') }}">
                            {{--<div class="progress">--}}
                                {{--<div class="bar"></div>--}}
                                {{--<div class="percent">0%</div>--}}
                            {{--</div>--}}
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <div class="text-center col-lg-12">
                        <button type="submit" class="btn green">{{ Lang::get('main.export') }}</button>
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
        {{--function validate(formData, jqForm, options) {--}}
            {{--var form = jqForm[0];--}}
            {{--if (!form.csv_file.value) {--}}
                {{--alert(" {{ Lang::get('main.select') }}{{ Lang::get('main.csv_file') }}");--}}
                {{--return false;--}}
            {{--}--}}
        {{--}--}}
        {{--$(document).ready(function () {--}}

            {{--var bar = $('.bar');--}}
            {{--var percent = $('.percent');--}}
            {{--var message = $('#message');--}}
            {{--$('form').ajaxForm({--}}
                {{--beforeSubmit: validate,--}}
                {{--beforeSend: function() {--}}
                    {{--message.empty();--}}
                    {{--var percentVal = '0%';--}}
                    {{--var posterValue = $('input[name=file]').fieldValue();--}}
                    {{--bar.width(percentVal);--}}
                    {{--percent.html(percentVal);--}}
                {{--},--}}
                {{--uploadProgress: function(event, position, total, percentComplete) {--}}
                    {{--var percentVal = percentComplete + '%';--}}
                    {{--bar.width(percentVal);--}}
                    {{--percent.html(percentVal);--}}
                {{--},--}}
                {{--success: function() {--}}
                    {{--var percentVal = "{{ Lang::get('main.completed') }}";--}}
                    {{--bar.width(percentVal);--}}
                    {{--percent.html(percentVal);--}}
                {{--},--}}
                {{--complete: function(xhr) {--}}
                    {{--message.html(xhr.responseJSON);--}}
                    {{--$("html, body").animate({ scrollTop: 0 });--}}
                    {{--return false;--}}
                {{--}--}}
            {{--});--}}
        {{--});--}}
    </script>
@endsection