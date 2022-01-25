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
                <span>{{ Lang::get('main.courses_resources') }}</span>
            </li>
        </ul>
        <!--<div class="page-toolbar">
           <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
               <i class="icon-calendar"></i>&nbsp;
               <span class="thin uppercase hidden-xs"></span>&nbsp;
               <i class="fa fa-angle-down"></i>
           </div>
           </div>-->
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.courses_resources') }}
        <small>{{ Lang::get('main.edit') }}</small>


    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
@endsection
@section('content')
    <style>
        ul li {
            list-style: none;
        }

        ul li ol {
            margin-top: 10px;
        }

        .error {
            color: red !important;
        }
        .page-footer{
            margin-left: 0 !important;
        }
        .row{
            margin-left: 0;
            margin-right: 0;
        }
    </style>
    <style>
        .progress {
            position: relative;
            width: 100%;
            border: 1px solid #7F98B2;
            padding: 1px;
            border-radius: 3px;
            padding-bottom: 21px;
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

        .form-group {
            margin-left: 0px !important;
            margin-right: 0px !important;
        }
    </style>
    <div class="row">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-profiles font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.course_resources') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="row">
                {{--<div class="col-md-12">--}}
                {!! Form::open(['method'=>'PUT','url'=>'admin/courses_resources/'.$course_resource->id,'id'=>'form','files'=>true]) !!}
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
                    <div class="form-group col-md-5">
                        <label class="control-label">{{ Lang::get('main.course') }}
                            <span class="required"> * </span>
                        </label>
                        <select class="form-control sel2" name="course_name">
                            <option value="0">{{ Lang::get('main.select') }}{{ Lang::get('main.course') }}</option>
                            @foreach($courses as $course)
                                <option @if($course_resource->course_id==$course->id) selected="selected"
                                        @endif value="{{$course->id}}">{{$course->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="control-label">{{ Lang::get('main.section') }}
                            <span class="required"> * </span>
                        </label>
                        <select class="form-control sel2" name="section_name">
                            <option value="">{{ Lang::get('main.select') }}{{ Lang::get('main.course') }}</option>
                           {{-- @foreach($courses_sections as $courses_section)
                                <option @if($course_resource->section_id==$courses_section->id) selected="selected"
                                        @endif value="{{ $courses_section->id }}">{{$courses_section->name}}</option>
                            @endforeach--}}
                        </select>
                    </div>
                    <div class="form-group col-md-2 text-center" style="margin-top:25px;">
                        <input type="checkbox" class="make-switch" name="published" value="yes"
                               @if($course_resource->published=="yes") checked @endif data-size="small"
                               data-on-color="success"
                               data-on-text="{{ Lang::get('main.published') }}" data-off-color="default" data-off-text="{{ Lang::get('main.unpublished') }}">
                    </div>
                    <div class="form-group col-md-12">
                        <label class="control-label">{{ Lang::get('main.name') }}

                        </label>
                        <input type="text" name="name" data-required="1" class="form-control"
                               value="{{$course_resource->name}}"/>
                    </div>
                   {{-- <div class="form-group col-md-4">
                        <label class="control-label">{{ Lang::get('main.type') }}
                            <span class="required" aria-required="true"> * </span>
                        </label>
                        <select class="form-control sel2" name="type">
                            <option value="">{{ Lang::get('main.select') }}{{ Lang::get('main.type') }}</option>
                            <option @if($course_resource->type=='default') selected="selected" @endif value="default">@lang('main.default')</option>
                            <option @if($course_resource->type=='exam') selected="selected" @endif value="exam">@lang('main.exam')</option>
                            <option @if($course_resource->type=='training') selected="selected" @endif value="training">@lang('main.training')</option>
                        </select>
                        --}}{{--<div class="mt-radio-list" data-error-container="#form_2_membership_error">
                            <label class="mt-radio">
                                <input type="radio" name="type"
                                       @if($course_resource->type=='default') checked="checked"
                                       @endif value="default"> default
                                <span></span>
                            </label>
                            <label class="mt-radio">
                                <input type="radio" name="type"
                                       @if($course_resource->type=='exam') checked="checked"
                                       @endif value="exam"> exam
                                <span></span>
                            </label>
                            <label class="mt-radio">
                                <input type="radio" name="type"
                                       @if($course_resource->type=='training') checked="checked"
                                       @endif value="training"> training
                                <span></span>
                            </label>
                        </div>
                        <div id="form_2_membership_error"></div>--}}{{--
                    </div>--}}

                    {{--<div class="form-group col-md-4">
                        <label class="control-label">{{ Lang::get('main.questions_numbers') }}
                            <span class="required"> * </span>
                        </label>
                        <input type="text" name="questions_numbers"
                               value="{{$course_resource->questions_numbers}}" data-required="1"
                               class="form-control" placeholder="0"/>
                    </div>--}}

                    <div class="form-group col-md-12">
                        <label class="control-label">{{ Lang::get('main.description') }}
                            <span class="required"> * </span>
                        </label>
                        <textarea class="form-control" rows="5"
                                  name="description"> {{$course_resource->description}}</textarea>
                    </div>



                    <div class="form-group col-md-6">
                        <label class="control-label">{{ Lang::get('main.link') }}

                        </label>
                        <input name="link" type="text" class="form-control"
                               value="{{$course_resource->link}}"/>
                        <span class="help-block"> e.g: http://www.demo.com or http://demo.com </span>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">{{ Lang::get('main.duration') }}
                            <span class="required"> * </span>
                        </label>
                        <input name="duration" value="{{$course_resource->duration}}" type="text"
                               class="form-control" placeholder="0"/>
                    </div>


                    {{-- {{$course_resource->file }} --}}
                    <div class="col-md-12" style="padding: 0px;">
                        <div class="form-group col-md-6">

                            <label class="control-label">{{ Lang::get('main.file') }}

                            </label>
                            <a id="currentFile"
                               href=" {{URL('admin/course_resources_download/'.$course_resource->file)}}"
                               download="{{$course_resource->file}}"> {{$course_resource->file}}</a>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">{{ Lang::get('main.replace') }} {{ Lang::get('main.file') }}
                                <span class="required"> * </span>
                            </label>
                            <input name="file" id="poster" type="file" class="form-control">
                        </div>
                        <div class="form-group col-md-12">
                            <div class="progress">
                                <div class="bar"></div>
                                <div class="percent">0%</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label class="control-label">{{ Lang::get('main.active') }}
                            <span class="required"> * </span>
                        </label>
                        <input name="active" value="{{$course_resource->active}}" type="text"
                               class="form-control" placeholder="0"/>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">{{ Lang::get('main.sent') }}
                            <span class="required"> * </span>
                        </label>
                        <input name="sent" type="text" value="{{$course_resource->sent}}"
                               class="form-control" placeholder="0"/>
                    </div>
                    <div class="col-md-12" style="padding: 0px;">
                        <div class="form-group col-md-6">
                            <label class="control-label">{{ Lang::get('main.isfree') }}
                                <span class="required" aria-required="true"> * </span>
                            </label>
                            <select class="form-control sel2" name="isfree">
                                <option value="">{{ Lang::get('main.select') }}{{ Lang::get('main.type') }}</option>
                                <option @if($course_resource->isfree=='yes') selected="selected" @endif value="yes">@lang('main.yes')</option>
                                <option @if($course_resource->isfree=='no') selected="selected" @endif value="no">@lang('main.no')</option>
                            </select>
                            {{--<div class="mt-radio-list" data-error-container="#form_2_membership_error">
                                <label class="mt-radio">
                                    <input type="radio" name="isfree"
                                           @if($course_resource->isfree=='yes') checked="checked"
                                           @endif value="yes">yes
                                    <span></span>
                                </label>
                                <label class="mt-radio">
                                    <input type="radio" @if($course_resource->isfree =='no') checked="checked"
                                           @endif name="isfree" value="no">no
                                    <span></span>
                                </label>
                            </div>
                            <div id="form_2_membership_error"></div>--}}
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">{{ Lang::get('main.sort') }}
                                <span class="required"> * </span>
                            </label>
                            <input name="sort" type="text" value="{{$course_resource->sort}}"
                                   class="form-control"/>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-12" style="text-align: center">
                                <button id="submit-form" type="submit" class="btn green">{{ Lang::get('main.submit') }}</button>
                                <button type="button" class="btn grey-salsa btn-outline">{{ Lang::get('main.cancel') }}</button>
                            </div>
                            {{Form::close()}}
                        </div>
                    </div>
                </div>
                {{--</div>--}}


                @endsection
                @section('scriptCode')
                    {{--<script--}}
                    {{--src="https://code.jquery.com/jquery-3.3.1.min.js"--}}
                    {{--integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="--}}
                    {{--crossorigin="anonymous"></script>--}}

                    {{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>--}}
                    {{-- <script src="/assets/pages/scripts/form-validation.min.js" type="text/javascript"></script> --}}
                    {{--<script src="https://malsup.github.com/jquery.form.js"></script>--}}
                    {{--<script src="{{ asset('assets/jquery.form.js') }}"></script>--}}


                    <script>
                        $(document).ready(function () {
                            $.ajax({
                                url: '{{ URL('/admin/get_sections/'.$course_resource->course_id ) }}'  ,
                                type: "GET",
                                dataType: "json",
                                beforeSend: function () {
                                    $('#loader').css("visibility", "visible");
                                },
                                success: function (data) {
                                    // console.log(data);
                                    $('select[name="section_name"]').empty();
                                    $('select[name="section_name"]').append('<option value="">{{ Lang::get('main.select') }}{{ Lang::get('main.section') }}</option>');
                                    $.each(data, function (key, value) {

                                        $('select[name="section_name"]').append('<option '+((key=='{{ $course_resource->section_id }}')?'selected=""':'')+' value="' + key + '">' + value + '</option>');

                                    });
                                },
                                complete: function () {
                                    $('#loader').css("visibility", "hidden");
                                }
                            });
                            $('select[name="course_name"]').on('change', function () {
                                var countryId = $(this).val();
                                if (countryId) {
                                    $.ajax({
                                        url: '{{ URL('/admin/get_sections/') }}/' + countryId,
                                        type: "GET",
                                        dataType: "json",
                                        beforeSend: function () {
                                            $('#loader').css("visibility", "visible");
                                        },
                                        success: function (data) {
                                            // console.log(data);
                                            $('select[name="section_name"]').empty();

                                            $.each(data, function (key, value) {

                                                $('select[name="section_name"]').append('<option value="' + key + '">' + value + '</option>');

                                            });
                                        },
                                        complete: function () {
                                            $('#loader').css("visibility", "hidden");
                                        }
                                    });
                                } else {
                                    $('select[name="section_name"]').empty();
                                }

                            });

                        });


                    </script>

                    <script type="text/javascript">

                        function validate(formData, jqForm, options) {
                            var form = jqForm[0];
                            if (form.sent.value.match(/[a-z]/i)) {
                                $("#error_name").html('<div class="alert alert-danger">please include a valid sent</div>');
                                return false;
                            }
                            if (form.sort.value.match(/[a-z]/i)) {
                                $("#error_name").html('<div class="alert alert-danger">please include a valid sort</div>');
                                return false;
                            }
                            if (form.active.value.match(/[a-z]/i)) {
                                $("#error_name").html('<div class="alert alert-danger">please include a valid active</div>');
                                return false;
                            }

                            /*if (form.questions_numbers.value.match(/[a-z]/i)) {
                                $("#error_name").html('<div class="alert alert-danger">please include a valid Questions Numbers</div>');
                                return false;
                            }*/
                            if (form.duration.value.match(/[a-z]/i)) {
                                $("#error_name").html('<div class="alert alert-danger">please include a valid duration</div>');
                                return false;
                            }
                            /* if (!form.name.value) {
                                 $("#error_name").html('<div class="alert alert-danger">please include a file</div>');
                                 return false;
                             }*/

                            if (!form.description.value) {
                                $("#fields_error").html('<div class="alert alert-danger">please include a file</div>');

                                return false;
                            }
                        }

                        (function () {

                            var bar = $('.bar');
                            var percent = $('.percent');
                            var status = $('#status');
                            $('form').ajaxForm({
                                beforeSubmit: validate,
                                beforeSend: function () {
                                    // $("#submit-form").attr('disabled', 'disabled');
                                    status.empty();
                                    var percentVal = '0%';
                                    var posterValue = $('input[name=file]').fieldValue();
                                    bar.width(percentVal)
                                    percent.html(percentVal);
                                },
                                uploadProgress: function (event, position, total, percentComplete) {
                                    var percentVal = percentComplete + '%';
                                    bar.width(percentVal)
                                    percent.html(percentVal);
                                },
                                success: function (data) {
                                    $('#message').html(data.message);
                                    $('html, body').animate({
                                        scrollTop: 0
                                    }, 100);
                                    var percentVal = 'Wait, Saving';
                                    bar.width(percentVal)
                                    percent.html(percentVal);
                                    if(data.success){
                                        setTimeout(function () {
                                            window.location.reload();
                                        },5000)
                                    }
                                    //
                                },
                                complete: function (xhr) {
                                    $("#submit-form").removeAttr('disabled');
                                    status.html(xhr.responseText);
                                    // alert('Uploaded Successfully');
                                    // console.log(this.error.errors.name);
                                    var app = this
                                    // setTimeout(function(){
                                    console.log(app.number);
                                    if (app.number === 3) {
                                        console.log('got here at the error')

                                        console.log(app.fields_error);

                                        $("#fields_error").html(app.fields_error);
                                        app.number = 0;

                                    } else {
                                        console.log('no errors')


                                    }
                                    // }, 2000);


                                },
                                error: function (data) {
                                    console.log(data);
                                    console.log('we got error');
                                    var r = jQuery.parseJSON(data.responseText);
                                    this.fields_error = r;
                                    this.number = 3;
                                    console.log('got here at number = 3 first')
                                }
                            });

                        })();


                    </script>

                    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
                    <script>
                        // just for the demos, avoids form submit
                        jQuery.validator.setDefaults({
                            debug: true,
                            success: "valid"
                        });


                        $("#form_sample_1").validate({

                            rules: {
                                sort: {
                                    required: true,
                                    number: true
                                },
                                sent: {
                                    required: true,
                                    number: true
                                },
                                active: {
                                    required: true,
                                    number: true
                                },
                                description: {
                                    required: true
                                },
                                questions_numbers: {
                                    required: true,
                                    number: true
                                },
                                duration: {
                                    required: true,
                                    number: true
                                }
                            },

                        });


                    </script>

                    <script>
                        {{--$(document).ready(function () {--}}
                        {{--var token = "{{csrf_token()}}";--}}
                        {{--$('#currentFile').on('click', function (e) {--}}
                        {{--e.preventDefault();--}}
                        {{--var path = "{{$course_resource->file}}";--}}
                        {{--console.log(path);--}}
                        {{--$.ajax({--}}
                        {{--url: '{{ URL('admin/course_resources_download') }}',--}}
                        {{--type: "POST",--}}
                        {{--dataType: "json",--}}
                        {{--data: {file: path, _token: token},--}}
                        {{--success: function (data) {--}}
                        {{--console.log('successs');--}}
                        {{--console.log(data);--}}
                        {{--},--}}
                        {{--complete: function (info) {--}}
                        {{--console.log('compl');--}}
                        {{--console.log(info);--}}
                        {{--}--}}
                        {{--});--}}
                        {{--// window.location.href = 'http://localhost/e3melbusinessV4/adminResources/public/admin';--}}
                        {{--})--}}
                        {{--})--}}
                    </script>

@endsection