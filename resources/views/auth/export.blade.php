<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.6
Version: 4.6
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
Renew Support: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8"/>
    <title>Export | Dashboard</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/global/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/global/plugins/simple-line-icons/simple-line-icons.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/global/plugins/morris/morris.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/fullcalendar/fullcalendar.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/global/plugins/jqvmap/jqvmap/jqvmap.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="{{asset('assets/global/css/components.min.css')}}" rel="stylesheet"
          id="style_components" type="text/css"/>
    <link href="{{ asset('assets/global/css/plugins.min.css') }}" rel="stylesheet" type="text/css"/>
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="{{ asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}"
          rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}"
          rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}"
          rel="stylesheet" type="text/css"/>

    <link href="{{ asset('assets/layouts/layout3/css/layout.min.css') }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset('assets/layouts/layout3/css/themes/default.min.css') }}" rel="stylesheet"
          type="text/css" id="style_color"/>
    <!-- END THEME LAYOUT STYLES -->
    <link rel="shortcut icon" href="{{ asset('fav.png') }}" type="image/x-icon"/>
</head>
<!-- END HEAD -->

<body class="page-container-bg-solid">
<div class="page-wrapper">
    <div class="page-wrapper-row">
        <div class="page-wrapper-top">
            <!-- BEGIN HEADER -->

            <!-- END HEADER -->
        </div>
    </div>
    <div class="page-wrapper-row full-height">
        <div class="page-wrapper-middle">
            <!-- BEGIN CONTAINER -->
            <div class="page-container">
                <!-- BEGIN CONTENT -->
                <div class="page-content-wrapper">
                    <!-- BEGIN CONTENT BODY -->
                    <!-- BEGIN PAGE HEAD-->
                    <div class="page-head">
                        <div class="container">
                            <!-- BEGIN PAGE TITLE -->
                            <div class="page-title">
                                <h1>Courses
                                    <small>Expoert</small>
                                </h1>
                            </div>
                            <!-- END PAGE TITLE -->
                            <!-- BEGIN PAGE TOOLBAR -->

                            <!-- END PAGE TOOLBAR -->
                        </div>
                    </div>
                    <!-- END PAGE HEAD-->
                    <!-- BEGIN PAGE CONTENT BODY -->
                    <div class="page-content">
                        <div class="container">
                            <div class="page-content-inner">
                                <div class="mt-content-body">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12">
                                            <div class="portlet light ">
                                                <div class="portlet-title">
                                                    <div class="caption caption-md">
                                                        <i class="icon-bar-chart font-dark hide"></i>
                                                        {!! Form::open() !!}
                                                        <input type="hidden" name="table" value="workshop">
                                                        <span class="caption-subject font-green-steel uppercase bold">
                                                            Export all workshop report
                                                            <button
                                                                    type="submit"
                                                                    class="btn btn-primary">Export</button></span>
                                                        {!! Form::close() !!}
                                                        <span class="caption-helper hide">weekly stats...</span>
                                                    </div>
                                                </div>


                                                <div class="portlet-title">
                                                    <div class="caption caption-md">
                                                        <i class="icon-bar-chart font-dark hide"></i>
                                                        {!! Form::open() !!}
                                                        <input type="hidden" name="table" value="webinar">
                                                        <span class="caption-subject font-green-steel uppercase bold">
                                                            Export all webinars report
                                                            <button type="submit" class="btn btn-primary">Export</button></span>
                                                        {!! Form::close() !!}
                                                        <span class="caption-helper hide">weekly stats...</span>
                                                    </div>
                                                </div>

                                                <div class="portlet-title">
                                                    <div class="caption caption-md">
                                                        <i class="icon-bar-chart font-dark hide"></i>
                                                        {!! Form::open() !!}
                                                        <input type="hidden" name="table" value="books">
                                                        <span class="caption-subject font-green-steel uppercase bold">
                                                            Export all books report
                                                            <button
                                                                    type="submit"
                                                                    class="btn btn-primary">Export</button></span>
                                                        {!! Form::close() !!}
                                                        <span class="caption-helper hide">weekly stats...</span>
                                                    </div>
                                                </div>


                                                <div class="portlet-title">
                                                    <div class="caption caption-md">
                                                        <i class="icon-bar-chart font-dark hide"></i>
                                                        {!! Form::open() !!}
                                                        <input type="hidden" name="table" value="successtories">
                                                        <span class="caption-subject font-green-steel uppercase bold">
                                                            Export all successstories report
                                                            <button
                                                                    type="submit"
                                                                    class="btn btn-primary">Export</button></span>
                                                        {!! Form::close() !!}
                                                        <span class="caption-helper hide">weekly stats...</span>
                                                    </div>
                                                </div>


                                                <div class="portlet-title">
                                                    <div class="caption caption-md">
                                                        <i class="icon-bar-chart font-dark hide"></i>
                                                        {!! Form::open() !!}
                                                        <input type="hidden" name="table" value="courses">
                                                        <span class="caption-subject font-green-steel uppercase bold">
                                                            Export all courses report
                                                            <button
                                                                    type="submit"
                                                                    class="btn btn-primary">Export</button></span>
                                                        {!! Form::close() !!}
                                                        <span class="caption-helper hide">weekly stats...</span>
                                                    </div>
                                                </div>
                                                <div class="portlet-title">
                                                    <div class="caption caption-md">
                                                        <i class="icon-bar-chart font-dark hide"></i>
                                                        {!! Form::open(['url'=>'exportCoursesPercentage','method'=>'GET']) !!}
                                                        <span class="caption-subject font-green-steel uppercase bold">
                                                            Export all courses Users persentage report
                                                            <button type="submit" class="btn btn-primary">Export</button></span>
                                                        {!! Form::close() !!}
                                                        <span class="caption-helper hide">weekly stats...</span>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <h3>Export courses views report max <span
                                                                class="caption-subject font-green-steel uppercase bold"> 3 months!</span>
                                                    </h3>
                                                    {!! Form::open(array('url' => 'search/export','method' => 'POST')) !!}
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="col-md-6">
                                                                <div class="input-group date fromToDate margin-bottom-5"
                                                                     data-date-format="yyyy-mm-dd">
                                                                    <input type="text"
                                                                           class="form-control form-filter input-sm"
                                                                           readonly
                                                                           name="date_from" placeholder="{{ Lang::get('main.from') }}">
                                                                    <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="input-group date fromToDate"
                                                                     data-date-format="yyyy-mm-dd">
                                                                    <input type="text"
                                                                           class="form-control form-filter input-sm"
                                                                           readonly
                                                                           name="date_to" placeholder="{{ Lang::get('main.to') }}">
                                                                    <span class="input-group-btn">
                                            <button class="btn btn-sm default" type="button">
                                                <i class="fa fa-calendar"></i>
                                            </button>
                                        </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <button class="btn btn-md green btn-block  filter-submit margin-bottom">
                                                                Export
                                                            </button>

                                                        </div>
                                                    </div>
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- END PAGE CONTENT INNER -->
                        </div>
                    </div>
                    <!-- END PAGE CONTENT BODY -->
                    <!-- END CONTENT BODY -->
                </div>
                <!-- END CONTENT -->
                <!-- BEGIN QUICK SIDEBAR -->
                <!-- END QUICK SIDEBAR -->
            </div>
            <!-- END CONTAINER -->
        </div>
    </div>
    <div class="page-wrapper-row">
        <div class="page-wrapper-bottom">
        </div>
    </div>
</div>

<script src="{{ asset('assets/global/plugins/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>


<script src="{{ asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}"
        type="text/javascript"></script>

<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('assets/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/jquery-validation/js/additional-methods.min.js') }}"
        type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"
        type="text/javascript"></script>

<!-- END THEME LAYOUT SCRIPTS -->

<script>
    $(document).ready(function () {
        $('.fromToDate').datepicker({
            rtl: App.isRTL(),
            autoclose: true
        });
    });
</script>
</body>

</html>