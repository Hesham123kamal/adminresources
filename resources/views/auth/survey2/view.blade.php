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
                <span>{{ Lang::get('main.survey2') }}</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> {{ Lang::get('main.survey2') }}
        <small>{{ Lang::get('main.view') }}</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
@endsection
@section('content')
<style>
    td {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
</style>
    @if(PerUser('survey2_view'))
        <div class="row">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-dark">
                        <i class="icon-users font-dark"></i>
                        <span class="caption-subject bold uppercase">{{ Lang::get('main.survey2') }}</span>
                    </div>
                    <div class="tools"></div>
                </div>
                <div class="portlet-body">
                    <table style=" table-layout: fixed;width: 100%;" class="table table-striped table-bordered table-hover table-checkable" width="100%"
                           id="datatable_ajax">
                        <thead>
                        <tr role="row" class="heading">
                            <th width="5%"> #</th>
                            <th>{{ Lang::get('main.client_id') }}</th>
                            <th>{{ Lang::get('main.max_table') }}</th>
                            <th>{{ Lang::get('main.score') }}</th>
                            <th>{{ Lang::get('main.max_table') }}</th>
                            <th>{{ Lang::get('main.score') }}</th>
                            <th>{{ Lang::get('main.max_table') }}</th>
                            <th>{{ Lang::get('main.score') }}</th>
                            <th width="20%">{{ Lang::get('main.created_time') }}</th>
                            <th></th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
                                <input type="number" min="1" class="form-control form-filter input-sm" name="id">
                            </td>
                            <td>
                                <input type="number" min="1" class="form-control form-filter input-sm" name="client_id">
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="max_table1">
                            </td>
                            <td>
                                <input type="number" min="0" class="form-control form-filter input-sm" name="score1">
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="max_table2">
                            </td>
                            <td>
                                <input type="number" min="0" class="form-control form-filter input-sm" name="score2">
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="max_table3">
                            </td>
                            <td>
                                <input type="number" min="0" class="form-control form-filter input-sm" name="score3">
                            </td>
                            <td>
                                <div class="input-group date fromToDate margin-bottom-5" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control form-filter input-sm"
                                           name="created_time_from" placeholder="{{ Lang::get('main.from') }}">
                                    <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                </div>
                                <div class="input-group date fromToDate" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control form-filter input-sm"
                                           name="created_time_to" placeholder="{{ Lang::get('main.to') }}">
                                    <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                </div>
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
            $('.fromToDate').datepicker({
                rtl: App.isRTL(),
                autoclose: true
            });
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
                        "url": "{{ URL('admin/survey2_search') }}", // ajax source
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
                        {extend: 'print', className: 'btn dark btn-outline'},
                        {extend: 'pdf', className: 'btn green btn-outline'},
                        {extend: 'csv', className: 'btn purple btn-outline '}
                    ],
                }
            });
        });
    </script>
@endsection