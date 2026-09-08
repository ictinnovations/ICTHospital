<?php 
use App\Http\Controllers\InstituteController;
use App\Http\Controllers\PermissionController;
$get_grad = new InstituteController;
$system_grade = $get_grad->index1();
$get_permission = new PermissionController;
$permissions  = $get_permission->get_permission_by_role();
$permision =array();
foreach($permissions as $permission){
$permision[] = $permission->permission_name;
}
?>
<?php //echo "<pre>";print_r($permision); 
//exit;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!--
    TMgymNeJK1
        ===
        This comment should NOT be removed.

        Charisma v2.0.0

        Copyright 2012-2014 Muhammad Usman
        Licensed under the Apache License v2.0
        http://www.apache.org/licenses/LICENSE-2.0

        http://usman.it
        http://twitter.com/halalit_usman
        ===
    -->
    <meta charset="utf-8">
    <title>@if(Session::get('inName')=='') Ict Innovations School @else {{Session::get('inName')}} @endif</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}" />

    <meta name="description" content="">
    <meta name="author" content="">

    <!-- The styles -->
  <head>
<script type="text/javascript">
window.addEventListener('keydown',function(e){if(e.keyIdentifier=='U+000A'||e.keyIdentifier=='Enter'||e.keyCode==13){if(e.target.nodeName=='INPUT'&&e.target.type=='text'){e.preventDefault();return false;}}},true);
</script>

   <!-- <link id="bs-css" href="{{ URL::asset('css/bootstrap-lumen.min.css') }}" rel="stylesheet">
-->
     <!-- Fontfaces CSS-->
    <link href="{{ URL::asset('css/charisma-app.css') }}" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
    <link href="{{ URL::asset('/assets/css/font-face.css')}}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('/assets/vendor/font-awesome-4.7/css/font-awesome.min.css')}}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('/assets/vendor/font-awesome-5/css/fontawesome-all.min.css')}}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('/assets/vendor/mdi-font/css/material-design-iconic-font.min.css')}}" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" media="all">
   
    <link href="{{ URL::asset('/assets/vendor/bootstrap-4.1/bootstrap.css')}}" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="{{ URL::asset('/assets/vendor/animsition/animsition.min.css')}}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('/assets/vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css')}}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('/assets/vendor/wow/animate.css')}}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('/assets/vendor/css-hamburgers/hamburgers.min.css')}}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('/assets/vendor/slick/slick.css')}}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('/assets/vendor/select2/select2.min.css')}}" rel="stylesheet" media="all">
    <link href="{{ URL::asset('/assets/vendor/perfect-scrollbar/perfect-scrollbar.css')}}" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="{{ URL::asset('/assets/css/theme.css')}}" rel="stylesheet" media="all">

    <link href='{{ URL::asset('/bower_components/fullcalendar/dist/fullcalendar.css') }}' rel='stylesheet'>
    <link href='{{ URL::asset('/bower_components/fullcalendar/dist/fullcalendar.print.css') }}' rel='stylesheet' media='print'>
    <link href='{{ URL::asset('/bower_components/chosen/chosen.min.css') }}' rel='stylesheet'>

    <link href='https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css' rel='stylesheet'>

    


    <link rel="shortcut icon" href="{{ URL::asset('img/favicon.ico')}}">
    {{--<link href='//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css' rel='stylesheet'>--}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css">

        <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap2-toggle.min.css" rel="stylesheet">

    @yield("style")
    <style media="screen">
  b {
    color:red
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button {
    padding : 0px;
    margin-left: 0px;
    display: inline;
    border: 0px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    border: 0px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 3px !important;
   
}
.paginate_button.next  {       
      background: rgba(0, 0, 0, 0) url("./next1.gif") no-repeat scroll right center;
 }
 .paginate_button.next.disabled  {       
      background: rgba(0, 0, 0, 0) url("./next0.gif") no-repeat scroll right center !important;
 }
 
 .paginate_button.first  {       
      background: rgba(0, 0, 0, 0) url("first1.gif") no-repeat scroll right center;
 }
 
 .paginate_button.first.disabled  {       
      background: rgba(0, 0, 0, 0) url("first0.gif") no-repeat scroll right center !important;
 }
 
 .paginate_button.last  {       
      background: rgba(0, 0, 0, 0) url("last1.gif") no-repeat scroll right center;
 }
 
 .paginate_button.last.disabled  {       
      background: rgba(0, 0, 0, 0) url("last0.gif") no-repeat scroll right center !important;
 }
 
.paginate_button.previous  {         
      background: rgba(0, 0, 0, 0) url("prev1.gif") no-repeat scroll right center;
 }

 #studentList_next{
    margin-top: 7px;
   background-image: -webkit-linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5) !important;
background-image: -o-linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5) !important;
background-image: linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5);
background-repeat: no-repeat !important;
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff54b4eb', endColorstr='#ff1d9ce5', GradientType=0);
filter: none !important;
border-bottom: 1px solid #178acc !important;
color: #ffffff !important;
background-color: #2fa4e7 !important;
border-color: #2fa4e7 !important;

