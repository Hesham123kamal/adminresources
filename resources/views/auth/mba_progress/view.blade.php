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
                <span>{{ Lang::get('main.mba_progress') }}</span>
            </li>
        </ul>
        <!--<div class="page-toolbar">
            <div id="dashboard-report-range" class="pull-right tooltips btn btn-sm" data-container="body" data-placement="bottom" data-original-title="Change dashboard date range">
                <i class="icon-calendar"></i>&nbsp;
                <span class="thin uppercase hidden-xs"></span>&nbsp;
                <i class="fa fa-angle-down"></i>
            </div>
        </div>-->
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.mba_progress') }}
        <small>{{ Lang::get('main.view') }}</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
@endsection
@section('content')

    @if(PerUser('mba_progress'))
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group pull-right">
                    <a href="{{ URL('admin/mba_progress/create') }}" id="sample_editable_1_new" class="btn green">
                        {{ Lang::get('main.add_new') }}
                        <i class="fa fa-plus"></i>
                    </a>
                    <a style="margin-left:5px;" href="{{ URL('admin/mba_emails') }}" id="sample_editable_1_new" class="btn green">
                        {{ Lang::get('main.emails') }}
                    </a>
                </div>
                <div class="btn-group pull-left">
                    <a href="{{ URL('admin/mba_progress_export') }}" id="sample_editable_1_new" class="btn btn-primary blue">
                        {{ Lang::get('main.export') }}
                    </a>
                </div>
            </div>
        </div>
    @endif
    @if(PerUser('mba_progress'))
        <div class="row">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-dark">
                        <i class="icon-users font-dark"></i>
                        <span class="caption-subject bold uppercase">{{ Lang::get('main.mba_progress') }}</span>
                    </div>
                    <div class="tools"></div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover dt-responsive" width="100%"
                           id="datatable_ajax">
                        <thead>
                        <tr role="row" class="heading">
                            <th >{{ Lang::get('main.email') }}</th>
                            <th >{{ Lang::get('main.module_name') }}</th>
                            <th >{{ Lang::get('main.project') }}</th>
                            <th >{{ Lang::get('main.exam') }}</th>
                            <th >{{ Lang::get('main.progress') }}</th>
                            <th ></th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="email">
                                <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
                            </td>
                            <td>
                                {{--<input type="text" class="form-control form-filter input-sm" name="mba_name">--}}
                                <select name="mba_id" class="module_name sel2 form-control form-filter">
                                    <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.mba') }}</option>
                                    @foreach($modules as $id=>$name)
                                        <option value="{{$id}}">{{$name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="project">
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="exam">
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="result">
                            </td>
                            <td>
                                <div class="margin-bottom-5">
                                    <button class="btn btn-sm green btn-outline filter-submit margin-bottom">
                                        <i class="fa fa-search"></i> {{ Lang::get('main.search') }}</button>
                                </div>
                                <button class="btn btn-sm red btn-outline filter-cancel">
                                    <i class="fa fa-times"></i> {{ Lang::get('main.reset') }}</button>
                            </td>

                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
        </div>
    @endif
@endsection
@section('scriptCode')

    <script>
        $(document).ready(function () {
            var token = "{{ csrf_token() }}";
            var grid = new Datatable();
            grid.setAjaxParam("_token", "{{ csrf_token() }}");
            // console.log(grid._token)
            grid.init({
                src: $("#datatable_ajax"),
                onSuccess: function (grid, response) {
                    // grid:        grid object
                    // response:    json object of server side ajax response
                    // execute some code after table records loaded
                },
                onError: function (grid) {
                    // execute some code on network or other general error
                },
                onDataLoad: function (grid) {
                },
                loadingMessage: '{{ Lang::get('main.loading') }}',
                dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options
                    // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                    // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                    // So when dropdowns used the scrollable div should be removed.
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, @if(PerUser('view_all_rows')) -1 @endif],
                        [10, 20, 50, 100, 150, @if(PerUser('view_all_rows')) "All" @endif] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "ajax": {
                        "url": "{{ URL('admin/mba_progress_search') }}", // ajax source
                    },
                    @if(getUserSystem('backend_lang')=='ar') "language": {"url": "{{ asset('assets/layouts/layout/datatables-arabic.json') }}"},@endif "columnDefs": [{
                        "targets": [0], // column or columns numbers
                        "orderable": true  // set orderable for selected columns

                    }],
                    "order": [
                        [0, "desc"]
                    ],// set first column as a default sort by asc
                    dom: 'Blfrtip',
                    buttons: [
                        // {extend: 'print', className: 'btn dark btn-outline'},
                        // {extend: 'pdf', className: 'btn green btn-outline'},
                        // {extend: 'csv', className: 'btn purple btn-outline'}
                    ],
                }
            });
        });
    </script>
@endsection
