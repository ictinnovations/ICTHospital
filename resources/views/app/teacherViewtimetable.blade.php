@extends('layouts.master')
@section('style')
    <link href="{{url('/css/bootstrap-datepicker.css')}}" rel="stylesheet">
          <link href="/css/timetable.css" rel="stylesheet">

@stop
@section('content')
    @if (Session::get('success'))
        <div class="alert alert-success">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <strong>Process Success.</strong> {{ Session::get('success')}}<br><a href="/teacher/list">View List</a><br>

        </div>
    @endif
  
    @php 
   if(isset($class)){

   }else{
    $class ='';
   }
   

    @endphp
 
<div class="row">
  <div class="box col-md-12">
    <div class="box-inner">
      <div data-original-title="" class="box-header well">
        <h2><i class="glyphicon glyphicon-user"></i> @if($class=='') Teacher Timetable @else Student Timetable @endif</h2>

      </div>
      <div class="box-content">
        @if (count($errors) > 0)
        <div class="alert alert-danger">
          <strong>Whoops!</strong> There were some problems with your input.<br><br>
          <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
  <ul class="nav nav-pills">
    <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#home">Monday</a></li>
    <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#menu1">Tuesday</a></li>
    <li class="nav-item"><a  class="nav-link"data-toggle="pill" href="#menu2">Wednesday</a></li>
    <li class="nav-item"><a  class="nav-link"data-toggle="pill" href="#menu3">Thursday</a></li>
     <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#menu4">Friday</a></li>
      <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#menu5">Sturday</a></li>
       <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#menu6">Sunday</a></li>
  </ul>
