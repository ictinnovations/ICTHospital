@extends('layouts.master')
@section('style')
    <link href="{{url('/css/bootstrap-datepicker.css')}}" rel="stylesheet">

@stop
@section('content')
@if (Session::get('success'))
<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
    <strong>Process Success.</strong><br>{{ Session::get('success')}}<br>
</div>

@endif
@if (Session::get('error'))
    <div class="alert alert-warning">
        <button data-dismiss="alert" class="close" type="button">×</button>
        <strong> {{ Session::get('error')}}</strong>

    </div>
@endif
<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-book"></i> Family List</h2>

            </div>
            <div class="box-content">

                <div class="row">
                    <div class="col-md-12">

                        
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="clear: both;margin-top: 18px;" >
              <table id="studentList" class="table table-striped table-bordered" >
                                                         <thead>
                                                             <tr>
                                                                <th>Family Head Name</th>
                                                                 <th>Phone</th>
                                                                 <th>ID</th>
                                                                 <th>About Family Behavior</th>
                                                                 <th>Refered By</th>
                                                                
                                                                  <th>Action</th>
                                                             </tr>
                                                         </thead>
                                                         <tbody>
                                                           @foreach($students as $student)
                                                             <tr>
                                                                     <td>{{$student->fatherName}}</td>
                                                                     <td>{{$student->fatherCellNo}}</td>
                                                                     <td>{{$student->family_id}}</td>
                                                                     <td>{{$student->about_family}}</td>
                                                                     <td>{{getrefralindfo($student->family_id)}}</td>
                                                             
                                                       <td>
                                                        <?php 

                                                          if($student->family_id==''){
                                                              $family_id = $student->fatherCellNo;
                                                          }else{
                                                              $family_id = $student->family_id;
                                                          }
                                                        ?>
                                                  <a title='View' class='btn btn-success' href='{{url("/family/students")}}/{{$family_id}}'> <i class="glyphicon glyphicon-zoom-in icon-white"></i></a>&nbsp&nbsp
                                                  <a title='Edit' class='btn btn-info' href='{{url("/family/edit")}}/{{$family_id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>
                                                  {{--<a title='vouchar' class='btn btn-warning'  onclick="confirmed('{{$family_id}}');" href='#'> <i class="glyphicon glyphicon-shopping-cart icon-white"></i></a>--}}
                                                  <a title='vouchar history' class='btn btn-primary'  href='{{url("/family/vouchar_history")}}/{{$family_id}}'> <i class="glyphicon glyphicon-usd icon-white"></i></a>
                                                    {{--&nbsp&nbsp<a title='Delete' class='btn btn-danger' href='{{url("/student/delete")}}/{{$student->id}}' onclick="return confirm('Are you sure you want to delete this Student?');"> <i class="glyphicon glyphicon-trash icon-white"></i></a>
                                                    &nbsp&nbsp <a title='View' class='btn btn-success' href='{{url("/student/access")}}/{{$student->id}}'> <i class="glyphicon glyphicon-phone"></i></a>--}}
                                                    <?php /*&nbsp&nbsp <a title='View' class='btn btn-success' href='{{url("/fee/collections?class_id=$student->class_code&section=$student->section_id&session=$student->session&type=Monthly&month=$month&fee_name=$fee_name")}}'> <i class="glyphicon glyphicon-phone"></i></a>
                                                               */ ?>
                                                               </td>
                                                           @endforeach
                                                           </tbody>
                                </table>
                        </div>
                    </div>
                                <br><br>


        </div>
    </div>
</div>
</div>
@stop
@section('model')
<div id="modelshow"></div>
@stop
@section('script')
<script src="{{url('/js/bootstrap-datepicker.js')}}"></script>
<script type="text/javascript">
function checkm(type){
  alert(type);
  if(type=='multi'){

    $("#multis").show();
  }else{
     $("#multis").hide();
  }
}
function confirmed(family_id)
{
  //alert(family_id);
  //return confirm('Are you sure you want to generate family vouchar?');
  var x = confirm('Are you sure you want to generate family vouchar?');
                if (x){
                   //window.location.href('{{url("/family/vouchars")}}/'+family_id);
                  //window.location = "{{url('/family/vouchars')}}/"+family_id;
                  
                  $.ajax({
                      url: "{{url('/f_vouchar/model')}}"+'/'+family_id,
                      data: {
                        //format: 'json'
                      },
                      error: function(error) {
                        alert("Please fill all inputs correctly!");
                      },
                      //dataType: 'json',
                      success: function(data) {
                        console.log(data);
                       $('#modelshow').html(data);
                        $("#myModal"+family_id).modal('show');

                      },
                      type: 'GET'
                  });
                  // $("#billDetails").modal('show');
            

                 return true
               }
                else{
                  return false;
                }
}
$( document ).ready(function() {
  //$('#studentList').dataTable();
    $('#studentList').DataTable( {
        //pagingType: "simple",
        //"pageLength": 5,
      //  "pagingType": "full_numbers",
        dom: 'Bfrtip',
        buttons: [
            'print'
        ],
         "sPaginationType": "bootstrap",
       
    });
  
    //console.log(data);

     
});
</script>
@stop
