
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
                <a href="{{ URL('/admin/our_partner') }}">{{ Lang::get('main.our_partners') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.our_partners') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.our_partners') }}</span>
                </div>
                <div class="tools"></div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/our_partner','class'=>"form-horizontal",'files'=>true]) !!}
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
                        <label class="control-label" for="flag_id">{{ Lang::get('main.flag') }} <span
                                    class="required"> * </span></label>
                        <select id="flag_id" class="form-control select2" required name="flag_id" style="width: 100%;">
                            <option value=" ">{{ Lang::get('main.select') }} {{ Lang::get('main.flag') }}</option>
                            @foreach($flags as $flag)
                                <option @if(old('flag_id')==$flag->id) selected @endif value="{{ $flag->id}}">{{ $flag->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-9">
                        <label class="control-label" for="title">{{ Lang::get('main.title') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{old('title')}}" id="title" name="title" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.title') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                        <input type="checkbox" class="make-switch" name="published" value="yes" checked data-size="small"
                               data-on-color="success" data-on-text="{{ Lang::get('main.published') }}" data-off-color="default"
                               data-off-text="{{ Lang::get('main.unpublished') }}">
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="name">{{ Lang::get('main.name') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{old('name')}}" id="name" name="name"
                                   data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="country">{{ Lang::get('main.country') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{old('country')}}" id="country" name="country"
                                   data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.country') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="phone">{{ Lang::get('main.phone') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{old('phone')}}" id="phone" name="phone"
                                   data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.phone') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="responsible_name">{{ Lang::get('main.responsible_name') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{old('responsible_name')}}" id="responsible_name" name="responsible_name"
                                   data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.responsible_name') }}">
                        </div>
                    </div>

                    <div class="form-group col-lg-12">
                        <label class="control-label" for="support_phone">{{ Lang::get('main.support_phone') }} </label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{old('support_phone')}}" id="support_phone" name="support_phone"
                                   data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.support_phone') }}">
                        </div>
                    </div>


                    <div class="form-group col-lg-12">
                        <label class="control-label" for="image">{{ Lang::get('main.image') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="file" class="form-control" value="" id="image" accept="image/*"
                                   name="image" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.image') }}">
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
@section('scriptCode')

@endsection