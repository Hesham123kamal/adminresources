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
                <a href="{{ URL('/admin/books_categories') }}">{{ Lang::get('main.books_categories') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.books_categories') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.books_categories') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/books_categories','class'=>"form-horizontal"]) !!}
                <div class="form-body">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_error') }}
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_success') }}
                    </div>
                    <div id="messages"></div>
                    <div class="form-group col-lg-9">
                        <label for="category">{{ Lang::get('main.all_category') }}<span
                                    class="required"> * </span></label>
                        <select id="category" name="category" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select')}}{{Lang::get('main.all_category')}}</option>
                            @foreach($categories as $id=>$name)
                                <option value="{{$id}}">{{$name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                        <input type="checkbox" class="make-switch" name="published" value="yes" checked data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.unpublished') }}">
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="sub_category">{{ Lang::get('main.sub_category') }}<span
                                    class="required"> * </span></label>
                        <select id="sub_category" name="sub_category" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select')}}{{Lang::get('main.sub_category')}}</option>
                        </select>
                    </div>

                    <div class="form-group col-lg-12">
                        <label for="book">{{ Lang::get('main.book') }}<span
                                    class="required"> * </span></label>
                        <select name="book" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select')}} {{Lang::get('main.book')}}</option>
                            @foreach($books as $id=>$name)
                                <option value="{{$id}}">{{$name}}</option>
                            @endforeach
                        </select>
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
        $(document).ready(function(){
            $('#category').change(function(){
                $.ajax({
                    type: "POST",
                    url: "{{ URL('admin/books_categories/getSubCategoriesByCategoryId') }}",
                    data: {"category_id": $(this).val(),"_token": "{{ csrf_token() }}"},
                    success: function(options){
                        $('#sub_category').empty().append(options);
                    }
                });
            })

        })

    </script>
@endsection