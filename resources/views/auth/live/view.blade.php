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
                <span>{{ Lang::get('main.live') }}</span>
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
    <h1 class="page-title"> {{ Lang::get('main.live') }}
        <small>{{ Lang::get('main.view') }}</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
@endsection
@section('content')

    @if(PerUser('live_add'))
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group pull-right">
                    <a href="{{ URL('admin/live/create') }}" id="sample_editable_1_new" class="btn green">
                        {{ Lang::get('main.add_new') }}
                        <i class="fa fa-plus"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif
    @if(PerUser('live_view'))
        <div class="row">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-dark">
                        <i class="icon-users font-dark"></i>
                        <span class="caption-subject bold uppercase">{{ Lang::get('main.live') }}</span>
                    </div>
                    <div class="tools"></div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover dt-responsive" width="100%"
                           id="datatable_ajax">
                        <thead>
                        <tr role="row" class="heading">
                            <th width="8%"> #</th>
                            <th width="20%">{{ Lang::get('main.name') }}</th>
{{--                            <th width="20%">{{ Lang::get('main.parent_id') }}</th>--}}
{{--                            <th width="10%">{{ Lang::get('main.instructors') }}</th>--}}
                            <th width="10%">{{ Lang::get('main.location') }}</th>
                            <th width="10%">{{ Lang::get('main.url') }}</th>
{{--                            <th width="10%">{{ Lang::get('main.type') }}</th>--}}
                            <th width="10%">{{ Lang::get('main.created_time') }}</th>
                            <th width="10%">{{ Lang::get('main.published') }}</th>
                            <th width="8%"></th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <input type="number" min="1" class="form-control form-filter input-sm" name="id">
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="name">
                                <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
                            </td>
                            {{--<td>--}}

                                {{--<select name="parent_id" class="module_name sel2 form-control form-filter" id="parent_id">--}}
                                    {{--<option value=" ">Select parent ID</option>--}}
                                    {{--@foreach($parent_lives as $parent_live)--}}
                                        {{--<option value="{{$parent_live->id}}">{{$parent_live->name}}</option>--}}
                                    {{--@endforeach--}}
                                {{--</select>--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--<select name="instructors" class="module_name sel2 form-control form-filter"--}}
                                        {{--id="instructors">--}}
                                    {{--<option value=" ">Select instructor name</option>--}}
                                    {{--@foreach($instructors as $instructor)--}}
                                        {{--<option value="{{$instructor->id}}">{{$instructor->name}}</option>--}}
                                    {{--@endforeach--}}
                                {{--</select>--}}
                            {{--</td>--}}
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="location">
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="url">
                            </td>
                            {{--<td>--}}
                                {{--<select name="type" class="module_name sel2 form-control form-filter" id="type">--}}
                                    {{--<option value=" ">Select type</option>--}}
                                    {{--<option value="offline">Offline</option>--}}
                                    {{--<option value="online">Online</option>--}}
                                {{--</select>--}}
                            {{--</td>--}}
                            <td>
                                <div class="input-group date fromToDate margin-bottom-5" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control form-filter input-sm" readonly
                                           name="created_time_from" placeholder="{{ Lang::get('main.from') }}">
                                    <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                </div>
                                <div class="input-group date fromToDate" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control form-filter input-sm" readonly
                                           name="created_time_to" placeholder="{{ Lang::get('main.to') }}">
                                    <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                </div>
                            </td>
                            <td>

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
        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ Lang::get('main.confirmation') }}!!</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{ Lang::get('main.are_you_sure_you_want_to_delete') }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ Lang::get('main.cancel') }}</button>
                        <button type="button" class="btn btn-primary confirm_deletion"
                                data-dismiss="modal">{{ LAng::get('main.delete') }}</button>
                    </div>
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
            $('.module_name').select2();
            var token = "{{ csrf_token() }}";
            @if(PerUser('live_active'))
            $(document).on('change', '.changeStatues', function () {
                var statues = $(this).is(':checked');
                var id = $(this).attr('data-id');
                if (statues) {
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/live/activation') }}",
                        data: {"active": "yes", "id": id, _token: token},
                        success: function (msg) {
                            $("#errors").html(msg);
                        }
                    });
                } else {
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/live/activation') }}",
                        data: {"active": "no", "id": id, _token: token},
                        success: function (msg) {
                            $("#errors").html(msg);
                        }
                    });
                }
            });
            @endif
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
                        "url": "{{ URL('admin/live_search') }}", // ajax source
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
            var drug_id = null;
            $(document).on('click', '.deleteResource', function (event) {
                event.preventDefault();
                drug_id = $(this).attr("data-location");
                console.log(drug_id);

            });

            $(document).on('click', '.confirm_deletion', function (event) {
                event.preventDefault();
                console.log(grid);

                console.log(drug_id);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax(
                    {
                        url: "{{ URL('admin/live') }}/" + drug_id,
                        method: "delete", // replaced from put
                        dataType: "JSON",
                        data: {
                            "id": drug_id // method and token not needed in data
                        },
                        success: function (response) {
                            console.log(response); // see the reponse sent
                            console.log('deleted'); // see the reponse sent
                            $('.filter-submit').trigger('click');
                            // window.location.href = "/admin/courses";


                        },
                        error: function (xhr) {
                            console.log(xhr.responseText); // this line will save you tons of hours while debugging
                            // do something here because of error
                        }
                    });

            });
            @if(PerUser('live_delete'))
            $(document).on('click', '.delete_this', function (event) {

                var deleted_id = $(this).attr("data-id");
                event.preventDefault();
                BootstrapDialog.show({
                    title: '{{ Lang::get('main.delete').lang::get('main.live') }}',
                    message: '{{ Lang::get('main.delete_this').lang::get('main.live') }} ?',
                    buttons: [
                        {
                            label: '{{ Lang::get('main.yes') }}',
                            cssClass: 'btn-primary',
                            action: function (dialogItself) {
                                $.ajax({
                                    type: "DELETE",
                                    url: "{{ URL('admin/live') }}/" + deleted_id,
                                    data: {"id": deleted_id, _token: token},
                                    success: function (msg) {
                                        $("#errors").html(msg);
                                        $("#single-order-" + deleted_id).parent().parent().remove();
                                        dialogItself.close();
                                    }
                                });
                            }
                        },
                        {
                            label: '{{ Lang::get('main.no') }}',
                            action: function (dialogItself) {
                                dialogItself.close();
                            }
                        }]
                });
            });
            @endif
        });
    </script>
@endsection
