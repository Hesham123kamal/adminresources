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
                <a href="{{ URL('/admin/initiative_sections') }}">{{ Lang::get('main.initiative_sections') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
            <li>
                <span>{{ $initiative_section->title }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.initiative_sections') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.initiative_sections') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/initiative_sections/'.$initiative_section->id,'class'=>"form-horizontal",'files'=>true]) !!}
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
                <div class="form-group col-lg-9">
                    <label class="control-label" for="title">{{ Lang::get('main.title') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$initiative_section->title}}" id="title" name="title" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.title') }}">
                    </div>
                </div>
                <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                    <input @if($initiative_section->published=="yes") checked @endif type="checkbox" class="make-switch" name="published" data-size="small"
                           data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"
                           data-off-text="{{ Lang::get('main.unpublished') }}">
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="meta_title">{{ Lang::get('main.meta_title') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$initiative_section->meta_title}}" id="meta_title" name="meta_title"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.meta_title') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="meta_description">{{ Lang::get('main.meta_description') }}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <textarea type="text" class="form-control" id="meta_description" name="meta_description" style="height: 200px;"
                                  placeholder="{{ Lang::get('main.enter').Lang::get('main.meta_description') }}">{{$initiative_section->meta_description}}</textarea>
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label">{{Lang::get('main.image')}}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <img width="20%" src="{{assetURL($initiative_section->image) }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="image">{{Lang::get('main.replace')}} {{Lang::get('main.image')}}</label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="file" class="form-control" value="" accept="image/*"
                               name="image" placeholder="{{ Lang::get('main.enter').Lang::get('main.image') }}">
                    </div>
                </div>

                @include('auth/description',['posts' =>[$initiative_section->description]])

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
