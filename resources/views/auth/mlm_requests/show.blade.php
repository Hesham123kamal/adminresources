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
                <a href="{{ URL('/admin/mlm_requests') }}">{{ Lang::get('main.mlm_requests') }}</a>
                <i class="fa fa-circle"></i>
                <span>{{ Lang::get('main.search') }}</span>
            </li>
        </ul>

    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.mlm_requests') }}
        <small>{{ Lang::get('main.search') }}</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
@endsection
@section('content')
    @if(PerUser('mlm_requests_send'))
        <div class="row">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-dark">
                        <i class="icon-users font-dark"></i>
                        <span class="caption-subject bold uppercase">{{ Lang::get('main.mlm_requests') }}</span>
                    </div>
                    <div class="tools"></div>
                </div>
                <div class="portlet-body">
                        <div class="input-group col-md-12 add-on">
                            <input class="form-control" placeholder="Email Address Or Phone Number" name="srch-term" id="search_box" type="text">
                            <div class="input-group-btn">
                                <button id="search_button" class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
                            </div>
                        </div>
                    <div class="col-md-12 ajax_result"></div>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        </div>
    @endif
@endsection
@section('scriptCode')
    <script>
        $(document).ready(function () {
          @if(PerUser('mlm_requests_send'))
            var token = "{{ csrf_token() }}";
            $(document).on('click','#search_button',function(){
                var query=$('#search_box').val();
                if(query!=''){
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/mlm_requests/process') }}",
                        data: {query: query, _token: token},
                        success: function (data) {
                            $(".ajax_result").html(data);
                        }
                    })
                }
            });

            $(document).on('click','.send_button',function(){
                $.ajax({
                    type: "POST",
                    url: "{{ URL('admin/mlm_requests/send') }}",
                    data: {id: $(this).data('id'),type:'R', _token: token},
                    success: function (data) {
                        console.log(data);
                        $("#response-data").html(data);
                    }
                })
            });

            $(document).on('click','.update_button',function(){
                $.ajax({
                    type: "POST",
                    url: "{{ URL('admin/mlm_requests/send') }}",
                    data: {id: $(this).data('id'),type:'U', _token: token},
                    success: function (data) {
                        $("#response-data").html(data);
                    }
                })
            });

            @endif
        });
    </script>
@endsection
