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
<!DOCTYPE html>
<html lang="en">
<head>
    <!--
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
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- The styles -->
  <link id="bs-css" href="{{ URL::asset('css/bootstrap-cerulean.min.css') }}" rel="stylesheet">
  
<script type="text/javascript">
window.addEventListener('keydown',function(e){if(e.keyIdentifier=='U+000A'||e.keyIdentifier=='Enter'||e.keyCode==13){if(e.target.nodeName=='INPUT'&&e.target.type=='text'){e.preventDefault();return false;}}},true);
</script>

   <!-- <link id="bs-css" href="{{ URL::asset('css/bootstrap-lumen.min.css') }}" rel="stylesheet">
-->
    <link href="{{ URL::asset('css/charisma-app.css') }}" rel="stylesheet">
    <link href='{{ URL::asset('/bower_components/fullcalendar/dist/fullcalendar.css') }}' rel='stylesheet'>
    <link href='{{ URL::asset('/bower_components/fullcalendar/dist/fullcalendar.print.css') }}' rel='stylesheet' media='print'>
    <link href='{{ URL::asset('/bower_components/chosen/chosen.min.css') }}' rel='stylesheet'>
    <link href='{{ URL::asset('/bower_components/colorbox/example3/colorbox.css') }}' rel='stylesheet'>

    <link href='{{ URL::asset('/bower_components/datatables/media/css/jquery.dataTables.css') }}' rel='stylesheet'>

    <link href='{{ URL::asset('/bower_components/responsive-tables/responsive-tables.css') }}' rel='stylesheet'>

     <!-- <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    -->
    <link href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css" rel="stylesheet">


    <link href='{{ URL::asset('/css/jquery.noty.css') }}' rel='stylesheet'>
    <link href='{{ URL::asset('/css/noty_theme_default.css') }}' rel='stylesheet'>
    <link href='{{ URL::asset('/css/elfinder.min.css') }}' rel='stylesheet'>
    <link href='{{ URL::asset('/css/elfinder.theme.css') }}' rel='stylesheet'>
    <link href='{{ URL::asset('/css/jquery.iphone.toggle.css') }}' rel='stylesheet'>
    <link href='{{ URL::asset('/css/uploadify.css') }}' rel='stylesheet'>
    <link href='{{ URL::asset('/css/animate.min.css') }}' rel='stylesheet'>
    <link href='{{ URL::asset('/css/app.css') }}' rel='stylesheet'>
    <link href='//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css' rel='stylesheet'>
    <link href='https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css' rel='stylesheet'>
    <link href="{{ URL::asset('/css/bootstrap-datepicker.css')}}" rel="stylesheet">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap2-toggle.min.css" rel="stylesheet">


    @yield("style")
    <style media="screen">
  body {
      color: #154d88;
      background: rgba(233, 237, 241, 0.27);
      font-family: "Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif;
      font-size: 13px;
      font-weight: 400;
      line-height: 1.471;
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

</head>

<body>
<!-- topbar starts -->
<div class="navbar navbar-default" role="navigation">

    <div class="navbar-inner">
        <button type="button" class="navbar-toggle pull-left animated flip">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <span class="iname">{{Session::get('inName')}}</span>

        <!-- user dropdown starts -->
        <div class="btn-group pull-right">
            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">

                <i class="glyphicon glyphicon-user"></i><span class="hidden-sm hidden-xs"> {{Session::get('name')}}</span>
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><a href="{{url('/settings')}}"><i class="glyphicon glyphicon-user"></i> Profile</a></li>
                <li class="divider"></li>
                <li><a href="{{url('/users/logout')}}"><i class="glyphicon glyphicon-log-out"></i> Logout</a></li>
            </ul>
        </div>
@if(Session::get('userRole') =="Admin")
        <div class="btn-group ">
          
                <form class="navbar-search" name="navbar_search" action="{{url('/student/list')}}" id="navbar_search" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="search" value="yes">

                    <input placeholder="Search Student" class="search-query form-control col-md-10" name="student_name" id="student_name" 
                    type="text" autocomplete="off">
                    <div id="studentListd">
                    </div>
                </form>
           
        </div>
        <!-- Addmission dropdown starts -->
    
        <div class="btn-group pull-right">
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
                <li><a href="{{url('/fees/view')}}"><i class="glyphicon glyphicon-search"></i> Student Fees</a></li>
                <!--<li><a href="/fee/vouchar"><i class="glyphicon glyphicon-pencil"></i> Create Vouchar</a></li>-->
                <li><a href="{{url('/fee/collection')}}"><i class="glyphicon glyphicon-pencil"></i> Fees Collection</a></li>

                <li class="divider"></li>
                <li><a href="{{url('/fees/list')}}"><i class="glyphicon glyphicon-list"></i> Fees List</a></li>
                <li><a href="{{url('/fees/setup')}}"><i class="glyphicon glyphicon-cog"></i> Fees Setup</a></li>
                
                

            </ul>

        </div>
        <!-- fees dropdown ends -->
    @endif
    </div>
</div>
<!-- topbar ends -->
<div class="ch-container">
    <div class="row">

        <!-- left menu starts -->
        <div class="col-sm-2 col-lg-2">
            <div class="sidebar-nav">
                <div class="nav-canvas">
                    <div class="nav-sm nav nav-stacked">

                    </div>
                    <ul class="nav nav-pills nav-stacked main-menu">
                        <li class="nav-header">Main</li>
                        <li><a class="ajax-link" href="{{url('/dashboard')}}"><i class="glyphicon glyphicon-th-large"></i><span> Dashboard</span></a>
                        </li>
                        @if (Session::get('userRole') =="Director")
                        <li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-cog"></i><span> Settings</span></a>
                              <ul class="nav nav-pills nav-stacked">
                                  <li><a href="{{url('/branches')}}">Branches</a></li>
                                  <!--<li><a href="{{url('/ictcore/fees')}}">Fees Message</a></li>
                                  -->
                                 <!-- <li><a href="{{url('/template/create')}}">Add Message</a></li>
                                  <li><a href="{{url('/template/list')}}">Recording List</a></li>
                                
                                  <li><a href="{{url('/schedule')}}">Fee Notification Reminder</a></li>
                               -->
                              </ul>
                        </li>
                        @endif
                        @if (Session::get('userRole') !="Director")
                        @if (Session::get('userRole') =="Admin")
                        <?php /* <li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-folder-open"></i><span> Levels</span></a>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="/level/create">Add New</a></li>
                                <li><a href="/level/list">Levels List</a></li>
                            </ul>
                        </li> */ ?>
                         @if(in_array('class_add',$permision) || in_array('class_update',$permision) || in_array('class_delete',$permision) || in_array('class_view',$permision))
                        <li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-home"></i><span> Class</span></a>
                            <ul class="nav nav-pills nav-stacked">
                                @if(in_array('class_add',$permision))
                                    <li><a href="{{url('/class/create')}}">Add New</a></li>
                                @endif
                                @if( in_array('class_update',$permision) || in_array('class_delete',$permision) || in_array('class_view',$permision))
                                    <li><a href="{{url('/class/list')}}">Class List</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if(in_array('section_add',$permision) || in_array('section_update',$permision) || in_array('section_delete',$permision) || in_array('section_time_table',$permision) || in_array('section_view',$permision))
                          <li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-folder-open"></i><span> Section</span></a>
                            <ul class="nav nav-pills nav-stacked">
                                @if(in_array('section_add',$permision))
                                <li><a href="{{url('/section/create')}}">Add New</a></li>
                                @endif
                                 @if(in_array('section_view',$permision))
                                <li><a href="{{url('/section/list')}}">Section List</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if(in_array('subject_view',$permision) || in_array('subject_add',$permision) || in_array('subject_update',$permision) || in_array('subject_delete',$permision))
                        <li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-book"></i><span> Subject</span></a>
                            <ul class="nav nav-pills nav-stacked">
                               @if(in_array('subject_add',$permision))
                                    <li><a href="{{url('/subject/create')}}">Add New</a></li>
                                @endif  
                                @if(in_array('subject_view',$permision)) 
                                    <li><a href="{{url('/subject/list')}}">Subject List</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif



                        <li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-hdd"></i><span> Paper Management</span></a>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="{{url('/question/create')}}">Add New</a></li>
                                <li><a href="{{url('/question/list')}}">List</a></li>
                                <li><a href="{{url('/paper/generate')}}"> Generate Paper</a></li>
                            </ul>
                        </li>

                         @if(in_array('student_view',$permision) || in_array('student_add',$permision) || in_array('student_delete',$permision) || in_array('student_student_bulk_add',$permision))
                        <li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-user"></i><span> Student</span></a>
                            <ul class="nav nav-pills nav-stacked">
                                @if(in_array('student_student_bulk_add',$permision))
                                <li><a href="{{url('/student/create-file')}}">Add from file</a></li>
                                @endif
                                @if(in_array('student_add',$permision))
                                <li><a href="{{url('/student/create')}}">Add New</a></li>
                                @endif
                                @if(in_array('student_view',$permision))
                                <li><a href="{{url('/student/list')}}">Student List</a></li>
                                @endif
                                @if(family_check()=='on')
                                <li><a href="{{url('/family/list')}}">Family List</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        
                        @endif
                        {{--<li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-text-width"></i><span> Teacher</span></a>
                            <ul class="nav nav-pills nav-stacked">
                            @if (Session::get('userRole') =="Admin")
                              <li><a href="{{url('/teacher/create-file')}}">Add from file</a></li>
                                <li><a href="{{url('/teacher/create')}}">Add New</a></li>
                                @endif
                                <li><a href="{{url('/teacher/list')}}">Teacher List</a></li>
                                @if (Session::get('userRole') =="Admin")
                                <li><a href="{{url('/teacher/create-timetable')}}">Timetable Management</a></li>
                                @endif

                            </ul>
                        </li>--}}
                          @if(in_array('teacher_view',$permision) || in_array('teacher_add',$permision) || in_array('teacher_delete',$permision) || in_array('add_teacher_bulk_add',$permision))
                         <li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-text-width"></i><span> Teacher</span></a>
                            <ul class="nav nav-pills nav-stacked">
                              @if(in_array('add_teacher_bulk_add',$permision))
                                <li><a href="{{url('/teacher/create-file')}}">Add from file</a></li>
                              @endif
                              @if(in_array('teacher_add',$permision))
                                <li><a href="{{url('/teacher/create')}}">Add New</a></li>
                              @endif
                               @if(in_array('teacher_view',$permision))
                                <li><a href="{{url('/teacher/list')}}">Teacher List</a></li>
                             @endif
                            @if(in_array('teacher_timetable_add',$permision))
                                <li><a href="{{url('/teacher/create-timetable')}}">Timetable Management</a></li>
                            @endif

                            </ul>
                        </li>
                        @endif
                        {{--<li class="accordion">
                           <a href="#"><i class="glyphicon glyphicon-pencil"></i><span> Attendance</span></a>
                           <ul class="nav nav-pills nav-stacked">
                           @if (Session::get('userRole') =="Admin")
                              <!-- <li><a href="/attendance/create-file">Add from file</a></li>-->
                               @endif
                               <!--<li><a href="{{url('/attendance/create')}}">Add</a></li>
                               -->
                               <li><a href="{{url('/attendance/list')}}">View</a></li>
                                <li><a href="{{url('/attendance/monthly-report')}}"><i class="glyphicon glyphicon-print"></i> Monthly Attendance Report</a></li>
                                <!--<li><a href="/teacher-attendance/monthly-report-2"><i class="glyphicon glyphicon-print"></i> Monthly Attendance Report Two</a></li>-->
                           </ul>
                       </li>--}}

                       @if(in_array('add_student_attendance',$permision) || in_array('view_student_attendance',$permision) || in_array('view_student_monthly_reports',$permision))
                        <li class="accordion">
                           <a href="#"><i class="glyphicon glyphicon-pencil"></i><span> Attendance</span></a>
                           <ul class="nav nav-pills nav-stacked">
                        
                              <!-- <li><a href="/attendance/create-file">Add from file</a></li>-->
                            @if(in_array('add_student_attendance',$permision))
                               <li><a href="{{url('/attendance/create')}}">Add</a></li>
                            @endif 
                            @if(in_array('view_student_attendance',$permision))
                               <li><a href="{{url('/attendance/list')}}">View</a></li>
                            @endif
                            @if(in_array('view_student_monthly_reports',$permision))
                                <li><a href="{{url('/attendance/monthly-report')}}"><i class="glyphicon glyphicon-print"></i> Monthly Attendance Report</a></li>
                            @endif
                                <!--<li><a href="/teacher-attendance/monthly-report-2"><i class="glyphicon glyphicon-print"></i> Monthly Attendance Report Two</a></li>-->
                           </ul>
                       </li>
                       @endif

                      {{--<li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-bullhorn"></i><span> Papers</span></a>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="{{url('/paper/create')}}">Add New</a></li>
                                <li><a href="{{url('/paper/list')}}">Paper List</a></li>
                            </ul>
                        </li>--}}
                    {{--<li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-fire"></i><span> Exams</span></a>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="{{url('/exam/create')}}">Add New</a></li>
                                <li><a href="{{url('/exam/list')}}">Exam List</a></li>
                            </ul>
                        </li>--}}

                         @if(in_array('exam_view',$permision) || in_array('exam_add',$permision))
                          <li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-fire"></i><span> Exams</span></a>
                            <ul class="nav nav-pills nav-stacked">
                                @if(in_array('exam_add',$permision))
                                <li><a href="{{url('/exam/create')}}">Add New</a></li>
                                @endif
                                @if(in_array('exam_view',$permision))
                                <li><a href="{{url('/exam/list')}}">Exam List</a></li>
                                @endif
                            </ul>
                          </li>
                        @endif
                   
                        {{--<li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-list-alt"></i><span> Mark Manage</span></a>
                            <ul class="nav nav-pills nav-stacked">
                             @if($system_grade=='' || $system_grade=='auto')
                                <li><a href="{{url('/mark/create')}}">Add New</a></li>
                                <li><a href="{{url('/mark/list')}}">Marks List</a></li>
                                 <li><a href="{{url('/template/creates')}}">Template</a></li>
                            @else
                                <li><a href="{{url('/mark/m_create')}}">Add New</a></li>
                                <li><a href="{{url('/mark/m_list')}}">Marks List</a></li>
                                <li><a href="{{url('/template/creates')}}">Template</a></li>
                            @endif
                            </ul>
                        </li>--}}

                        @if(in_array('add_marks',$permision) || in_array('view_marks',$permision))
                        <li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-list-alt"></i><span> Mark Manage</span></a>
                            <ul class="nav nav-pills nav-stacked">
                                @if($system_grade=='' || $system_grade=='auto')
                                    @if(in_array('add_marks',$permision))
                                    <li><a href="{{url('/mark/create')}}">Add New</a></li>
                                    @endif
                                    @if(in_array('view_marks',$permision))
                                    <li><a href="{{url('/mark/list')}}">Marks List</a></li>
                                    @endif
                                @else
                                    @if(in_array('add_marks',$permision))
                                    <li><a href="{{url('/mark/m_create')}}">Add New</a></li>
                                    @endif
                                    @if(in_array('view_marks',$permision))
                                    <li><a href="{{url('/mark/m_list')}}">Marks List</a></li>
                                    @endif
                                @endif
                                <li><a href="{{url('/template/creates')}}">Template</a></li>
                            </ul>
                        </li>
                       @endif
                        @if (Session::get('userRole') =="Admin")
                        {{--<li class="accordion">
                            <a href="#"><i class="glyphicon  glyphicon glyphicon-list"></i><span> Result</span></a>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="{{url('/result/generate')}}">Generate</a></li>
                                <li><a href="{{url('/result/search')}}">Search</a></li>
                                <li><a href="{{url('/results')}}">Search Public</a></li>

                            </ul>
                        </li>--}}
                        @if(in_array('generate_result',$permision) || in_array('search_result',$permision))
                        <li class="accordion">
                            <a href="#"><i class="glyphicon  glyphicon glyphicon-list"></i><span> Result</span></a>
                            <ul class="nav nav-pills nav-stacked">
                                @if(in_array('generate_result',$permision))
                                <li><a href="{{url('/result/generate')}}">Generate</a></li>
                                @endif
                                @if(in_array('search_result',$permision))
                                <li><a href="{{url('/result/search')}}">Search</a></li>
                                <li><a href="{{url('/results')}}">Search Public</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
                         @if(in_array('promote_student',$permision) )
                        <li class="">
                            <a href="{{url('/promotion')}}"><i class="glyphicon glyphicon-arrow-up"></i><span> Promotion</span></a>

                        </li>
                        @endif
                        
                        
                      {{--<li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-folder-open"></i><span> Message Template</span></a>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="{{url('/template/create')}}">Add New</a></li>
                                <li><a href="{{url('/template/list')}}">Message List</a></li>
                            </ul>
                        </li>--}}

                        {{--<li class="">
                            <a href="{{url('/template/create')}}"><i class="glyphicon glyphicon-folder-open"></i><span> Fee Collection Message</span></a>
                        </li>--}}

                        @if(in_array('send_notification',$permision) )
                        <li class="">
                            <a href="{{url('/message')}}"><i class="glyphicon glyphicon-envelope"></i><span> Voice / SMS</span></a>
                        </li>
                        @endif
                       <!-- <li class="accordion">
                            <a href="#"><i class="glyphicon  glyphicon glyphicon-list-alt"></i><span> Accounting</span></a>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="/accounting/sectors">Sectors</a></li>
                                <li><a href="/accounting/income">Add Income</a></li>
                                <li><a href="/accounting/incomelist">View Income</a></li>
                                <li><a href="/accounting/expence">Add Expence</a></li>
                                <li><a href="/accounting/expencelist">View Expence</a></li>

                            </ul>
                        </li>-->
                        @endif
                      <!--  <li class="accordion">
                          <a href="#"><i class="glyphicon glyphicon-envelope"></i><span> SMS</span></a>
                          <ul class="nav nav-pills nav-stacked">
                              <li><a href="/sms">Bulk SMS</a></li>
                          </ul>
                      </li> -->
                      @if (Session::get('userRole') =="Admin")
                       <?php /* <li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-print"></i><span> Reports</span></a>
                            <ul class="nav nav-pills nav-stacked">
                                <li><a href="/gradesheet">Marksheet</a></li>
                                <li><a href="/attendance/report">Attendance</a></li>
                                <li><a href="/tabulation">Tabulationsheet</a></li>
                                <li><a href="/smslog">Voice Log / SMS Log</a></li>
                                <li><a href="/accounting/report">Account By Type</a></li>
                                <li><a href="/accounting/reportsum">Account Balance</a></li>
                                 <li><a href="/barcode">Barcode Generate</a></li>
                                 <li><a href="/fees/report"> Fee Collection Report</a></li>
                                 <li><a href="/fees/classreport"> Fee Class Report</a></li>


                            </ul>
                        </li> */ ?>
                         @endif
                         <?php 
                         
                        //echo Session::get('userRole')."adeel";
                        //dd(session()->all());
                        ?>
                        @if (Session::get('userRole')=="Admin")
                        <li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-cog"></i><span> Settings</span></a>
                              <ul class="nav nav-pills nav-stacked">
                                  <li><a href="{{url('/academicYear')}}">Academic Year</a></li>
                                  <li><a href="{{url('/gpa')}}">GPA Ruels</a></li>
                                  <li><a href="{{url('/users')}}">Users</a></li>
                                  <li><a href="{{url('/holidays')}}">Holidays</a></li>
                                  <li><a href="{{url('/class-off')}}">Class Off Days</a></li>
                                  <li><a href="{{url('/institute')}}">Institute</a></li>
                                  <li><a href="{{url('/ictcore?type=sms')}}">Sms Integration</a></li>
                                  <li><a href="{{url('/ictcore?type=voice')}}">Voice Integration</a></li>
                                  <li><a href="{{url('/notification_type')}}">Notification Types</a></li>
                                  <li><a href="{{url('/ictcore/attendance')}}">Notifications</a></li>
                                  <li><a href="{{url('/permission')}}">Permission</a></li>
                                  <!--<li><a href="{{url('/ictcore/fees')}}">Fees Message</a></li>
                                  -->
                                 <!-- <li><a href="{{url('/template/create')}}">Add Message</a></li>
                                  <li><a href="{{url('/template/list')}}">Recording List</a></li>
                                
                                  <li><a href="{{url('/schedule')}}">Fee Notification Reminder</a></li>
                               -->
                              </ul>
                        </li>

                      <?php /*  
                     <li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-globe"></i><span> Site</span></a>
                            <ul class="nav nav-pills nav-stacked">
        
          <li>
            <a href="{{ URL::route('site.dashboard') }}">
              <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            </a>
          </li>
          <li class="accordion">
                            <a href="#"><i class="glyphicon glyphicon-home"></i><span> Home</span></a>
                            <ul class="nav nav-pills nav-stacked">
            <ul class="treeview-menu">
              <li><a href="{{URL::route('slider.index')}}"><i class="fa fa-picture-o text-aqua"></i> Sliders</a></li>
              <li><a href="{{URL::route('site.about_content')}}"><i class="fa fa-info text-aqua"></i> About Us</a></li>
              <li><a href="{{ URL::route('site.service') }}"><i class="fa fa-file-text text-aqua"></i> Our Services</a></li>
              <li><a href="{{ URL::route('site.statistic') }}"><i class="fa fa-bars"></i> Statistic</a></li>
              <li><a href="{{ URL::route('site.testimonial') }}"><i class="fa fa-comments"></i> Testimonials</a></li>
              <li><a href="{{ URL::route('site.subscribe') }}"><i class="fa fa-users"></i> Subscribers</a></li>
            </ul>
            </ul>
            </li>
            <li>
            <a href="{{ URL::route('class_profile.index') }}">
              <i class="fa fa-building"></i>
              <span>Class</span>
            </a>
          </li>
          <li>
            <a href="{{ URL::route('teacher_profile.index') }}">
              <i class="fa icon-teacher"></i>
              <span>Teachers</span>
            </a>
          </li>
          <li>
            <a href="{{ URL::route('event.index') }}">
              <i class="fa fa-bullhorn"></i>
              <span>Events</span>
            </a>
          </li>
          <li>
            <a href="{{ URL::route('site.gallery') }}">
              <i class="fa fa-camera"></i>
              <span>Gallery</span>
            </a>
          </li>
          <li>
            <a href="{{ URL::route('site.contact_us') }}">
              <i class="fa fa-map-marker"></i>
              <span>Contact Us</span>
            </a>
          </li>
          <li>
            <a href="{{ URL::route('site.faq') }}">
              <i class="fa fa-question-circle"></i>
              <span>FAQ</span>
            </a>
          </li>
          <li>
            <a href="{{ URL::route('site.timeline') }}"><i class="fa fa-clock-o"></i>
              <span>Timeline</span>
            </a>
          </li>
          <li>
            <a href="{{ URL::route('site.settings') }}"><i class="fa fa-cogs"></i>
              <span>Settings</span>
            </a>
          </li>
          <li>
            <a href="{{ URL::route('site.analytics') }}"><i class="fa fa-line-chart"></i>
              <span>Analytics</span>
            </a>
</li>
            </ul>
            </li>
            <?php */ ?>
            @endif
</li>





                          @endif
                    </ul>

                </div>
            </div>
        </div>
        <!--/span-->
        <!-- left menu ends -->

        <noscript>
            <div class="alert alert-block col-md-12">
                <h4 class="alert-heading">Warning!</h4>

                <p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a>
                    enabled to use this site.</p>
            </div>
        </noscript>

        <div id="content" class="col-lg-10 col-sm-10">
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


            @yield('content')




            <!-- content ends -->
        </div><!--/#content.col-md-0-->
   

    <footer class="footer">
        <hr>
        <p class="col-md-9 col-sm-9 col-xs-12 copyright"> <a href="#" target="_blank">{{Session::get('inName')}}</a> &copy;<?php echo date("Y");?></p>

        <p class="col-md-3 col-sm-3 col-xs-12 powered-by">Developed by:
        <a href="http://ictvision.net/">IctVision</a></p>
    </footer>
</div><!--/.fluid-container-->

<!-- external javascript -->

<script src="{{ URL::asset('/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>


<!-- library for cookie management -->
<script src="{{ URL::asset('/js/jquery.cookie.js') }}"></script>
<!-- calender plugin -->
<script src="{{ URL::asset('/bower_components/moment/min/moment.min.js') }}"></script>
<script src='{{ URL::asset('/bower_components/fullcalendar/dist/fullcalendar.min.js') }}'></script>
<!-- data table plugin -->
<!--<script src='{{ URL::asset('/bower_components/datatables/media/js/jquery.dataTables.js') }}'></script>
-->
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<!-- select or dropdown enhancer -->
<script src="{{ URL::asset('/bower_components/chosen/chosen.jquery.min.js') }}"></script>
<!-- plugin for gallery image view -->
<script src="{{ URL::asset('/bower_components/colorbox/jquery.colorbox-min.js') }}"></script>
<!-- notification plugin -->
<script src="{{ URL::asset('/js/jquery.noty.js') }}"></script>
<!-- library for making tables responsive -->
<script src="{{ URL::asset('/bower_components/responsive-tables/responsive-tables.js') }}"></script>

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
