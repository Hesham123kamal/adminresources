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
                <a href="{{ URL('/admin/modules_trainings') }}">{{ Lang::get('main.modules_trainings') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.modules_trainings') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.modules_trainings') }}</span>
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/modules_trainings','id'=>'addmodules_trainingsForm','files'=>true]) !!}
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
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="questions_numbers">{{ Lang::get('main.questions_numbers') }}  <span class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{ old('questions_numbers') }}" id="questions_numbers" name="questions_numbers" data-required="1" placeholder="{{ Lang::get('main.enter').Lang::get('main.questions_numbers') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="question_time">{{ Lang::get('main.question_time') }} <span
                                    class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="" name="question_time" data-required="1"
                                   placeholder="{{ Lang::get('main.enter').Lang::get('main.question_time') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label for="module_id">{{ Lang::get('main.module') }}</label>
                        <select name="module_id" class="sel2 form-control" id="module_id">
                            <option value="">{{ Lang::get('main.select').Lang::get('main.module') }}</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->id }}">{{ $module->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="course_name">{{ Lang::get('main.course_name') }} <span
                                    class="required"> * </span></label>
                        <select id="course_name" class="form-control sel2" required name="course_id">
                            <option value=" ">{{ Lang::get('main.select').Lang::get('main.course') }}</option>
                            @foreach($courses as $course)
                                <option value="{{$course->id}}">{{$course->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="clearfix"></div>
                    <div class="clearfix"></div>
                    <div class="text-center">
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

        });
    </script>
@endsection