<br>
<br>
<br>
  <div class="tab-content">
    <div id="home" class="tab-pane active">
   
      <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="table-head">
            <th class="col-md-1">Time Start</th>
            <th class="col-md-1">Time End</th>
            <th class="col-md-3">Class</th>
            <th class="col-md-2">Section</th>
            <th class="col-md-4">Subjects</th>
            <th class="col-md-4">Teacher</th>
            <th class="col-md-4">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <!--  <th colspan="7">>Luni</th>-->
          </tr>
           @foreach ($timetables as $teacher)
             @if ($teacher->day =='monday')
             <?php 
                $classinfo = gclass_name($teacher->classname);
              ?>
             
          <tr>
            <td scope="row">{{ $teacher->stattime}}</td>
            <td >{{$teacher->endtime }}</td>
            <td>@if(isset($classinfo->name)) {{ $classinfo->name }} @endif</td>
            <td>{{$teacher->section_id }}</td>
            <td>{{$teacher->subname }}</td>
            <td><a href="#" onclick="getteacherinfo('{{$teacher->id}}')">{{$teacher->firstName}}{{$teacher->lastName}}</a></td>
            <th class="col-md-4">Action</th>
            <td>
              <a title='Edit' class='btn btn-info' href='{{url("/timetable/edit")}}/{{$teacher->timetable_id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>
             &nbsp&nbsp<a title='Delete' class='btn btn-danger' onclick="confirmed('{{$teacher->timetable_id}}');" href='#' > <i class="glyphicon glyphicon-trash icon-white"></i></a>

            </td>
          </tr>
          @endif
           @endforeach
        
          <!--<tr>
            <th colspan="7">>Marti</th>
          </tr>
          <tr>
            <th scope="row">08.00</th>
            <td>10.00</td>
            <td>Algoritmica grafurilor</td>
            <td>Curs</td>
            <td>Prof. dr. Cornelius Croitoru</td>
            <td>C309</td>
          </tr>-->
        </tbody>
      </table>
    </div>
    <div id="menu1" class="tab-pane">
   
       <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="table-head">
            <th class="col-md-1">Time Start</th>
            <th class="col-md-1">Time End</th>
            <th class="col-md-3">Class</th>
            <th class="col-md-2">Section</th>
            <th class="col-md-4">Subjects</th>
            <th class="col-md-4">Teacher</th>
            <th class="col-md-4">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <!--  <th colspan="7">>Luni</th>-->
          </tr>
           @foreach ($timetables as $teacher)
             @if ($teacher->day =='tuesday')
             <?php 
                $classinfo = gclass_name($teacher->classname);
              ?>
          <tr>
            <td scope="row">{{ $teacher->stattime}}</td>
            <td >{{$teacher->endtime }}</td>
            <td>@if(isset($classinfo->name)) {{ $classinfo->name }} @endif</td>
            <td>{{$teacher->section_id }}</td>
            <td>{{$teacher->subname }}</td>
            <td><a href="#" onclick="getteacherinfo('{{$teacher->id}}')">{{$teacher->firstName}}{{$teacher->lastName}}</a></td>

            <td>
              <a title='Edit' class='btn btn-info' href='{{url("/timetable/edit")}}/{{$teacher->timetable_id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>
             &nbsp&nbsp<a title='Delete' class='btn btn-danger' onclick="confirmed('{{$teacher->timetable_id}}');" href='#' > <i class="glyphicon glyphicon-trash icon-white"></i></a>

            </td>
          </tr>
          @endif
           @endforeach
        
          <!--<tr>
            <th colspan="7">>Marti</th>
          </tr>
          <tr>
            <th scope="row">08.00</th>
            <td>10.00</td>
            <td>Algoritmica grafurilor</td>
            <td>Curs</td>
            <td>Prof. dr. Cornelius Croitoru</td>
            <td>C309</td>
          </tr>-->
        </tbody>
      </table>
    </div>
    <div id="menu2" class="tab-pane">
      
       <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="table-head">
            <th class="col-md-1">Time Start</th>
            <th class="col-md-1">Time End</th>
            <th class="col-md-3">Class</th>
            <th class="col-md-2">Section</th>
            <th class="col-md-4">Subjects</th>
            <th class="col-md-4">Teacher</th>
            <th class="col-md-4">Action</th>
            
          </tr>
        </thead>
        <tbody>
          <tr>
          <!--  <th colspan="7">>Luni</th>-->
          </tr>
           @foreach ($timetables as $teacher)
             @if ($teacher->day =='wednesday')
             <?php 
                $classinfo = gclass_name($teacher->classname);
              ?>
          <tr>
            <td scope="row">{{ $teacher->stattime}}</td>
            <td >{{$teacher->endtime }}</td>
            <td>@if(isset($classinfo->name)) {{ $classinfo->name }} @endif</td>
            <td>{{$teacher->section_id }}</td>
            <td>{{$teacher->subname }}</td>
            <td><a href="#" onclick="getteacherinfo('{{$teacher->id}}')">{{$teacher->firstName}}{{$teacher->lastName}}</a></td>

            <td>
              <a title='Edit' class='btn btn-info' href='{{url("/timetable/edit")}}/{{$teacher->timetable_id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>
             &nbsp&nbsp<a title='Delete' class='btn btn-danger' onclick="confirmed('{{$teacher->timetable_id}}');" href='#' > <i class="glyphicon glyphicon-trash icon-white"></i></a>

            </td>
          </tr>
          @endif
           @endforeach
        
          <!--<tr>
            <th colspan="7">>Marti</th>
          </tr>
          <tr>
            <th scope="row">08.00</th>
            <td>10.00</td>
            <td>Algoritmica grafurilor</td>
            <td>Curs</td>
            <td>Prof. dr. Cornelius Croitoru</td>
            <td>C309</td>
          </tr>-->
        </tbody>
      </table>
    </div>
    <div id="menu3" class="tab-pane">
     
       <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="table-head">
            <th class="col-md-1">Time Start</th>
            <th class="col-md-1">Time End</th>
            <th class="col-md-3">Class</th>
            <th class="col-md-2">Section</th>
            <th class="col-md-4">Subjects</th>
            <th class="col-md-4">Teacher</th>
            <th class="col-md-4">Action</th>
            
          </tr>
        </thead>
        <tbody>
          <tr>
          <!--  <th colspan="7">>Luni</th>-->
          </tr>
           @foreach ($timetables as $teacher)
             @if ($teacher->day =='thursday')
             <?php 
                $classinfo = gclass_name($teacher->classname);
              ?>
          <tr>
            <td scope="row">{{ $teacher->stattime}}</td>
            <td >{{$teacher->endtime }}</td>
            <td>@if(isset($classinfo->name)) {{ $classinfo->name }} @endif</td>
            <td>{{$teacher->section_id }}</td>
            <td>{{$teacher->subname }}</td>
            <td><a href="#" onclick="getteacherinfo('{{$teacher->id}}')">{{$teacher->firstName}}{{$teacher->lastName}}</a></td>

            <td>
              <a title='Edit' class='btn btn-info' href='{{url("/timetable/edit")}}/{{$teacher->timetable_id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>
             &nbsp&nbsp<a title='Delete' class='btn btn-danger' onclick="confirmed('{{$teacher->timetable_id}}');" href='#' > <i class="glyphicon glyphicon-trash icon-white"></i></a>

            </td>
          </tr>
          @endif
           @endforeach
        
          <!--<tr>
            <th colspan="7">>Marti</th>
          </tr>
          <tr>
            <th scope="row">08.00</th>
            <td>10.00</td>
            <td>Algoritmica grafurilor</td>
            <td>Curs</td>
            <td>Prof. dr. Cornelius Croitoru</td>
            <td>C309</td>
          </tr>-->
        </tbody>
      </table>
    </div>
    <div id="menu4" class="tab-pane">
      
     <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="table-head">
            <th class="col-md-1">Time Start</th>
            <th class="col-md-1">Time End</th>
            <th class="col-md-3">Class</th>
            <th class="col-md-2">Section</th>
            <th class="col-md-4">Subjects</th>
            <th class="col-md-4">Teacher</th>
            <th class="col-md-4">Action</th>
            
          </tr>
        </thead>
        <tbody>
          <tr>
          <!--  <th colspan="7">>Luni</th>-->
          </tr>
           @foreach ($timetables as $teacher)
             @if ($teacher->day =='friday')
             <?php 
                $classinfo = gclass_name($teacher->classname);
              ?>
          <tr>
            <td scope="row">{{ $teacher->stattime}}</td>
            <td >{{$teacher->endtime }}</td>
            <td>@if(isset($classinfo->name)) {{ $classinfo->name }} @endif</td>
            <td>{{$teacher->section_id }}</td>
            <td>{{$teacher->subname }}</td>
            <td><a href="#" onclick="getteacherinfo('{{$teacher->id}}')">{{$teacher->firstName}}{{$teacher->lastName}}</a></td>

            <td>
              <a title='Edit' class='btn btn-info' href='{{url("/timetable/edit")}}/{{$teacher->timetable_id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>
             &nbsp&nbsp<a title='Delete' class='btn btn-danger' onclick="confirmed('{{$teacher->timetable_id}}');" href='#' > <i class="glyphicon glyphicon-trash icon-white"></i></a>

            </td>
          </tr>
          @endif
           @endforeach
        
          <!--<tr>
            <th colspan="7">>Marti</th>
          </tr>
          <tr>
            <th scope="row">08.00</th>
            <td>10.00</td>
            <td>Algoritmica grafurilor</td>
            <td>Curs</td>
            <td>Prof. dr. Cornelius Croitoru</td>
            <td>C309</td>
          </tr>-->
        </tbody>
      </table>
    </div>
       <div id="menu5" class="tab-pane">
     
       <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="table-head">
            <th class="col-md-1">Time Start</th>
            <th class="col-md-1">Time End</th>
            <th class="col-md-3">Class</th>
            <th class="col-md-2">Section</th>
            <th class="col-md-4">Subjects</th>
            <th class="col-md-4">Teacher</th>
            <th class="col-md-4">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <!--  <th colspan="7">>Luni</th>-->
          </tr>
           @foreach ($timetables as $teacher)
             @if ($teacher->day =='saturday')
             <?php 
                $classinfo = gclass_name($teacher->classname);
              ?>
          <tr>
            <td scope="row">{{ $teacher->stattime}}</td>
            <td >{{$teacher->endtime }}</td>
            <td>@if(isset($classinfo->name)) {{ $classinfo->name }} @endif</td>
            <td>{{$teacher->section_id }}</td>
            <td>{{$teacher->subname }}</td>
            <td><a href="#" onclick="getteacherinfo('{{$teacher->id}}')">{{$teacher->firstName}}{{$teacher->lastName}}</a></td>

            <td>
              <a title='Edit' class='btn btn-info' href='{{url("/timetable/edit")}}/{{$teacher->timetable_id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>
             &nbsp&nbsp<a title='Delete' class='btn btn-danger' onclick="confirmed('{{$teacher->timetable_id}}');" href='#' > <i class="glyphicon glyphicon-trash icon-white"></i></a>

            </td>
          </tr>
          @endif
           @endforeach
        
          <!--<tr>
            <th colspan="7">>Marti</th>
          </tr>
          <tr>
            <th scope="row">08.00</th>
            <td>10.00</td>
            <td>Algoritmica grafurilor</td>
            <td>Curs</td>
            <td>Prof. dr. Cornelius Croitoru</td>
            <td>C309</td>
          </tr>-->
        </tbody>
      </table>
    </div>
       <div id="menu6" class="tab-pane">
       <table class="table table-bordered table-striped table-hover">
        <thead>
          <tr class="table-head">
            <th class="col-md-1">Time Start</th>
            <th class="col-md-1">Time End</th>
            <th class="col-md-3">Class</th>
            <th class="col-md-2">Section</th>
            <th class="col-md-4">Subjects</th>
            <th class="col-md-4">Teacher</th>
            <th class="col-md-4">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <!--  <th colspan="7">>Luni</th>-->
          </tr>
           @foreach ($timetables as $teacher)
             @if ($teacher->day =='sunday')
             <?php 
                $classinfo = gclass_name($teacher->classname);
              ?>
          <tr>
            <td scope="row">{{ $teacher->stattime}}</td>
            <td >{{$teacher->endtime }}</td>
            <td>@if(isset($classinfo->name)) {{ $classinfo->name }} @endif</td>
            <td>{{$teacher->section_id }}</td>
            <td>{{$teacher->subname }}</td>
            <td><a href="#" onclick="getteacherinfo('{{$teacher->id}}')">{{$teacher->firstName}}{{$teacher->lastName}}</a></td>

            <td>
              <a title='Edit' class='btn btn-info' href='{{url("/timetable/edit")}}/{{$teacher->timetable_id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>
             &nbsp&nbsp<a title='Delete' class='btn btn-danger' onclick="confirmed('{{$teacher->timetable_id}}');" href='#' > <i class="glyphicon glyphicon-trash icon-white"></i></a>

            </td>
          </tr>
          @endif
           @endforeach
        
          <!--<tr>
            <th colspan="7">>Marti</th>
          </tr>
          <tr>
            <th scope="row">08.00</th>
            <td>10.00</td>
            <td>Algoritmica grafurilor</td>
            <td>Curs</td>
            <td>Prof. dr. Cornelius Croitoru</td>
            <td>C309</td>
          </tr>-->
        </tbody>
      </table>
    </div>
  </div>
