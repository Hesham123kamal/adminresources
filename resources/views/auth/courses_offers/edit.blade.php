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
                <a href="{{ URL('/admin/courses_offers') }}">{{ Lang::get('main.course_offer') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>

        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.course_offer') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.course_offer') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/courses_offers/'.$offer->id,'class'=>"form-horizontal"]) !!}
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
                <div class="form-group col-lg-12">
                    <label for="course">{{ Lang::get('main.course') }}<span
                                class="required"> * </span></label>
                    <select name="course" class="module_name form-control form-filter">
                        <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.course') }}</option>
                        @foreach($courses as $id=>$course)
                            <option @if($offer->course_id==$id)  selected="selected" @endif value="{{$id}}">{{$course}}</option>
                        @endforeach
                    </select>
                </div>

                {{--<div class="form-group col-lg-3 text-center" style="margin-top:25px;">--}}
                    {{--<input @if($offer->published=="yes") checked @endif type="checkbox" class="make-switch" name="published" value="yes" data-size="small"--}}
                           {{--data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"--}}
                           {{--data-off-text="{{ Lang::get('main.unpublished') }}">--}}
                {{--</div>--}}

                <div class="form-group col-lg-12">
                    <label class="control-label" for="egy_price">{{ Lang::get('main.egy_price') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$offer->egy_price}}" id="egy_price" name="egy_price" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.egy_price') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="egy_sale_price">{{ Lang::get('main.egy_sale_price') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$offer->egy_sale_price}}" id="egy_sale_price" name="egy_sale_price" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.egy_sale_price') }}">
                    </div>
                </div>
                <div class="form-group col-lg-12">
                    <label class="control-label" for="ksa_price">{{ Lang::get('main.ksa_price') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$offer->ksa_price}}" id="ksa_price" name="ksa_price" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.ksa_price') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="ksa_sale_price">{{ Lang::get('main.ksa_sale_price') }} <span
                                class="required"> * </span></label>
                    <div class="input-icon right">
                        <i class="fa"></i>
                        <input type="text" class="form-control" value="{{$offer->ksa_sale_price}}" id="ksa_sale_price" name="ksa_sale_price" data-required="1"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.ksa_sale_price') }}">
                    </div>
                </div>

                <div class="form-group col-lg-12">
                    <label class="control-label" for="expired_date">{{ Lang::get('main.expired_date') }} <span
                                class="required"> * </span></label>
                    <div class="input-group date margin-bottom-5" data-date-format="yyyy-mm-dd">
                        <input type="text" value="{{$offer->expired_date}}" class="form-control form-filter input-sm expired_date"
                               name="expired_date" placeholder="{{ Lang::get('main.expired_date') }}">
                        <span class="input-group-btn">
                                                <button class="btn btn-sm default" type="button">
                                                    <i class="fa fa-calendar"></i>
                                                </button>
                                            </span>
                    </div>
                </div>
               @include('auth/description',['posts' =>[$offer->description]])

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