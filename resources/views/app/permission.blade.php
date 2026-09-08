@extends('layouts.master')
@section('content')
   <link type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" />
   <link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css" />
@if (Session::get('success'))

<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
    <strong>Process Success.</strong> {{ Session::get('success')}}<br><br>

</div>
@endif
<?php 
/*$permission_fields = array(
  'Student view',
  'Student Update',
  'Student Delete',
  'Add Student Attendance',
  'View Student Attendance',
  'View Student Monthly Reports',
  'Add Marks',
  'View Marks',
  'Delete Marks',
  'Generate Result',
  'Search Result',
  'promote Student',
  'Add Fess',
  'View Fess',
  'Delete Fess',
  'View Fess Report',
  'View Result Reports',
  'View Attendance Reports',
  'View Sms/voice log Reports',
  //'View Student Monthly Reports',
  'Class View',
  'Class Add',
  'Class update',
  'Class delete',
  'Sections view',
  'Section add',
  'Section update',
  'Section View',
  'Teacher View',
  'Teacher Add',
  'Teacher update',
  'Teacher delete',
  'Teacher timetable add',
  'Teacher timetable view',
  'Send Sms/Voice',
  'Setting GPA Rule view',
  'GPA Rule add',
  'GPA Rule update',
  'GPA Rule delete',
  'holidays add',
  'holidays view',
  'holidays delete',
  'Class off view',
  'Class off add',
  'Class off delete',
  'Institute information add',
  'Grade system (auto,manual)',

  );*/
$permission_fields = array(
          'Student View',
          'Student Add',
          'Student Update',
          'Student Delete',
          'Student Info',
          'Student Student Portal Access',
          'Student Student Bulk Add',
          'Family',

          'Add Student Attendance',
          'View Student Attendance',
          'View Student Monthly Reports',
          'View Attendance Reports',

          'Add Marks',
          'View Marks',
          'Delete Marks',
          'Generate Result',
          'Search Result',
          'Exam View',
          'Exam Add',
          'Exam update',
          'Exam delete',
          'Gradesheet View',
          'Gradesheet Print',
          'Paper View',
          'Paper Add',
          'Paper update',
          'Paper delete',

          
          'Add Fess',
          'View Fess',
          'Update Fess',
          'Delete Fess',
          'View Fess Report',
          'View Result Reports',
          

          
          
          //'View Student Monthly Reports',
          'Class View',
          'Class Add',
          'Class update',
          'Class delete',
          'Section View',
          'Section add',
          'Section update',
          'Section Delete',
          'Section Time Table',
          'Subject View',
          'Subject Add',
          'Subject update',
          'Subject delete',
          'promote Student',
          'Teacher View',
          'Teacher Add',
          'Teacher Bulk Add',
          'Teacher update',
          'Teacher delete',
          'Teacher timetable add',
          'Teacher timetable view',
          'Teacher Portal Access',
          'Send Sms/Voice',
          'Setting GPA Rule view',
          'GPA Rule add',
          'GPA Rule update',
          'GPA Rule delete',
          'GPA Rule View',
          'holidays add',
          'holidays view',
          'holidays delete',
          'Class off view',
          'Class off add',
          'Class off delete',
          'Institute information add',
          'Grade system (auto,manual)',
          
          
          'Send Notification',
          'View Sms/voice log Reports',
          'Accounting',
        );

?>
<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-th"></i>Permissions Setting</h2>

            </div>
             <div class="box-content">
               <div class="container">
              <form role="form" >
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label" for="class">admin</label>

                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-home blue"></i></span>
                                                  <input type="checkbox" name="admin" class="form-control" style="margin-top: -35px;">
                                            </div>
                                        </div>
                                    </div>
                                     
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label" for="section">teacher</label>

                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                 <input type="checkbox" name="teacher" class="form-control" style="margin-top: -35px;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label" for="section">Student</label>

                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                 <input type="checkbox" name="student" class="form-control" style="margin-top: -35px;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label" for="section">Accountant</label>

                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                 <input type="checkbox" name="accountant" class="form-control" style="margin-top: -35px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-primary pull-right"  type="submit"><i class="glyphicon glyphicon-th"></i>Get List</button>

                                </div>
                            </div>
                            <br>
                        </form>
<div id="user-permissions">
   <form role="form" action="{{url('/permission/create')}}" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