display: inline-block !important;
margin-bottom: 0 !important;
font-weight: normal !important;
text-align: center !important;
vertical-align: middle !important;
cursor: pointer !important;
background-image: none !important;
border: 1px solid transparent !important;
white-space: nowrap !important;
padding: 20px 12px !important;
font-size: 14px !important;
line-height: 1.42857143 !important;
border-radius: 4px !important;
-webkit-user-select: none !important;
-moz-user-select: none !important;
-ms-user-select: none !important;
user-select: none !important;
}
#studentList_previous{
margin-top: 7px;
margin-right: 5px;
background-image: -webkit-linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5) !important;
background-image: -o-linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5) !important;
background-image: linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5);
background-repeat: no-repeat !important;
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff54b4eb', endColorstr='#ff1d9ce5', GradientType=0);
filter: none !important;
border-bottom: 1px solid #178acc !important;
color: #ffffff !important;
background-color: #2fa4e7 !important;
border-color: #2fa4e7 !important;

display: inline-block !important;
margin-bottom: 0 !important;
font-weight: normal !important;
text-align: center !important;
vertical-align: middle !important;
cursor: pointer !important;
background-image: none !important;
border: 1px solid transparent !important;
white-space: nowrap !important;
padding: 20px 12px !important;
font-size: 14px !important;
line-height: 1.42857143 !important;
border-radius: 4px !important;
-webkit-user-select: none !important;
-moz-user-select: none !important;
-ms-user-select: none !important;
user-select: none !important;
}
#attendanceList_next{
    margin-top: 7px;
   background-image: -webkit-linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5) !important;
background-image: -o-linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5) !important;
background-image: linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5);
background-repeat: no-repeat !important;
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff54b4eb', endColorstr='#ff1d9ce5', GradientType=0);
filter: none !important;
border-bottom: 1px solid #178acc !important;
color: #ffffff !important;
background-color: #2fa4e7 !important;
border-color: #2fa4e7 !important;

display: inline-block !important;
margin-bottom: 0 !important;
font-weight: normal !important;
text-align: center !important;
vertical-align: middle !important;
cursor: pointer !important;
background-image: none !important;
border: 1px solid transparent !important;
white-space: nowrap !important;
padding: 20px 12px !important;
font-size: 14px !important;
line-height: 1.42857143 !important;
border-radius: 4px !important;
-webkit-user-select: none !important;
-moz-user-select: none !important;
-ms-user-select: none !important;
user-select: none !important;
}
#attendanceList_previous{
margin-top: 7px;
margin-right: 5px;
background-image: -webkit-linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5) !important;
background-image: -o-linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5) !important;
background-image: linear-gradient(#54b4eb, #2fa4e7 60%, #1d9ce5);
background-repeat: no-repeat !important;
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff54b4eb', endColorstr='#ff1d9ce5', GradientType=0);
filter: none !important;
border-bottom: 1px solid #178acc !important;
color: #ffffff !important;
background-color: #2fa4e7 !important;
border-color: #2fa4e7 !important;

display: inline-block !important;
margin-bottom: 0 !important;
font-weight: normal !important;
text-align: center !important;
vertical-align: middle !important;
cursor: pointer !important;
background-image: none !important;
border: 1px solid transparent !important;
white-space: nowrap !important;
padding: 20px 12px !important;
font-size: 14px !important;
line-height: 1.42857143 !important;
border-radius: 4px !important;
-webkit-user-select: none !important;
-moz-user-select: none !important;
-ms-user-select: none !important;
user-select: none !important;
}

