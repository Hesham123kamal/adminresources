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
                <a href="{{ URL('/admin/subscription_prices') }}">{{ Lang::get('main.subscription_prices') }}</a>
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
    <h1 class="page-title"> {{ Lang::get('main.subscription_prices') }}
        <small>{{ Lang::get('main.edit') }}</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
@endsection
@section('content')
    {!! Form::open(['method'=>'PUT','url'=>'admin/subscription_prices']) !!}
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
    <div class="row">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-users font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.subscription_prices') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                <div class="form-group col-lg-6">
                    <label for="diplomas_prices">{{ Lang::get('main.diplomas_prices') }} <span
                                class="required"> * </span></label>
                        <input type="text" class="form-control"
                               value="{{$subscription_prices!=null?$subscription_prices->diplomas_prices:''}}" name="diplomas_prices"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.diplomas_prices') }}">
                </div>

                <div class="form-group col-lg-6">
                    <label for="lifetime_new">{{ Lang::get('main.lifetime_new') }} <span
                                class="required"> * </span></label>
                        <input type="text" class="form-control"
                               value="{{$subscription_prices!=null?$subscription_prices->lifetime_new:''}}" name="lifetime_new"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.lifetime_new') }}">
                </div>

                <div class="form-group col-lg-6">
                    <label for="lifetime_renew">{{ Lang::get('main.lifetime_renew') }} <span
                                class="required"> * </span></label>
                        <input type="text" class="form-control"
                               value="{{$subscription_prices!=null?$subscription_prices->lifetime_renew:''}}" name="lifetime_renew"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.lifetime_renew') }}">
                </div>

                <div class="form-group col-lg-6">
                    <label for="mba">{{ Lang::get('main.mba') }} <span
                                class="required"> * </span></label>
                        <input type="text" class="form-control"
                               value="{{$subscription_prices!=null?$subscription_prices->mba:''}}" name="mba"
                               placeholder="{{ Lang::get('main.enter').Lang::get('main.mba') }}">
                </div>

                <div class="clearfix"></div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-users font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.diplomas_prices') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                @foreach($diplomas as $diploma)
                <div class="form-group col-lg-6">
                    <label for="diploma_{{$diploma->id}}" style="direction: rtl; text-align: left">{{ $diploma->name }} <span
                                class="required"> * </span></label>
                    <input type="text" class="form-control"
                           value="{{$diploma->ksa_price}}" name="diploma_price[{{$diploma->id}}]" id="diploma_{{$diploma->id}}"
                           placeholder="{{ Lang::get('main.enter').Lang::get('main.price') }}">
                </div>
                @endforeach
                <div class="clearfix"></div>

            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn green">{{ Lang::get('main.save') }}</button>
    </div>
    <div class="clearfix"></div>
    {!! Form::close() !!}
@endsection
