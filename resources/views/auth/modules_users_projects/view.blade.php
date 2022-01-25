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
    <style>
        .has_another{
            text-decoration: line-through;
        }
        tbody tr td{
            word-break: break-word
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
                <span>{{ Lang::get('main.modules_users_projects') }}</span>
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
    <h1 class="page-title"> {{ Lang::get('main.modules_users_projects') }}
        <small>{{ Lang::get('main.view') }}</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->
@endsection
@section('content')
    @if(PerUser('modules_users_projects_view'))
        <div class="row">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-dark">
                        <i class="icon-users font-dark"></i>
                        <span class="caption-subject bold uppercase">{{ Lang::get('main.modules_users_projects') }}</span>
                    </div>
                    <div class="tools"></div>
                </div>
                <div class="portlet-body">
                    <div id="errorUploadCorrection"></div>
                    <div class="hidden" id="progressUploadCorrection">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0"
                                 aria-valuemax="100" style="width: 0%;">
                                0%
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="clearfix" style="height: 60px;"></div>
                    <table class="table table-striped table-bordered table-hover dt-responsive" width="100%"
                           id="datatable_ajax">
                        <thead>
                        <tr role="row" class="heading">
                            <th width="8%"> #</th>
                            <th width="20%">{{ Lang::get('main.module_name') }}</th>
                            <th width="20%">{{ Lang::get('main.project_name') }}</th>
                            {{--<th width="10%">{{ Lang::get('main.user_name') }}</th>--}}
                            <th width="10%">{{ Lang::get('main.user_email') }}</th>
                            {{--<th width="10%">{{ Lang::get('main.user_phone') }}</th>--}}
                            <th width="10%">{{ Lang::get('main.file') }}</th>
                            <th width="10%">{{ Lang::get('main.result') }}</th>
                            <th width="10%">{{ Lang::get('main.status') }}</th>
                            {{--<th width="10%">{{ Lang::get('main.status_date') }}</th>--}}
                            <th width="10%">{{ Lang::get('main.created_time') }}</th>
                            <th width="10%">{{ Lang::get('main.correction') }}</th>
                        </tr>
                        <tr role="row" class="filter">
                            <td>
                                <input type="number" min="1" class="form-control form-filter input-sm" name="id">
                            </td>
                            <td>
                                <select name="module_name" style="width: 100%" class="module_name form-control form-filter" id="module_name">
                                    <option value=" ">{{ Lang::get('main.select') }} {{ Lang::get('main.module') }}</option>
                                    @foreach($modules_names as $module_name)
                                        <option value="{{$module_name->name}}">{{$module_name->name}}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
                            </td>
                            <td>
                                <select name="project_name" style="width: 100%" class="module_name form-control form-filter" id="project_name">
                                    <option value=" ">{{ Lang::get('main.select') }} {{ Lang::get('main.project') }}</option>
                                    @foreach($modules_projects as $project_name)
                                        <option value="{{$project_name->title}}">{{$project_name->title}}</option>
                                    @endforeach
                                </select>
                            </td>
                           {{-- <td>
                                <input type="text" class="form-control form-filter input-sm" name="user_name">
                            </td>--}}
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="user_email">
                            </td>
                            {{--<td>
                                <input type="text" class="form-control form-filter input-sm" name="user_phone">
                            </td>--}}
                            <td>
                                <input type="text" class="form-control form-filter input-sm" name="file">
                            </td>
                            <td>
                                <input type="number" min="0" max="100" maxlength="3" class="form-control form-filter input-sm" name="result">
                            </td>
                            <td>
                                <select name="project_status" style="width: 100%" class="module_name form-control form-filter" id="project_status">
                                    <option value="">{{ Lang::get('main.select').Lang::get('main.status') }}</option>
                                    <option value="approved">{{ Lang::get('main.approved') }}</option>
                                    <option value="unapproved">{{ Lang::get('main.unapproved') }}</option>
                                </select>
                            </td>
                            {{--<td>
                                <div class="input-group date fromToDate margin-bottom-5" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control form-filter input-sm" readonly
                                           name="status_date_from" placeholder="{{ Lang::get('main.from') }}">
                                    <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                </div>
                                <div class="input-group date fromToDate" data-date-format="yyyy-mm-dd">
                                    <input type="text" class="form-control form-filter input-sm" readonly
                                           name="status_date_to" placeholder="{{ Lang::get('main.to') }}">
                                    <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                </div>
                            </td>--}}
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
            $(".menu-toggler.sidebar-toggler").trigger('click');
            $('.module_name').select2();
            var token = "{{ csrf_token() }}";
            $(document).on('change','.uploadCorrection',function(){
                $([document.documentElement, document.body]).animate({
                    scrollTop: 0
                }, 2000);
                if($("#progressUploadCorrection").hasClass('hidden')){
                    var file = this.files[0];
                    var imagefile = file.type;
                    console.log(imagefile);
                    var match= [
                        //word files
                        //'application/zip',
                        'application/msword',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
                        //excel
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
                        //pdf file
                        'application/pdf',
                        //powerpoint
                        'application/vnd.ms-powerpoint'
                    ];
                    if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2]) || (imagefile==match[3]) || (imagefile==match[4]) || (imagefile==match[5])|| (imagefile==match[6])|| (imagefile==match[7])|| (imagefile==match[8])))
                    {

                        $("#errorUploadCorrection").html('<div class="alert alert-danger">{{ Lang::get('main.error_upload_correction') }}</div>')
                        return false;
                    }else{
                        project_id=$(this).attr('data-id');
                        data=new FormData();
                        data.append('file',file);
                        data.append('project_id',project_id);
                        data.append('_token',token);
                        $.ajax({
                            type: 'POST',
                            url: '{{ URL('admin/modules_users_projects/upload_correction') }}',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data:data,
                            beforeSend: function() {
                                console.log('beforeSend');
                                $("#progressUploadCorrection .progress-bar").attr('aria-valuenow',0).css({"width":0+'%'}).html(0+'%')
                                $("#progressUploadCorrection").removeClass('hidden');

                            },
                            xhr: function() {
                                var xhr = new window.XMLHttpRequest();
                                xhr.upload.addEventListener("progress", function(evt) {
                                    if (evt.lengthComputable) {
                                        var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                                        console.log(percentComplete);
                                        $("#progressUploadCorrection .progress-bar").attr('aria-valuenow',percentComplete).css({"width":percentComplete+'%'}).html(percentComplete+'%')
                                        //Do something with upload progress here
                                    }
                                }, false);
                                return xhr;
                            },
                            success: function (data) {
                                console.log('success');
                                console.log(data);
                                $("#progressUploadCorrection").addClass('hidden');
                                $("#errorUploadCorrection").html(data.message);

                            }
                        });
                    }
                }else{
                    $("#errorUploadCorrection").html('<div class="alert alert-danger">{{ Lang::get('main.error_upload_correction_there_are_another_file') }}</div>')
                }

            });
            @if(PerUser('modules_users_projects_active'))
            $(document).on('change', '.changeStatues', function () {
                var statues = $(this).is(':checked');
                var id = $(this).attr('data-id');
                if (statues) {
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/modules_users_projects/activation') }}",
                        data: {"active": "yes", "id": id, _token: token},
                        success: function (msg) {
                            $("#errors").html(msg);
                        }
                    });
                } else {
                    $.ajax({
                        type: "POST",
                        url: "{{ URL('admin/modules_users_projects/activation') }}",
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
                    var $edit_project = $('.editable-project');
                    var $edit_result = $('.editable-result');
                    var $edit_project_status_id = $edit_project.attr("id");
                    var $edit_project_result_id = $edit_result.attr("id");
                    $.fn.editable.defaults.mode = 'popup';
                    $.fn.editable.defaults.ajaxOptions = {type: "PUT"};
                    $edit_project.editable({
                        source: [
                            {
                                'approved': 'Approved'
                            },
                            {
                                'unapproved': 'Unapproved'
                            }
                        ],
                        url: "{{ URL('admin/modules_users_projects') }}/" + $edit_project_status_id + '/editProjectStatus',
                        params: function (params) {
                            //originally params contain pk, name and value
                            params._token = token;
                            return params;
                        }
                    });

                    $edit_result.editable({
                        url: "{{ URL('admin/modules_users_projects') }}/" + $edit_project_result_id + '/editProjectResult',
                        params: function (params) {
                            //originally params contain pk, name and value
                            params._token = token;
                            return params;
                        }
                    });
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
                        "url": "{{ URL('admin/modules_users_projects_search') }}", // ajax source
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
                        url: "{{ URL('admin/modules_users_projects') }}/" + drug_id,
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
            @if(PerUser('modules_users_projects_delete'))
            $(document).on('click', '.delete_this', function (event) {

                var deleted_id = $(this).attr("data-id");
                event.preventDefault();
                BootstrapDialog.show({
                    title: '{{ Lang::get('main.delete').lang::get('main.modules_users_projects') }}',
                    message: '{{ Lang::get('main.delete_this').lang::get('main.modules_users_projects') }} ?',
                    buttons: [
                        {
                            label: '{{ Lang::get('main.yes') }}',
                            cssClass: 'btn-primary',
                            action: function (dialogItself) {
                                $.ajax({
                                    type: "DELETE",
                                    url: "{{ URL('admin/modules_users_projects') }}/" + deleted_id,
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
