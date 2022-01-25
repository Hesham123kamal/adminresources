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
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"
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
                                <h1>Mba
                                    <small>Email Templates</small>
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
                                                <div class="portlet-title">Mba Email Templates</div>

                                                    <div class="portlet-body">
                                                       <div class="form-group">
                                                           <input type="text" class="form-control " id="customerEmail" placeholder="Email Address">
                                                       </div>
                                                        <div id="errors"></div>
                                                        <div class="col-lg-3 pull-right">
                                                            <button class="btn btn-success" id="sendWeeklyReport"> تقرير التقدم الاسبوعي في MBA</button>
                                                        </div>
                                                        <div class="col-lg-3 pull-right">
                                                            <button class="btn btn-success" id="sendAfter3DaysReport">ايميل كل 3 أيام لحث الطالب علي الحضور</button>
                                                        </div>
                                                        <div class="col-lg-3 pull-right">
                                                            <button class="btn btn-success"  id="sendAfter14DaysNotLoginReport">ايميل اذا مر اسبوعين ولم يدخل العميل علي الموقع</button>
                                                        </div>
                                                        <div class="col-lg-3 pull-right">
                                                            <button class="btn btn-success" id="sendAfter2WeeksReport">ايميل كل اسبوعين</button>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <div class="clearfix" style="height: 30px;"></div>
                                                        <div class="clearfix"></div>
                                                    </div>
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




<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>



<!-- END THEME LAYOUT SCRIPTS -->

<script>
    $(document).ready(function () {
        $(document).on('click','#sendWeeklyReport',function(e){
            e.preventDefault();
            el=$(this);
            email=$("#customerEmail").val();
            if(email){
                el.attr('disabled','disabled');
                $.ajax({
                    type: "GET",
                    url: "https://www.e3melbusiness.com/?page=EmailsReport&action=cronSendWeeklyReport",
                    data: {"email":email},
                    success: function (msg) {
                        $("#errors").html(msg);
                        el.removeAttr('disabled');
                    }
                });
            }

        });
        $(document).on('click','#sendAfter3DaysReport',function(e){
            e.preventDefault();
            el=$(this);
            email=$("#customerEmail").val();
            if(email){
                el.attr('disabled','disabled');
                $.ajax({
                    type: "GET",
                    url: "https://www.e3melbusiness.com/?page=EmailsReport&action=cronSendAfter3DaysReport",
                    data: {"email":email},
                    success: function (msg) {
                        $("#errors").html(msg);
                        el.removeAttr('disabled');
                    }
                });
            }

        });

        $(document).on('click','#sendAfter14DaysNotLoginReport',function(e){
            e.preventDefault();
            el=$(this);
            email=$("#customerEmail").val();
            if(email){
                el.attr('disabled','disabled');
                $.ajax({
                    type: "GET",
                    url: "https://www.e3melbusiness.com/?page=EmailsReport&action=cronSendAfter14DaysNotLoginReport",
                    data: {"email":email},
                    success: function (msg) {
                        $("#errors").html(msg);
                        el.removeAttr('disabled');
                    }
                });
            }

        });
        $(document).on('click','#sendAfter2WeeksReport',function(e){
            e.preventDefault();
            el=$(this);
            email=$("#customerEmail").val();
            if(email){
                el.attr('disabled','disabled');
                $.ajax({
                    type: "GET",
                    url: "https://www.e3melbusiness.com/?page=EmailsReport&action=cronSendAfter2WeeksReport",
                    data: {"email":email},
                    success: function (msg) {
                        $("#errors").html(msg);
                        el.removeAttr('disabled');
                    }
                });
            }

        });

    });
</script>
</body>

</html>