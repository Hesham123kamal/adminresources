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
                <a href="{{ URL('/admin/our_products_courses') }}">{{ Lang::get('main.our_products_courses') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.our_products_courses') }}
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
                    <i class="icon-our_products_courses font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.our_products_courses') }}</span>
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/our_products_courses','id'=>'addour_products_coursesForm','files'=>true]) !!}
                <div class="form-body">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_error') }}
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_success') }}
                    </div>
                    <div class="form-group col-lg-9">
                        <label class="control-label" for="name">{{ Lang::get('main.name') }}  <span class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{ old('name') }}" id="name" name="name" data-required="1" placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                        <input type="checkbox" class="make-switch" name="active" value="1" checked data-size="small" data-on-color="success" data-on-text="{{ Lang::get('main.active') }}" data-off-color="default" data-off-text="{{ Lang::get('main.inActive') }}">
                    </div>
                    <div class="clearfix"></div>
                    {{--<script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
                    <script type="text/javascript">
                        tinymce.init({
                            relative_urls : false,
                            remove_script_host : false,
                            document_base_url : "{{ URL('') }}",
                            convert_urls : true,
                            selector: ".description",
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
                            ]
                        });
                    </script>--}}
                    <div class="form-group col-lg-12">
                        <label class=" control-label" for="description">{{ Lang::get('main.description') }}  <span class="required"> * </span></label>
                        <textarea class="form-control description" style="min-height: 300px;" id="description" name="description" data-required="1" placeholder="{{ Lang::get('main.enter').Lang::get('main.description') }}">{!! old('description') !!}</textarea>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="username">{{ Lang::get('main.url') }}  <span class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" @if(!PerUser('our_products_courses_url')) disabled="disabled" readonly="readonly" @endif class="form-control" id="url" value="{{ old('url') }}" name="url"  data-required="1" placeholder="{{ Lang::get('main.enter').Lang::get('main.url') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="sort">{{ Lang::get('main.sort') }}  <span class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="number" min="1" value="{{ old('sort') }}" class="form-control" id="sort" name="sort" data-required="1" placeholder="{{ Lang::get('main.enter').Lang::get('main.sort') }}">
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail" style="width: 200px; height: 150px; border: none">
                                <img src="{{ asset('img/our_products_courses/default_image.png') }}" alt="" /> </div>
                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div>
                            <div >
                                <span class="btn default btn-file">
                                    <span class="fileinput-new"> {{ Lang::get('main.select_image') }} </span>
                                    <span class="fileinput-exists"> {{ Lang::get('main.change') }} </span>
                                    <input type="file" name="image"> </span>
                                <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> {{ Lang::get('main.remove') }} </a>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix" style="height: 30px"></div>
                    <div class="text-center">
                        <button type="submit" class="btn green">{{ Lang::get('main.add') }}</button>
                    </div>
                </div>
                <div class="clearfix" style="height: 30px"></div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@section('scriptCode')
    <script>
        $(document).ready(function(){

        });
    </script>
@endsection