@stop
@section('model')
    <!-- The Modal -->
<div class="modal"  data-backdrop="" id="teacherModal" role="dialog" aria-labelledby="preview-modal" aria-hidden="true" style="margin-top: 100px;" >
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Teacher Detail</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
       <table id="classList" class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th style="width:30%">Name</th>
              <th style="width:30%">Phone</th>
              <th style="width:30%">Email</th>
            </tr>
          </thead>
          <tbody id="tdetails">
            
          </tbody>
          </table>
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>
  @stop
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
<script>

function getteacherinfo(teacher_id){
    //alert(teacher_id)
       $.ajax({
      url:"{{ url('/get/teacher') }}"+"/"+teacher_id,
      method:"GET",
      //data:{name:class_name,code:class_code,description:class_des, _token:_token},
      success:function(data){
          $("#tdetails").html(data);

          $('#teacherModal').modal('show');
      },

            error: function (textStatus, errorThrown) {
                alert(JSON.stringify(textStatus));
            }
     });
  }
 $('#timepicker1').timepicker();
  $('#timepicker2').timepicker();

  function confirmed(teacher_id){
    var x = confirm('Are you sure you want to delete timetable?');
                if (x){
                  window.location = "{{url('/timetable/delete/')}}/"+teacher_id;
                 return true;
                }else{
                  return false;
                }
  }
</script>
@stop