<table style="width:100%" id="permission" class="table responsive table-bordered">
  <thead>
    <tr>
     <th>Permissions</th>
     @if($admin=="yes")
     <th>Admin <input type="checkbox" id="checkAll"></th>
     @endif
     @if($teacherd=="yes")
      <th>Teachers <input type="checkbox" id="checkAll1"></th>
      @endif
      @if($studentss=="yes")
      <th>Students <input type="checkbox" id="checkAll2"></th>
      @endif
       @if($accountant=="yes")
      <th>Accuntant <input type="checkbox" id="checkAll3"></th>
      @endif 
    </tr>
  </thead>
   <tbody>
   <?php 

      $i       = 0 ;
      $student = count($permission_fields);
     /*if($studentss=="yes"){
      
        }
       else{
        $teacher =  $student  + count($permission_fields);
        }*/

      $teacher    =   $student  + count($permission_fields);
       $accounnt  =   $teacher  + count($permission_fields);
      //echo $teacher + $student;

     // echo "<pre>";print_r($permissions->toArray());
       //echo $studentss;
  $p=0;
  ?>
    @foreach($permission_fields as $permission_field)

    <?php $field_name = str_replace(" ","_",strtolower($permission_field)); 
    ?>

    @if($permissions)
    @if($p==0)
    <tr id="">

    <td colspan="5"><h2>Student Information</h2></td>
    </tr>
    @endif
    @if($p==8)
    <tr id="">

    <td colspan="5"><h2>Student Attendance</h2></td>
    </tr>
    @endif
    @if($p==12)
    <tr id="">

    <td colspan="5"><h2>Examination</h2></td>
    </tr>
    @endif
    @if($p==27)
    <tr id="">

    <td colspan="5"><h2>Fee</h2></td>
    </tr>
    @endif
     @if($p==33)
    <tr id="">

    <td colspan="5"><h2>Acadamic</h2></td>
    </tr>
    @endif

    @if($p==55)
    <tr id="">

    <td colspan="5"><h2>Settings</h2></td>
    </tr>
    @endif

<tr>

      <td width="50"><p>{{$permission_field}}</p></td>
      
      @if($permissions[$i]->permission_group=='admin')
      @if($admin=="yes")
      <td width="50">
        <div class="btn-group btn-toggle">
            <input class="cb-element chb admins" data-toggle="toggle" id="admin_{{$field_name}}" data-on="Yes" data-off="No" data-width="100"   name="admin[{{$field_name}}]" data-onstyle="success" data-offstyle="danger" type="checkbox"  @if($permissions[$i]->permission_type=='yes') checked @endif  >                                            
        </div>
      </td>
      @endif
      @endif
      
       @if($permissions[$teacher]->permission_group=='teacher')
       @if($teacherd=="yes")
        <td width="50">
          <div class="btn-group btn-toggle">
            <input class="cb-element1 chb" data-toggle="toggle" id="teacher_{{$field_name}}" data-on="Yes" data-off="No" data-width="100"   name="teacher[{{$field_name}}]" data-onstyle="success" data-offstyle="danger" type="checkbox"   @if($permissions[$teacher]->permission_type=='yes') checked @endif >                                            
          </div>

          </div>
        </td>
      @endif
      @endif
      
       @if($permissions[$student]->permission_group=='student')
       @if($studentss=="yes")
      <td width="50">
        <div class="btn-group btn-toggle">
          <input class="cb-element2 chb" data-toggle="toggle" id="student_{{$field_name}}" data-on="Yes" data-off="No" data-width="100"   name="student[{{$field_name}}]" data-onstyle="success" data-offstyle="danger" type="checkbox" @if($permissions[$student]->permission_type=='yes') checked @endif >                                            
        </div>
      </td>
      @endif
      @endif
     
      @if($permissions[$accounnt]->permission_group=='accountant')
      @if($accountant=="yes")
     
      <td width="50">
        <div class="btn-group btn-toggle">
          <input class="cb-element3 chb" data-toggle="toggle" id="accutant_{{$field_name}}" data-on="Yes" data-off="No" data-width="100"   name="accutant[{{$field_name}}]" data-onstyle="success" data-offstyle="danger" type="checkbox" @if($permissions[$accounnt]->permission_type=='yes') checked @endif >                                            
        </div>
      </td>
      @endif
      @endif

    </tr>
    <?php 
     $i++        ;
     $p++        ;
     $student++  ;
     $teacher++  ;
     $accounnt++ ;
    ?>
   @else

    <tr>
      <td>{{$permission_field}}</td>
      @if($admin=="yes")
      <td>
        <div class="btn-group btn-toggle">
            <input class="cb-element chb" data-toggle="toggle" id="admin_{{$field_name}}" data-on="Yes" data-off="No" data-width="100"   name="admin[{{$field_name}}]" data-onstyle="success" data-offstyle="danger" type="checkbox"  >                                            
        </div>
      </td>
      @endif
     @if($teacherd=="yes")
      <td>
        <div class="btn-group btn-toggle">
          <input class="cb-element1 chb" data-toggle="toggle" id="teacher_{{$field_name}}" data-on="Yes" data-off="No" data-width="100"   name="teacher[{{$field_name}}]" data-onstyle="success" data-offstyle="danger" type="checkbox"    >                                            
        </div>

        </div>
      </td>
      @endif
      @if($studentss=="yes")
      <td>
        <div class="btn-group btn-toggle">
          <input class="cb-element2 chb" data-toggle="toggle" id="student_{{$field_name}}" data-on="Yes" data-off="No" data-width="100"   name="student[{{$field_name}}]" data-onstyle="success" data-offstyle="danger" type="checkbox" >                                            
        </div>
      </td>
      @endif
      @if($accountant=="yes")
      <td>
        <div class="btn-group btn-toggle">
          <input class="cb-element3 chb" data-toggle="toggle" id="accutant_{{$field_name}}" data-on="Yes" data-off="No" data-width="100"   name="accutant[{{$field_name}}]" data-onstyle="success" data-offstyle="danger" type="checkbox" >                                            
        </div>
      </td>
    </tr>
   @endif
   @endif
   @endforeach
    
  </tbody>