.role {
    display: inline-block;
    line-height: 30px;
    font-size: 14px;
    color: #fff;
    padding: 0 15px;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    text-transform: capitalize;
}
.role.paid {
    background: green;
}
.role.unpaid {
    background: #fa4251;
}
table i{
  
  font-size: 20px;
}

.input-group-addon {
    display: none !important;
}

.box-header  h2 {

  margin-top: -15px !important;
}

.dataTables_filter{

    float: right !important;
    
}

.notifi-dropdown {
    left: 0px;
    top: 49px;
}
.js-dropdown a {
  display:block !important;
}
.bs-caret{
  display:none;
}

.box-content {
    padding: 33px !important;
}
table i {
    ##font-size: 10px !important;
}

.ScrollStyle
{
    max-height: 500px;
    overflow-y: scroll;
}

.mess-dropdown, .email-dropdown, .notifi-dropdown, .setting-dropdown{

  min-width: 239px !important;
}
  </style>
    <!-- jQuery -->
    <!--<script src="{{ URL::asset('/bower_components/jquery/jquery.min.js') }}"></script>
   -->
   <script src="https://code.jquery.com/jquery-3.3.1.js"></script>

    <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- The fav icon -->
    <link rel="shortcut icon" href="{{ URL::asset('img/favicon.ico')}}">
    <link href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css" rel="stylesheet">
}
</head>

<body @if(Request::is('student/create-file')) @else class="animsition" @endif>
<!-- topbar starts -->

