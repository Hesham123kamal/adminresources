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
                <span>{{ Lang::get('main.webinar_resources') }}</span>
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
    <h1 class="page-title"> {{ Lang::get('main.webinar_resources') }}
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
    </style>
    <div class="row">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-profiles font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.webinar_resources') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <p id="fields_error"></p>
                    <p id="error_name"></p>
                    {!! Form::open(['method'=>'PUT','url'=>'admin/webinar_resources/'.$webinar_resource->id,'id'=>'form','files'=>true]) !!}
                    <div class="form-body">
                        <div class="alert alert-danger display-hide">
                            <button class="close" data-close="alert"></button>
                            {{ Lang::get('main.form_validation_error') }}
                        </div>
                        <div class="alert alert-success display-hide">
                            <button class="close" data-close="alert"></button>
                            {{ Lang::get('main.form_validation_success') }}
                        </div>

                        {{-- form  start ================================= --}}
                        <div class="form-group  col-md-12">
                            <label class="control-label">{{ Lang::get('main.webinar') }}
                                <span class="required"> * </span>
                            </label>
                            <div class="">
                                <select class="form-control sel2" name="webinar_name">
                                    <option value="0">{{ Lang::get('main.select') }} {{ Lang::get('main.webinar') }}</option>
                                    @foreach($webinars as $webinar)
                                        <option @if($webinar_resource->webinar_id==$webinar->id) selected="selected"
                                                @endif value="{{$webinar->id}}">{{$webinar->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group   col-md-12">
                            <label class="control-label">{{ Lang::get('main.name') }}
                                <span class="required"> * </span>
                            </label>
                            <div class="">
                                <input type="text" value="{{$webinar_resource->name}}" name="name" data-required="1"
                                       class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group   col-md-12">
                            <label class="control-label">{{ Lang::get('main.description') }}
                                <span class="required"> * </span>
                            </label>

                            <div class="">
                                    <textarea value="" class="form-control" rows="5"
                                              name="description">{{$webinar_resource->description}}</textarea>
                            </div>
                        </div>

                        <div class="form-group  col-md-12">
                            <label class="control-label">{{ Lang::get('main.active') }}
                                <span class="required" aria-required="true"> * </span>
                            </label>
                            <div class="">
                                <div class="mt-radio-list" data-error-container="#form_2_membership_error">
                                    <label class="mt-radio">
                                        <input type="radio" name="active"
                                               @if($webinar_resource->active==1) checked="checked" @endif value="1">
                                        {{ Lang::get('main.active') }}
                                        <span></span>
                                    </label>
                                    <label class="mt-radio">
                                        <input @if($webinar_resource->active==0) checked="checked"
                                               @endif type="radio" name="active" value="0"> {{ Lang::get('main.not_active') }}
                                        <span></span>
                                    </label>

                                </div>
                                <div id="form_2_membership_error"></div>
                            </div>
                        </div>

                        <div class="form-group  col-md-12">

                            <label class="control-label ">{{ Lang::get('main.file') }}

                            </label>
                            <div class="">
                                <a href="{{assetURL($webinar_resource->file) }}">{{$webinar_resource->file }} </a>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label class="control-label">{{ Lang::get('main.replace') }} {{ Lang::get('main.file') }}
                                <span class="required">  </span>
                            </label>
                            <div class="">
                                <input name="file" id="poster" type="file" class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class=" col-md-12">

                                <div class="progress">
                                    <div class="bar"></div>
                                    <div class="percent">0%</div>
                                </div>
                            </div>

                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label ">{{ Lang::get('main.isfree') }}
                                <span class="required" aria-required="true"> * </span>
                            </label>
                            <div class="">
                                <div class="mt-radio-list" data-error-container="#form_2_membership_error">
                                    <label class="mt-radio">
                                        <input type="radio" @if($webinar_resource->isfree=='yes') checked="checked"
                                               @endif name="isfree" value="yes"> yes
                                        <span></span>
                                    </label>
                                    <label class="mt-radio">
                                        <input type="radio" name="isfree"
                                               @if($webinar_resource->isfree=='no') checked="checked"
                                               @endif value="no"> no
                                        <span></span>
                                    </label>
                                </div>
                                <div id="form_2_membership_error"></div>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label class="control-label">{{ Lang::get('main.sort') }}
                                <span class="required"> * </span>
                            </label>
                            <div class="">
                                <input name="sort" type="text" value="{{$webinar_resource->sort}}"
                                       class="form-control"/>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button id="submit-form" type="submit" class="btn green">{{ Lang::get('main.submit') }}</button>
                                    <button type="button" class="btn grey-salsa btn-outline">{{ Lang::get('main.cancel') }}</button>
                                </div>
                            </div>
                        </div>
                        {{Form::close()}}
                    </div>
                </div>
            </div>


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



                <script type="text/javascript">

                    function validate(formData, jqForm, options) {
                        var form = jqForm[0];
                        if (form.sort.value.match(/[a-z]/i)) {
                            $("#error_name").html('<div class="alert alert-danger">{{ Lang::get('main.webinar_resources_error_sort') }}</div>');
                            return false;
                        }

                        if (!form.description.value) {
                            $("#fields_error").html('<div class="alert alert-danger">{{ Lang::get('main.webinar_resources_error_description') }}</div>');

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
                                $("#submit-form").attr('disabled', 'disabled');
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
                                // console.log(data);
                                var percentVal = 'Wait, Saving';
                                bar.width(percentVal)
                                percent.html(percentVal);
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

                                    var id = {!! json_encode($webinar_resource->id) !!};
                                    window.location.href = "{{ URL('/admin/webinar_resources/') }}/" + id + "/edit";
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
                            active: {
                                required: true,
                                number: true
                            },
                            description: {
                                required: true
                            },
                        }
                    });
                </script>

@endsection