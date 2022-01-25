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
                <a href="{{ URL('/admin/old_urls') }}">{{ Lang::get('main.old_urls') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.old_urls') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.old_urls') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/old_urls/'.$url->id,'class'=>"form-horizontal"]) !!}
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
                    <div class="form-group col-lg-12">
                        <label for="table_name">{{ Lang::get('main.table_name') }}<span
                                    class="required"> * </span></label>
                        <select name="table_name" class="module_name sel2 form-control form-filter">
                            <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.table_name') }}</option>
                            <option @if($url->table_name=='articles') selected="selected" @endif value="articles">articles</option>
                            <option @if($url->table_name=='diplomas') selected="selected" @endif  value="diplomas">diplomas</option>
                            <option @if($url->table_name=='mba') selected="selected" @endif  value="mba">mba</option>
                            <option @if($url->table_name=='courses') selected="selected" @endif  value="courses">courses</option>
                            <option @if($url->table_name=='books') selected="selected" @endif  value="books">books</option>
                            <option @if($url->table_name=='webinar') selected="selected" @endif  value="webinar">webinar</option>
                            <option @if($url->table_name=='events') selected="selected" @endif  value="events">events</option>
                            <option @if($url->table_name=='successtories') selected="selected" @endif  value="successtories">successtories</option>
                            <option @if($url->table_name=='articles_category') selected="selected" @endif  value="articles_category">articles_category</option>
                            <option @if($url->table_name=='sup_categories') selected="selected" @endif  value="sup_categories">sup_categories</option>
                            <option @if($url->table_name=='our_products') selected="selected" @endif  value="our_products">our_products</option>
                            <option @if($url->table_name=='our_products_courses') selected="selected" @endif  value="our_products_courses">our_products_courses</option>
                            <option @if($url->table_name=='instractors') selected="selected" @endif  value="instractors">instractors</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="old_url">{{ Lang::get('main.old_url') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{$url->old_url}}" id="old_url" name="old_url" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.old_url') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="new_url">{{ Lang::get('main.new_url') }}<span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{$url->new_url}}" id="new_url" name="new_url" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.new_url') }}">
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
