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
                <a href="{{ URL('/admin/instructors') }}">{{ Lang::get('main.instructors') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
            <li>
                <span>{{ $instructor->name }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.instructors') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.instructors') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/instructors/'.$instructor->id,'id'=>'addInstructorsForm','files'=>true]) !!}
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
                <div class="form-group col-lg-6">
                    <label class="control-label" for="title">{{ Lang::get('main.title') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$instructor->title}} " id="title" name="title" placeholder="{{ Lang::get('main.enter').Lang::get('main.title') }}">
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label class="control-label" for="en_title">{{ Lang::get('main.en_title') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$instructor->en_title}} " id="en_title" name="en_title" placeholder="{{ Lang::get('main.enter').Lang::get('main.en_title') }}">
                    </div>
                </div>
                <div class="form-group col-lg-5">
                    <label class="control-label" for="name">{{ Lang::get('main.name') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$instructor->name}} " id="name" name="name" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                    </div>
                </div>
                <div class="form-group col-lg-5">
                    <label class="control-label" for="name">{{ Lang::get('main.en_name') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$instructor->en_name}} " id="en_name" name="en_name" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.en_name') }}">
                    </div>
                </div>
                <div class="form-group col-lg-2 text-center" style="margin-top:25px;">
                    <input type="checkbox" class="make-switch" name="published" value="yes"
                           @if($instructor->published=="yes") checked @endif data-size="small" data-on-color="success"
                           data-on-text="{{ Lang::get('main.published') }}" data-off-color="default" data-off-text="{{ Lang::get('main.unpublished') }}">
                </div>

                <script type="text/javascript" src="<?php echo URL('js/tinymce/tinymce.min.js')?>"></script>
                <script type="text/javascript">
                    tinymce.init({
                        relative_urls : false,
                        remove_script_host : false,
                        document_base_url : "{{ url('') }}",
                        convert_urls : true,
                        selector: ".editor",
                        theme: "modern",
                        plugins: [
                            "advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker",
                            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                            "table contextmenu directionality emoticons template textcolor paste textcolor colorpicker textpattern "/*fullpage*/
                        ],

                        toolbar1: "newdocument fullpage | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontselect fontsizeselect",
                        toolbar2: "cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media code | insertdatetime preview | forecolor backcolor",
                        toolbar3: "table | hr removeformat | subscript superscript | charmap emoticons | print fullscreen | ltr rtl | spellchecker | visualchars visualblocks nonbreaking template pagebreak restoredraft",
                        image_advtab: true,
                        menubar: false,
                        toolbar_items_size: 'small',
                        style_formats: [
                            {title: 'Bold text', inline: 'b'},
                            {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                            {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                            {title: 'Example 1', inline: 'span', classes: 'example1'},
                            {title: 'Example 2', inline: 'span', classes: 'example2'},
                            {title: 'Table styles'},
                            {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
                        ],

                        templates: [
                            {title: 'Test template 1', content: 'Test 1'},
                            {title: 'Test template 2', content: 'Test 2'}
                        ],
                        autosave_ask_before_unload:false
                    });
                </script>
                <div class="col-lg-12 form-group">
                    <label class=" control-label" for="description">{{ Lang::get('main.description') }}</label>
                    <textarea class="form-control editor" name="description" id="description">{!! $instructor->description !!}</textarea>
                </div>
                <div class="col-lg-12 form-group">
                    <label class=" control-label" for="en_description">{{ Lang::get('main.en_description') }}</label>
                    <textarea class="form-control editor" name="en_description" id="en_description">{!! $instructor->en_description !!}</textarea>
                </div>
                {{--@include('auth/description',['posts' =>[$instructor->description]])

                @include('auth/description',['posts' =>[$instructor->description_en],'selectors'=>'.description,.description_en','labels'=>[Lang::get('main.description_en')]])
--}}

                <div class="form-group col-lg-12">
                    <label class="control-label">{{ Lang::get('main.pic') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <img style="width: 20%" src="{{assetURL($instructor->pic) }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="pic">{{ Lang::get('main.replace') }} {{ Lang::get('main.pic') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="file" class="form-control" value="" id="pic" accept="image/*"
                               name="pic" placeholder="{{ Lang::get('main.enter').Lang::get('main.pic') }}">
                    </div>
                </div>


                <div class="form-group col-lg-12">
                    <label class="control-label" for="linkedin">{{ Lang::get('main.linkedin') }} <span
                                class="required"> * </span> </label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$instructor->linkedin}}" id="linkedin"
                               name="linkedin" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.linkedin') }} Account">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="facebook">{{ Lang::get('main.facebook') }} <span
                                class="required"> * </span> </label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$instructor->facebook}}" id="facebook"
                               name="facebook" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.facebook') }} Account">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="url">{{ Lang::get('main.url') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$instructor->url}}" id="url" name="url"
                               data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.url') }}">
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