</table>
  </div>
</div>
<!--button save -->
        <div class="row">
         <div class="col-md-12">
           <button class="btn btn-primary pull-right" id="btnsave" type="submit"><i class="glyphicon glyphicon-plus"></i>Save</button>
             </form>

            <div id="push"></div>
        

           
        </div>
        </div>

     
@stop
@section('script')
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
<script>

/*$(function() {
    $("td").find("p").hide();
    $("table").click(function(event) {
        event.stopPropagation();
        var $target = $(event.target);
        if ( $target.closest("td").attr("colspan") > 1 ) {
            $target.slideUp();
        } else {
            $target.closest("tr").next().find("p").slideToggle();
        }                    
    });
});*/
$( document ).ready(function() {

  $('#permissione').DataTable();

  $("#checkAll").change(function () {
  //alert(34);
  //alert(JSON.stringify($("input:checkbox.cb-element").prop('checked', $(this).prop("checked"))));
    $("input:checkbox.cb-element").prop('checked', $(this).prop("checked")).change();
});
$(".cb-element").change(function () {
    _tot = $(".cb-element").length 
    //alert(_tot);             
    _tot_checked = $(".cb-element:checked").length;
    
    if(_tot != _tot_checked){
      $("#checkAll").prop('checked',false);
    }
});

$("#checkAll1").change(function () {
  //alert(34);
  //alert(JSON.stringify($("input:checkbox.cb-element").prop('checked', $(this).prop("checked"))));
    $("input:checkbox.cb-element1").prop('checked', $(this).prop("checked")).change();
});
$(".cb-element1").change(function () {
    _tot = $(".cb-element1").length 
    //alert(_tot);             
    _tot_checked = $(".cb-element1:checked").length;
    
    if(_tot != _tot_checked){
      $("#checkAll1").prop('checked',false);
    }
});

$("#checkAll2").change(function () {
  //alert(34);
  ///alert(JSON.stringify($("input:checkbox.cb-element").prop('checked', $(this).prop("checked"))));
    $("input:checkbox.cb-element2").prop('checked', $(this).prop("checked")).change();
});
$(".cb-element2").change(function () {
    _tot = $(".cb-element2").length 
    //alert(_tot);             
    _tot_checked = $(".cb-element2:checked").length;
    
    if(_tot != _tot_checked){
      $("#checkAll2").prop('checked',false);
    }
});
$("#checkAll3").change(function () {
  //alert(34);
  ///alert(JSON.stringify($("input:checkbox.cb-element").prop('checked', $(this).prop("checked"))));
    $("input:checkbox.cb-element3").prop('checked', $(this).prop("checked")).change();
});
$(".cb-element3").change(function () {
    _tot = $(".cb-element3").length 
    //alert(_tot);             
    _tot_checked = $(".cb-element3:checked").length;
    
    if(_tot != _tot_checked){
      $("#checkAll3").prop('checked',false);
    }
});

   //$('#timepicker1').timepicker();
    $('#timepicker').timepicker({
        timeFormat: 'HH:mm:ss',
    });

            $('#timepicker1').timepicker();
    
});

$("#adminchckwww").click(function(e) {
    // this function will get executed every time the #home element is clicked (or tab-spacebar changed)
    if($(this).is(":checked")) // "this" refers to the element that fired the event
    {
        alert('home is checked');
        /*$(':checkbox').each(function () {
          //$(this).removeAttr('checked');
          $('input[type="radio"]').prop('checked', false);

        })*/
        $('input:checkbox[name=admin]').each(function () { $(this).prop('checked', true); });
          
    }else{
      alert('home is unchecked');
      $('.admins').prop('checked', $(e.target).prop('checked'));

     // $("input:checkbox[name=admin]").prop('checked', $(this).prop("checked",false));
       //$('input:checkbox').removeAttr('checked');
      //$('input:checkbox[name=admin]').each(function () { alert($(this)); $(this).prop('checked', false); });
    }
});
</script>
@stop
