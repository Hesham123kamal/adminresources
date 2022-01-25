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
                <a href="{{ URL('/admin/site_faq') }}">{{ Lang::get('main.site_faq') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.site_faq') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.site_faq') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/site_faq/'.$site_faq->id,'class'=>"form-horizontal",'files'=>true]) !!}
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

                <div id="messages"></div>
                <div class="form-group col-lg-9">
                    <label for="type">{{ Lang::get('main.type') }}</label>
                    <select name="type" class="module_name sel2 form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.type') }}</option>
                        @foreach($types as $type)
                            <option @if($site_faq->type==$type->id) selected @endif value="{{$type->id}}">{{$type->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                    <input @if($site_faq->published=="yes") checked @endif type="checkbox" class="make-switch" name="published" value="yes" data-size="small"
                           data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"
                           data-off-text="{{ Lang::get('main.unpublished') }}">
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="question">{{ Lang::get('main.question') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <textarea class="form-control" style="min-height: 300px;"
                                  id="question" name="question" data-required="1"
                                  placeholder="{{ Lang::get('main.enter').Lang::get('main.question') }}">{{$site_faq->question}}</textarea>

                    </div>
                </div>
                {{--<div class="form-group col-lg-12">--}}
                    {{--<label class="control-label" for="answer">{{ Lang::get('main.answer') }} <span--}}
                                {{--class="required"> * </span></label>--}}
                    {{--<div class="input-icon right">--}}
                        {{--<i class="fa"></i>--}}
                        {{--<textarea class="form-control answer" style="min-height: 300px;"--}}
                                  {{--id="answer" name="answer" data-required="1"--}}
                                  {{--placeholder="{{ Lang::get('main.enter').Lang::get('main.answer') }}">{{$site_faq->answer}}</textarea>--}}
                    {{--</div>--}}
                {{--</div>--}}

                @include('auth/description',['selectors'=>'.answer','labels'=>[Lang::get('main.answer')],'posts' =>[$site_faq->answer]])

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