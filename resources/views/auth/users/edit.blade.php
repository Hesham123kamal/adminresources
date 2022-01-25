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
                <a href="{{ URL('/admin/users') }}">{{ Lang::get('main.users') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.edit') }}</span>
            </li>
            <li>
                <span>{{ $post->name }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.users') }}
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
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.users') }}</span>
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'PUT','url'=>'admin/users/'.$post->id,'id'=>'addUsersEditForm','class'=>"form-horizontal"]) !!}
                <div class="form-body">
                    <div class="alert alert-danger display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_error') }}
                    </div>
                    <div class="alert alert-success display-hide">
                        <button class="close" data-close="alert"></button>
                        {{ Lang::get('main.form_validation_success') }}
                    </div>
                    <div class="form-group col-lg-12">
                        <label for="profile_id">{{ Lang::get('main.profiles') }}</label>
                        <select style="width: 100%;" id="profile_id" class="form-control sel2" name="profile_id">
                            @foreach($profiles as $profile)
                                <option @if($post->profile_id==$profile->id) selected="selected" @endif value="{{ $profile->id }}">{{ $profile->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-lg-9">
                        <label class="control-label" for="name">{{ Lang::get('main.name') }}  <span class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{ $post->name }}" id="name" name="name" data-required="1" placeholder="{{ Lang::get('main.enter').Lang::get('main.name') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-3 text-center" style="margin-top:25px;">
                        <input type="checkbox" class="make-switch" name="active" value="1" @if($post->active==1) checked @endif data-size="small" data-on-color="success" data-on-text="{{ Lang::get('main.active') }}" data-off-color="default" data-off-text="{{ Lang::get('main.inActive') }}">
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-lg-12">
                        <label class=" control-label" for="website">{{ Lang::get('main.email') }}  <span class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="email" class="form-control" id="email" value="{{ $post->email }}" name="email" data-required="1" placeholder="{{ Lang::get('main.enter').Lang::get('main.email') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="username">{{ Lang::get('main.username') }}  <span class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" id="username" value="{{ $post->username }}" name="username"  data-required="1" placeholder="{{ Lang::get('main.enter').Lang::get('main.username') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="password">{{ Lang::get('main.password') }}  <span class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="password" class="form-control" id="password" name="password" data-required="1" placeholder="{{ Lang::get('main.enter').Lang::get('main.password') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="confirm_password">{{ Lang::get('main.confirm_password') }}  <span class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" data-required="1" placeholder="{{ Lang::get('main.enter').Lang::get('main.confirm_password') }}">
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail" style="width: 200px; height: 150px; border: none">
                                <img src="{{ asset($post->img_dir.$post->img) }}" alt="" /> </div>
                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div>
                            <div >
                                <span class="btn default btn-file">
                                    <span class="fileinput-new"> {{ Lang::get('main.select_image') }} </span>
                                    <span class="fileinput-exists"> {{ Lang::get('main.change') }} </span>
                                    <input type="file" name="image"> </span>
                                <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> {{ Lang::get('main.remove') }} </a>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix" style="height: 30px"></div>
                    <div class="text-center">
                        <button type="submit" class="btn green">{{ Lang::get('main.save') }}</button>
                    </div>
                </div>
                <div class="clearfix" style="height: 30px"></div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection
@section('scriptCode')
    <script>
        $(document).ready(function(){
            $(document).on('change','#all_projects',function(){
                if($(this).is(':checked')){
                    $("#projects_ids").attr('disabled','disabled');
                }else{
                    $("#projects_ids").removeAttr('disabled')
                }
            });
        });
    </script>
@endsection