<div class="page-wrapper">
      <aside class="menu-sidebar2 d-none d-lg-block">
        <!-- END HEADER MOBILE-->
        {{-- @yield('sidebarmenu') --}}
        @include('layouts.sidebarmenu') 

        </ul>
        </nav>
        </div>
        <!-- MENU SIDEBAR-->
        </aside>
        <!-- PAGE CONTAINER-->

        <div class="page-container2">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop2">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap2">
                            <div class="logo d-block d-lg-none">

                                <a class="js-arrow" href="#">
                                    @if(Session::get('inName')=='')
                                    <img src="images/icon/logo-white.png" alt="CoolAdmin" />
                                    @else
                                      <h2>{{Session::get('inName')}}</h2>
                                    @endif
                                </a>
                            </div>
                            <div class="header-button-item js-item-menu">
                                    <i class="zmdi zmdi-search"></i>
                                    <div class="search-dropdown js-dropdown btn-group">
                                        {{--<form action="">
                                            <input class="au-input au-input--full au-input--h65" type="text" placeholder="Search for datas &amp; reports..." />
                                            <span class="search-dropdown__icon">
                                                <i class="zmdi zmdi-search"></i>
                                            </span>
                                        </form>--}}

                                        <form class="navbar-search" name="navbar_search" action="{{url('/student/list')}}" id="navbar_search" method="post">
                                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                          <input type="hidden" name="search" value="yes">

                                              <input placeholder="Search Student" class="au-input au-input--full au-input--h65 " name="student_name" id="student_name" 
                                              type="text" autocomplete="off">
                                              <span class="search-dropdown__icon">
                                                <i class="zmdi zmdi-search"></i>
                                            </span>
                                              <div id="studentListd">
                                              </div>
                                        </form>
                                    </div>
                                </div>


                          <!-- Addmission dropdown starts -->

                          {{--<div class="btn-group pull-right">
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                              <i class="glyphicon glyphicon-print"></i><span class=""> Reports</span>
                              <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                              <li><a href="{{url('/gradesheet')}}">Marksheet</a></li>
                              <!-- <li><a href="{{url('/attendance/report')}}">Attendance</a></li>-->
                              <li><a href="{{url('/attendance/student_report')}}">Student Wise Attendance</a></li>
                              <li><a href="{{url('/tabulation')}}">Tabulationsheet</a></li>
                              <li><a href="{{url('/smslog')}}">Voice Log / SMS Log</a></li>
                              <!-- <li><a href="/accounting/report">Account By Type</a></li>
                              <li><a href="/accounting/reportsum">Account Balance</a></li>
                              <li><a href="/barcode">Barcode Generate</a></li>-->
                              <li class="divider"></li>
                              <!--<li><a href="{{url('/fees/report')}}"> Fee Collection Report</a></li>
                              -->
                              <li><a href="{{url('/fees/classreport')}}"> Fee Class Report</a></li>
                            </ul>
                          </div>
                          <!-- fees dropdown starts-->
                          <div class="btn-group pull-right">
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                              <i class="glyphicon glyphicon-list-alt"></i><span class=""> Fees</span>
                              <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                          

                              <li><a href="{{url('/template/create')}}"><i class="glyphicon glyphicon-folder-open"></i><span> Fee Collection Message</span></a></li>
                              @if(in_array('view_fess',$permision))
                              <li><a href="{{url('/fees/view')}}"><i class="glyphicon glyphicon-search"></i> Student Fees</a></li>
                               @endif

                               @if(in_array('add_fess',$permision))
                              <li><a href="{{url('/fees/invoices')}}"><i class="glyphicon glyphicon-shopping-cart"></i> Invoices</a></li>
                              <!--<li><a href="/fee/vouchar"><i class="glyphicon glyphicon-pencil"></i> Create Vouchar</a></li>-->
                              <li><a href="{{url('/fee/collection')}}"><i class="glyphicon glyphicon-pencil"></i> Fees Collection</a></li>
                              @endif
                              <li class="divider"></li>
                              
                              @if(in_array('view_fess',$permision))
                              <li><a href="{{url('/fees/list')}}"><i class="glyphicon glyphicon-list"></i> Fees List</a></li>
                              @endif
                              <li><a href="{{url('/fees/setup')}}"><i class="glyphicon glyphicon-cog"></i> Fees Setup</a></li>
                            </ul>
                          </div>--}}
                           @if(family_check()=='on')
                           <div class="noti-wrap">
                           <div class="noti__item js-item-menu">
                                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                          <i class="glyphicon glyphicon-user"></i>
                                          <span class=""> Family Lists</span>
                                         
                                        </button>
                                        <div class="mess-dropdown js-dropdown">
                                            
                                           <a href="{{url('/family/list')}}">
                                        <div class="notifi__item">
                                         <i class="glyphicon glyphicon-folder-open"></i>&nbsp;&nbsp;&nbsp;&nbsp;Get List</a>
                                        </div>
                                        </a>
                                        </div>
                                        </div>
                                        @endif

                           <div class="noti-wrap">
                           <div class="noti__item js-item-menu">
                                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                          <i class="glyphicon glyphicon-list-alt"></i>
                                          <span class=""> Fees</span>
                                         
                                        </button>
                                     
                                        <div class="mess-dropdown js-dropdown">
                                            
                                           <a href="{{url('/template/create')}}">
                                        <div class="notifi__item">
                                         <i class="glyphicon glyphicon-folder-open"></i>&nbsp;&nbsp;&nbsp;&nbsp; Fee Collection Message</a>
                                        </div>
                                        </a>
                                        <a href="{{url('/fees/view')}}">
                                          <div class="notifi__item">
                                           <i class="glyphicon glyphicon-search"></i> &nbsp;&nbsp;&nbsp;&nbsp;Student Fees
                                          </div>
                                        </a>
                                        @if(in_array('add_fess',$permision))
                                        <a href="{{url('/fees/invoices')}}">
                                          <div class="notifi__item">
                                           <i class="glyphicon glyphicon-shopping-cart"></i> &nbsp;&nbsp;&nbsp;&nbsp;Invoices
                                          </div>
                                        </a>
                                        @endif
                                        @if(in_array('add_fess',$permision))
                                        <a href="{{url('/fee/collection')}}">
                                          <div class="notifi__item">
                                           <i class="glyphicon glyphicon-pencil"></i> &nbsp;&nbsp;&nbsp;&nbsp;Fees Collection
                                          </div>
                                        </a>
                                        @endif
                                        @if(in_array('view_fess',$permision))
                                        <a href="{{url('/fees/list')}}">
                                          <div class="notifi__item">
                                          <i class="glyphicon glyphicon-list"></i>&nbsp;&nbsp;&nbsp;&nbsp;Fees List
                                          </div>
                                        </a>
                                        @endif
                                        
                                        {{--<a href="{{url('/fees/list')}}">
                                          <div class="notifi__item">
                                           <i class="glyphicon glyphicon-list"></i>&nbsp;&nbsp;&nbsp;&nbsp; Fees List
                                          </div>
                                        </a>--}}
                                        @if(in_array('add_fess',$permision))
                                        <a href="{{url('/fees/setup')}}">
                                          <div class="notifi__item">
                                           <i class="glyphicon glyphicon-cog"></i>&nbsp;&nbsp;&nbsp;&nbsp; Fees Setup
                                          </div>
                                        </a>
                                        @endif
                                        </div>
                                    </div>
                                  <div class="noti__item js-item-menu">
                                    {{--<i class="zmdi zmdi-notifications"></i>
                                    --}}
                                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                      <i class="glyphicon glyphicon-print"></i><span class=""> Reports</span>
                                    </button>
                                    <div class="notifi-dropdown js-dropdown">
                                         <a href="{{url('/gradesheet')}}">
                                        <div class="notifi__item">
                                         Marksheet
                                        </div>
                                        </a>
                                        <a href="{{url('/attendance/student_report')}}">
                                          <div class="notifi__item">
                                           Student Wise Attendance
                                          </div>
                                        </a>
                                        <a href="{{url('/tabulation')}}">
                                          <div class="notifi__item">
                                           Tabulationsheet
                                          </div>
                                        </a>
                                        <a href="{{url('/smslog')}}">
                                          <div class="notifi__item">
                                           Voice Log / SMS Log
                                          </div>
                                        </a>
                                         <div class="divider"></div>
                                        <a href="{{url('/fees/classreport')}}">
                                          <div class="notifi__item">
                                           Fee Class Report
                                          </div>
                                        </a>
                                        <div class="divider"></div>
                                        <a href="{{url('/accounting/report')}}">
                                          <div class="notifi__item">
                                            Accounting Report
                                          </div>
                                        </a>
                                        <div class="divider"></div>
                                        <a href="{{url('/accounting/reportsum')}}">
                                          <div class="notifi__item">
                                            Over All Accounting Report
                                          </div>
                                        </a>
                                    </div>
                                </div>
                              </div>
                            <div class="header-button2">
                                
                                {{--<div class="header-button-item has-noti js-item-menu">
                                    <i class="zmdi zmdi-notifications"></i>
                                    <div class="notifi-dropdown js-dropdown">
                                        <div class="notifi__title">
                                            <p>You have 3 Notifications</p>
                                        </div>
                                        <div class="notifi__item">
                                            <div class="bg-c1 img-cir img-40">
                                                <i class="zmdi zmdi-email-open"></i>
                                            </div>
                                            <div class="content">
                                                <p>You got a email notification</p>
                                                <span class="date">April 12, 2018 06:50</span>
                                            </div>
                                        </div>
                                        <div class="notifi__item">
                                            <div class="bg-c2 img-cir img-40">
                                                <i class="zmdi zmdi-account-box"></i>
                                            </div>
                                            <div class="content">
                                                <p>Your account has been blocked</p>
                                                <span class="date">April 12, 2018 06:50</span>
                                            </div>
                                        </div>
                                        <div class="notifi__item">
                                            <div class="bg-c3 img-cir img-40">
                                                <i class="zmdi zmdi-file-text"></i>
                                            </div>
                                            <div class="content">
                                                <p>You got a new file</p>
                                                <span class="date">April 12, 2018 06:50</span>
                                            </div>
                                        </div>
                                        <div class="notifi__footer">
                                            <a  class="js-arrow" href="#">All notifications</a>
                                        </div>
                                    </div>
                                </div>--}}
                                <div class="header-button-item mr-0 js-sidebar-btn">
                                    <i class="zmdi zmdi-menu"></i>
                                </div>
                                <div class="setting-menu js-right-sidebar d-none d-lg-block">
                                    <div class="account-dropdown__body">
                                        <div class="account-dropdown__item">
                                           <span class="hidden-sm hidden-xs" style="text-align: center;margin-left: 35px;"> {{Session::get('name')}}</span>
                                            
                                        </div> 
                                        <div class="account-dropdown__item">
                                            <a  class="" href="{{url('/settings')}}">
                                                <i class="zmdi zmdi-account"></i>Profile
                                            </a>
                                        </div>
                                        
                                        <?php /* <div class="account-dropdown__item ScrollStyle" style="overflow-y: auto;">
                                                
                                                 @if (Session::get('userRole')=="Admin")
                                                    <li class="has-sub" style="list-style-type: none;">
                                                      <a  class="js-arrow {{ Request::is('academicYear', 'gpa', 'users', 'holidays', 'class-off', 'institute', 'ictcore?type=sms', 'ictcore?type=voice','notification_type','ictcore/attendance','permission') ? 'open' : '' }}" href="#">
                                                        <i class="glyphicon glyphicon-cog"></i>
                                                         Settings
                                                        {{--<span class="arrow {{ Request::is('academicYear', 'gpa', 'users', 'holidays', 'class-off', 'institute', 'ictcore?type=sms', 'ictcore?type=voice','notification_type','ictcore/attendance','permission') ? 'up' : '' }}">
                                                          <i class="fas fa-angle-down"></i> 
                                                        </span> --}}                           
                                                      </a>
                                                      <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:{{ Request::is('academicYear', 'gpa', 'users', 'holidays', 'class-off', 'institute', 'ictcore?type=sms', 'ictcore?type=voice','notification_type','ictcore/attendance','permission') ? 'block' : 'none' }} ;">
                                                        <li class="{{ Request::is('academicYear') ? 'active' : '' }}"><a href="{{url('/academicYear')}}">Academic Year</a></li>
                                                        <li class="{{ Request::is('gpa') ? 'active' : '' }}"><a href="{{url('/gpa')}}">GPA Ruels</a></li>
                                                        <li class="{{ Request::is('users') ? 'active' : '' }}"><a href="{{url('/users')}}">Users</a></li>
                                                        <li class="{{ Request::is('holidays') ? 'active' : '' }}"><a href="{{url('/holidays')}}">Holidays</a></li>
                                                        <li class="{{ Request::is('class-off') ? 'active' : '' }}"><a href="{{url('/class-off')}}">Class Off Days</a></li>
                                                        <li class="{{ Request::is('institute') ? 'active' : '' }}"><a href="{{url('/institute')}}">Institute</a></li>
                                                        <li class="{{ Request::is('ictcore?type=sms') ? 'active' : '' }}"><a href="{{url('/ictcore?type=sms')}}">Sms Integration</a></li>
                                                        <li class="{{ Request::is('ictcore?type=voice') ? 'active' : '' }}"><a href="{{url('/ictcore?type=voice')}}">Voice Integration</a></li>
                                                        <li class="{{ Request::is('notification_type') ? 'active' : '' }}"><a href="{{url('/notification_type')}}">Notification Types</a></li>
                                                        <li class="{{ Request::is('ictcore/attendance') ? 'active' : '' }}"><a href="{{url('/ictcore/attendance')}}">Notifications</a></li>
                                                        <li class="{{ Request::is('permission') ? 'active' : '' }}"><a href="{{url('/permission')}}">Permission</a></li>
                                                          @if(accounting_check()!='' && accounting_check()=='yes' )
                                                            <li class="{{ Request::is('accounting') ? 'active' : '' }}"><a href="{{url('/accounting')}}">Accounting Api</a></li>
                                                          @endif 
                                                      </ul>
                                                    </li>
                                                    @endif
                                                </div> */ ?>
                                                <div class="account-dropdown__item">
                                            <a  class="" href="{{url('/users/logout')}}">
                                              <i class="fas fa-power-off"></i>Logout
                                            </a>
                                        </div>

                                        {{--<div class="account-dropdown__item">
                                            <a  class="js-arrow" href="#">
                                                <i class="zmdi zmdi-money-box"></i>Billing</a>
                                        </div>
                                    </div>
                                    <div class="account-dropdown__body">
                                        <div class="account-dropdown__item">
                                            <a  class="js-arrow" href="#">
                                                <i class="zmdi zmdi-globe"></i>Language</a>
                                        </div>
                                        <div class="account-dropdown__item">
                                            <a  class="js-arrow" href="#">
                                                <i class="zmdi zmdi-pin"></i>Location</a>
                                        </div>
                                        <div class="account-dropdown__item">
                                            <a  class="js-arrow" href="#">
                                                <i class="zmdi zmdi-email"></i>Email</a>
                                        </div>
                                        <div class="account-dropdown__item">
                                            <a  class="js-arrow" href="#">
                                                <i class="zmdi zmdi-notifications"></i>Notifications</a>
                                        </div>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- MObile menue -->
            <aside class="menu-sidebar2 js-right-sidebar d-block d-lg-none">
              
                @include('layouts.sidebarmenu') 

               {{-- <li class="has-sub">
                      <a  class="js-arrow {{ Request::is('academicYear', 'gpa', 'users', 'holidays', 'class-off', 'institute', 'ictcore?type=sms', 'ictcore?type=voice','notification_type','ictcore/attendance','permission','accounting') ? 'open' : '' }}" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                         Settings 
                        <span class="arrow {{ Request::is('academicYear', 'gpa', 'users', 'holidays', 'class-off', 'institute', 'ictcore?type=sms', 'ictcore?type=voice','notification_type','ictcore/attendance','permission','accounting') ? 'up' : '' }}">
                          <i class="fas fa-angle-down"></i> 
                        </span>                            
                      </a>
                      <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:{{ Request::is('academicYear', 'gpa', 'users', 'holidays', 'class-off', 'institute', 'ictcore?type=sms', 'ictcore?type=voice','notification_type','ictcore/attendance','permission','accounting') ? 'block' : 'none' }} ;">
                        <li class="{{ Request::is('academicYear') ? 'active' : '' }}"><a href="{{url('/academicYear')}}">Academic Year</a></li>
                        <li class="{{ Request::is('gpa') ? 'active' : '' }}"><a href="{{url('/gpa')}}">GPA Ruels</a></li>
                        <li class="{{ Request::is('users') ? 'active' : '' }}"><a href="{{url('/users')}}">Users</a></li>
                        <li class="{{ Request::is('holidays') ? 'active' : '' }}"><a href="{{url('/holidays')}}">Holidays</a></li>
                        <li class="{{ Request::is('class-off') ? 'active' : '' }}"><a href="{{url('/class-off')}}">Class Off Days</a></li>
                        <li class="{{ Request::is('institute') ? 'active' : '' }}"><a href="{{url('/institute')}}">Institute</a></li>
                        <li class="{{ Request::is('ictcore?type=sms') ? 'active' : '' }}"><a href="{{url('/ictcore?type=sms')}}">Sms Integration</a></li>
                        <li class="{{ Request::is('ictcore?type=voice') ? 'active' : '' }}"><a href="{{url('/ictcore?type=voice')}}">Voice Integration</a></li>
                        <li class="{{ Request::is('notification_type') ? 'active' : '' }}"><a href="{{url('/notification_type')}}">Notification Types</a></li>
                        <li class="{{ Request::is('ictcore/attendance') ? 'active' : '' }}"><a href="{{url('/ictcore/attendance')}}">Notifications</a></li>
                        <li class="{{ Request::is('permission') ? 'active' : '' }}"><a href="{{url('/permission')}}">Permission</a></li>
                        @if(accounting_check()!='' && accounting_check()=='yes' )
                        <li class="{{ Request::is('accounting') ? 'active' : '' }}"><a href="{{url('/accounting')}}">Accounting Api</a></li>
                        @endif 
                      </ul>
                    </li>--}}

                </ul>
                </nav>
                </div>
            </aside>
            <!-- END Mobile menu-->
            <!-- END HEADER DESKTOP-->
        

        <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        {{--<div class="row">
                            <div class="col-md-12">
                                <div class="overview-wrap">
                                    <h2 class="title-1">overview</h2>
                                    <button class="au-btn au-btn-icon au-btn--blue">
                                        <i class="zmdi zmdi-plus"></i>add item</button>
                                </div>
                            </div>
                        </div>--}}

                        <noscript>
                          <div class="alert alert-block col-md-12">
                            <h4 class="alert-heading">Warning!</h4>

                            <p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a>
                            enabled to use this site.
                            </p>
                          </div>
                        </noscript>
                          <!-- content starts -->
                          @if (isset($successmsg))
                            <div class="alert alert-success">
                              <button data-dismiss="alert" class="close" type="button">×</button>
                              <strong>{{ $success }}.</strong>
                            </div>
                          @endif
                          @if (isset($errormsg))
                            <div class="alert alert-danger">
                              <button data-dismiss="alert" class="close" type="button">×</button>
                              <strong>{{ $error }}.</strong>
                            </div>
                          @endif

                          
                          @if(Voucharcheck()==0)
                               <div class="alert alert-danger">
                                  <button data-dismiss="alert" class="close" type="button">×</button>
                                  <strong> Note!</strong> <strong>Please Create Vouchars this Months On dashboard</strong>
                               </div>
                          @endif
                          @yield('content')

                          <!-- content ends -->
                          <div class="row">
                            <div class="col-md-12">
                                <div class="copyright">
                                   <p class="col-md-9 col-sm-9 col-xs-12 copyright"> <a href="#" target="_blank">{{Session::get('inName')}}</a> &copy;<?php echo date("Y");?></p>
                                    <p class="col-md-3 col-sm-3 col-xs-12 powered-by">Developed by:
                                    <a href="http://ictvision.net/">IctVision</a></p>
                                </div>
                            </div>
                        </div>
                      </div>
                  </div>
            </div>
   </div>
                        
</div>

@yield('model')
<!-- Jquery JS-->
    <script src="{{ URL::asset('/assets/vendor/jquery-3.2.1.min.js')}}"></script>
    <!-- Bootstrap JS-->
    {{--<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
--}}
    <script src="{{ URL::asset('/assets/vendor/bootstrap-4.1/popper.min.js')}}"></script>
    <script src="{{ URL::asset('/assets/vendor/bootstrap-4.1/bootstrap.min.js')}}"></script>
    
    <!-- library for cookie management -->
    <script src="{{ URL::asset('/js/jquery.cookie.js') }}"></script>

    <script src="{{ URL::asset('/bower_components/chosen/chosen.jquery.min.js') }}"></script>
    <!-- plugin for gallery image view -->
    <!-- plugin for gallery image view -->
    <script src="{{ URL::asset('/bower_components/colorbox/jquery.colorbox-min.js') }}"></script>
    <!-- notification plugin -->
    <script src="{{ URL::asset('/js/jquery.noty.js') }}"></script>
    <!-- DAtatbale -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <!-- Vendor JS       -->
    <script src="{{ URL::asset('/assets/vendor/slick/slick.min.js')}}">
    </script>
    <script src="{{ URL::asset('/assets/vendor/wow/wow.min.js')}}"></script>
    <script src="{{ URL::asset('/assets/vendor/animsition/animsition.min.js')}}"></script>
    <script src="{{ URL::asset('/assets/vendor/bootstrap-progressbar/bootstrap-progressbar.min.js')}}">
    </script>
    <script src="{{ URL::asset('/assets/vendor/counter-up/jquery.waypoints.min.js')}}"></script>
    <script src="{{ URL::asset('/assets/vendor/counter-up/jquery.counterup.min.js')}}">
    </script>
    <script src="{{ URL::asset('/assets/vendor/circle-progress/circle-progress.min.js')}}"></script>
    <script src="{{ URL::asset('/assets/vendor/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
    <script src="{{ URL::asset('/assets/vendor/chartjs/Chart.bundle.min.js')}}"></script>
    </script>
    
    <!-- calender plugin -->
    <script src="{{ URL::asset('/bower_components/moment/min/moment.min.js') }}"></script>
    <script src='{{ URL::asset('/bower_components/fullcalendar/dist/fullcalendar.min.js') }}'></script>
    <!-- data table plugin -->
    <script src="{{ URL::asset('/js/jquery.iphone.toggle.js') }}"></script>
    <!-- Main JS-->
    <script src="{{ URL::asset('/assets/js/main.js')}}"></script>






<!-- star rating plugin -->
<script src="{{ URL::asset('/js/jquery.raty.min.js') }}"></script>
<!-- for iOS style toggle switch -->
<script src="{{ URL::asset('/js/jquery.iphone.toggle.js') }}"></script>
<!-- autogrowing textarea plugin -->
<script src="{{ URL::asset('/js/jquery.autogrow-textarea.js') }}"></script>
<!-- multiple file upload plugin -->

<script src="{{ URL::asset('/js/jquery.history.js') }}"></script>
<!-- application script for Charisma demo -->
<script src="{{ URL::asset('/js/charisma.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.14/jquery.mask.min.js"></script>
<script src="{{url('/js/bootstrap-datepicker.js')}}"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
{{--<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
<script>
$(document).ready(function(){

 $('#student_name').keyup(function(){ 
        var query = $('#student_name').val();
        if(query != '')
        {
         var _token = $('input[name="_token"]').val();
         $.ajax({
          url:"{{ url('student/search') }}",
          method:"POST",
          data:{query:query, _token:_token},
          success:function(data){
           $('#studentListd').fadeIn();  
           $('#studentListd').html(data);
          }
         });
        }else{
           $('#studentListd').fadeOut(); 
        }
    });
    $('#studentListd').on('click', 'li', function() { 
        // $('#student_name').val($(this).text());  
         var sd_id = $(this).attr('data-sid'); 
         $('#student_name').val(sd_id);
         $('#studentListd').fadeOut(); 
         $( "#navbar_search" ).submit(); 
    });

});
</script>
@yield('script')
</body>
</html>