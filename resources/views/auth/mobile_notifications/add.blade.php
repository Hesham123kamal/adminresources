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
                <a href="{{ URL('/admin/mobile_notifications') }}">{{ Lang::get('main.mobile_notifications') }}</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>{{ Lang::get('main.add') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.mobile_notifications') }}
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
                    <i class="icon-mobile_notifications font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.mobile_notifications') }}</span>
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                {!! Form::open(['method'=>'POST','url'=>'admin/mobile_notifications']) !!}
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
                        <label class="control-label" for="name">{{ Lang::get('main.title') }}  <span class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <input type="text" class="form-control" value="{{ old('title') }}" id="title" name="title" data-required="1" placeholder="{{ Lang::get('main.enter').Lang::get('main.title') }}">
                        </div>
                    </div>
                    <div class="form-group col-lg-12">
                        <label class="control-label" for="body">{{ Lang::get('main.body') }}  <span class="required"> * </span></label>
                        <div class="input-icon right">
                            <i class="fa"></i>
                            <textarea class="form-control" id="body" name="body" data-required="1" placeholder="{{ Lang::get('main.enter').Lang::get('main.body') }}">{{ old('body') }}</textarea>
                        </div>

                    </div>
                    <div class="form-group col-lg-10">
                        <label for="send_to">{{ Lang::get('main.send_to') }}</label>
                        <select id="send_to" class="form-control select2" multiple name="send_to[]">
                        </select>
                    </div>
                    <div class="form-group col-lg-2">
                        <div class="md-checkbox">
                            <input type="checkbox" id="to_all" name="to_all" class="md-check">
                            <label for="to_all">
                                <span></span>
                                <span class="check"></span>
                                <span class="box"></span> {{ Lang::get('main.send_to_all') }} </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="clearfix" style="height: 30px"></div>
                    <div class="text-center">
                        <button type="submit" class="btn green">{{ Lang::get('main.add') }}</button>
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
            $("#to_all").change(function() {
                if(this.checked) {
                    $(".select2").fadeOut();
                }
                else{
                    $(".select2").fadeIn();
                }
            });
            $(".select2").select2({
                ajax: {
                    url: "{{ URL('admin/mobile_notifications/users') }}",
                    dataType: 'json',
                    type: 'POST',
                    data: function (params) {
                        return {
                            q: params.term,
                            _token: "{{ csrf_token() }}",
                        };
                    },
                    processResults: function (data, params) {
                        // parse the results into the format expected by Select2
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data, except to indicate that infinite
                        // scrolling can be used
                        return {
                            results: data,
                        };
                    },
                 //  cache: true
                },
                placeholder: '{{ Lang::get('main.search_for_user') }}',
                // minimumInputLength: 1,
                // templateResult: formatRepo,
                // templateSelection: formatRepoSelection
            });

            // function formatRepo (repo) {
            //     if (repo.loading) {
            //         return repo.text;
            //     }
            // }
            //
            // function formatRepoSelection (repo) {
            //     return repo.text;
            // }
        });
    </script>
    @endsection