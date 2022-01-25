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
                <span>{{ Lang::get('main.modules_questions') }}</span>
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
    <h1 class="page-title"> {{ Lang::get('main.modules_questions') }}
        <small>{{ Lang::get('main.view') }}</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
    @endsection
@section('content')
    @if(PerUser('modules_questions_add'))
    <div class="row">
        <div class="col-md-12">
            <div class="btn-group pull-right">
                <a href="{{ URL('admin/modules_questions/create') }}" id="sample_editable_1_new" class="btn green"> {{ Lang::get('main.add_new') }}
                    <i class="fa fa-plus"></i>
                </a>
            </div>
        </div>
    </div>
    @endif
    @if(PerUser('modules_questions_view'))
    <div class="row">
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption font-dark">
                    <i class="icon-modules_questions font-dark"></i>
                    <span class="caption-subject bold uppercase">{{ Lang::get('main.modules_questions') }}</span>
                </div>
                <div class="tools"> </div>
            </div>
            <div class="portlet-body">
                <div class="table-container">
                    <table class="table table-striped table-bordered table-hover table-checkable" id="datatable_ajax">
                        <thead>
                        <tr role="row" class="heading">
                            <th width="10%"> # </th>
                            <th width="30%">{{ Lang::get('main.module_name') }}</th>
                            <th width="20%">{{ Lang::get('main.question_numbers') }}</th>
                            <th width="40%">{{ Lang::get('main.question_count') }}</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                            </td>
                            <td>
                                <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
                                <select name="module" class="module_name sel2 form-control form-filter">
                                    <option value=" ">{{ Lang::get('main.select') }}{{ Lang::get('main.module') }}</option>
                                    @foreach($modules as $id=>$name)
                                        <option value="{{$id}}">{{$name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="question_numbers">
                            </td>
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="question_count">
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
                        <tbody> </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
@section('scriptCode')
    @if(PerUser('modules_questions_view'))
    <script>
        $(document).ready(function(){
            token= '{{ csrf_token() }}';

            @if(PerUser('modules_questions_view'))
            var grid = new Datatable();
            grid.setAjaxParam("_token", "{{ csrf_token() }}")
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
                onDataLoad: function(grid) {
                    // execute some code on ajax data load
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
                        "url": "{{ URL('admin/modules_questions2/getModulesQuestionsAJAX') }}", // ajax source
                    },
                    "order": [
                        [0, "desc"]
                    ],// set first column as a default sort by asc
                    dom: 'Blfrtip',
                    @if(getUserSystem('backend_lang')=='ar') "language": {"url": "{{ asset('assets/layouts/layout/datatables-arabic.json') }}"},@endif
                    buttons: [
                        { extend: 'print', className: 'btn dark btn-outline' },
                        { extend: 'pdf', className: 'btn green btn-outline' },
                        { extend: 'csv', className: 'btn purple btn-outline ' }
                    ],
                }
            });
            @endif
            @if(PerUser('modules_questions_delete'))
            $(document).on('click','.delete_this',function(event){
                event.preventDefault();
                console.log('erl');
                el=$(this);
                deleted_id=$(this).attr("data-id");

                BootstrapDialog.show({
                    title: '{{ Lang::get('main.delete').lang::get('main.modules_questions') }}',
                    message: '{{ Lang::get('main.delete_this').lang::get('main.modules_questions') }} ?',
                    buttons: [
                        {
                            label: '{{ Lang::get('main.yes') }}',
                            cssClass: 'btn-primary',
                            action: function(dialogItself){
                                $.ajax({
                                    type: "DELETE",
                                    url: "{{ URL('admin/modules_questions2') }}/"+deleted_id,
                                    data: {"id":deleted_id,_token:token},
                                    success: function (msg) {
                                        $("#errors").html(msg);
                                        el.closest('tr').remove();
                                        dialogItself.close();
                                    }
                                });
                            }
                        },
                        {
                            label: '{{ Lang::get('main.no') }}',
                            action: function(dialogItself){
                                dialogItself.close();
                            }
                        }]
                });
            });
            @endif
        });
    </script>
    @endif
@endsection
