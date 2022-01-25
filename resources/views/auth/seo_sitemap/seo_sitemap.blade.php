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
                <a href="{{ URL('/admin/app_settings') }}">{{ Lang::get('main.seo_sitemap') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
            <li>
                <span></span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.seo_sitemap') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.seo_sitemap') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/seo_sitemap','id'=>'seo_sitemapForm','files'=>true]) !!}
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    {{ Lang::get('main.form_validation_error') }}
                </div>
                <div class="alert alert-success display-hide">
                    <button class="close" data-close="alert"></button>
                    {{ Lang::get('main.form_validation_success') }}
                </div>
                <div class="form-body row" style="margin-left: 10px;">
                    @foreach($seo_sitemap as $key => $sitemap)
                        <div class="form-group col-lg-6 grid-margin">
                            <label for="priority">{{ $sitemap->name }} - {{ $sitemap->page_name }} <span class="required"> * </span></label>
                            <input type="number" min="0.1" max="1" step="0.1" class="form-control" id="priority" value="{{$sitemap->priority}}" name="priority[{{$sitemap->name}}]" placeholder="Enter Priority" required>
                        </div>
                    @endforeach
                </div>

                <div class="clearfix"></div>
                <div class="text-center">
                    <button type="submit" class="btn green" style="padding: 10px 40px;">{{ Lang::get('main.save') }}</button>
                    <button type="button" id="generateSiteMap" class="btn blue" style="padding: 10px 40px;"><i class="fa fa-paper-plane"></i> {{ Lang::get('main.generate_site_map') }}</button>
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
            $(document).on('click','#generateSiteMap',function(e){
                el=$(this);
                e.preventDefault();
                if(el.attr('disabled')==undefined){
                    el.attr('disabled','disabled')

                    var dataForm = new FormData($('#seo_sitemapForm')[0]);
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: 'POST',
                        url: '{{ URL("admin/seo_sitemap") }}',
                        data: dataForm,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            $.ajax({
                                type: 'GET',
                                url: 'https://www.e3melbusiness.com/?page=seo&action=generateSiteMap',
                                success: function() {
                                    $('.alert-success').html('<button class="close" data-close="alert"></button> Success Generated');
                                    $('.alert-success').show(300);
                                    el.removeAttr('disabled');
                                    $('html, body').animate({ scrollTop: 0 }, "slow");
                                },

                            });
                        },
                        error: function(response) {
                            $('#currentErros').remove();
                            html = '<ul id="currentErros">';
                            $.each(response.responseJSON, function(key, value) {
                                html += '<li>' + value + '</li>';
                            });
                            html += '</ul>';
                            $('.alert-danger').append(html);
                            $('.alert-danger').show(300);
                            $('html, body').animate({ scrollTop: 0 }, "slow");
                        }
                    });

                }


            });
            $(document).on('submit', '#seo_sitemapForm', function (e) {
                e.preventDefault();
                var dataForm = new FormData(this);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: '{{ URL("admin/seo_sitemap") }}',
                    data: dataForm,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('.alert-danger').hide();
                        $('.alert-success').show(300);
                        $('html, body').animate({ scrollTop: 0 }, "slow");
                    },
                    error: function(response) {
                        $('#currentErros').remove();
                        html = '<ul id="currentErros">';
                        $.each(response.responseJSON, function(key, value) {
                            html += '<li>' + value + '</li>';
                        });
                        html += '</ul>';
                        $('.alert-danger').append(html);
                        $('.alert-danger').show(300);
                        $('html, body').animate({ scrollTop: 0 }, "slow");
                    }
                });
            });
        });
    </script>
@endsection
