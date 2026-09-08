@extends('layouts.master')

@section('style')
    <link href="{{url('/css/bootstrap-datepicker.css')}}" rel="stylesheet">
<style type="text/css">
  
  .modal-dialog {
    max-width: 915px !important;
  
}
</style>
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
              <button type="button" class="btn btn-primary" style="margin-left: 80px;" data-toggle="modal" data-target="#searchsd">
                    Search and Add Student this Family
              </button>
              <button type="button" class="btn btn-primary" style="margin-left: 0px;" data-toggle="modal" data-target="#students_shift">
                   Shift Student TO Other Family
              </button>
              <button type="button" class="btn btn-primary" style="margin-left: 0px;" data-toggle="modal" data-target="#exampleModal">
                    Add Family Discount
              </button>
              <a title='vouchar history' class='btn btn-primary'  href='{{url("/family/vouchar_history")}}/{{$family_id}}'> <i class="glyphicon glyphicon-usd icon-white"></i></a>
              <a title='Add Student' class='btn btn-primary'  href='{{url("/student/create")}}?family_id={{$family_id}}'> <i class="glyphicon glyphicon-plus icon-white"></i></a>

              <table id="studentList" class="table table-striped table-bordered" >
                <thead>
                  <tr>
                    <th>Roll No</th>
                    <th>Class</th>
                    <th>section</th>
                    <th>Name</th>
                    <th>Father Name</th>
                    <th>Guardian's Contact</th>
                    <th>Fees</th>
                    <th>Discount</th>
                    <th>Teacher</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($students as $student)
                    <tr>

                      <td>{{$student->rollNo}}</td>
                      <td>{{$student->class}}</td>
                      <td>{{$student->name}}</td>
                      <td>{{$student->firstName}} {{$student->middleName}} {{$student->lastName}}</td>
                      <td>{{$student->fatherName}}</td>
                      <td>   {!! "<b> Father:</b> ". $student->fatherCellNo. " <br \><b >Mother: </b>". $student->motherCellNo. $student->localGuardianCell !!}</td>
                      <td >{{$student->fee}}</td>
                      <td class="example">{{$student->discount_id}}</td>
                      <td>   {!! "<b> Teacher:</b> ". teacher_details_f($student->section_id)->firstName." ".teacher_details_f($student->section_id)->lastName. " <br \><b >Phone: </b>".teacher_details_f($student->section_id)->phone !!}</td>

                      <td>
                        <a title='View' class='btn btn-success' href='{{url("/student/view")}}/{{$student->id}}'> <i class="glyphicon glyphicon-zoom-in icon-white"></i></a>
                        &nbsp&nbsp<a title='Edit' class='btn btn-info' href='{{url("/student/edit")}}/{{$student->id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>
                        &nbsp&nbsp<a title='Delete' class='btn btn-danger' href='{{url("/student/delete")}}/{{$student->id}}' onclick="return confirm('Are you sure you want to delete this Student?');"> <i class="glyphicon glyphicon-trash icon-white"></i></a>
                        &nbsp&nbsp<a title='View' class='btn btn-success' href='' onclick="window.open('{{url("/gradesheet?class=$student->class_code&section=$student->section&regiNo=$student->regiNo")}}','','width=1500','height=500'); 
                        return false;"> <i class="glyphicon glyphicon-phone"></i></a>
                        <?php /*&nbsp&nbsp <a title='View' class='btn btn-success' href='{{url("/fee/collections?class_id=$student->class_code&section=$student->section_id&session=$student->session&type=Monthly&month=$month&fee_name=$fee_name")}}'> <i class="glyphicon glyphicon-phone"></i></a>
                        */ ?>
                      </td>
                    </tr>
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
<!-- Modal -->
<div class="modal" style="display:none;" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Family Student Discount</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="{{url('/family_discount/'.$family_id)}}" >
           <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <table id="" class="table table-striped table-bordered" >
              <thead>
                <tr>
                  <th>Roll No</th>
                  <th>Class</th>
                  <th>section</th>
                  <th>Name</th>
                  <th>Fee</th>
                  <th>Discount</th>
                </tr>
              </thead>
              <tbody>
                @foreach($students as $student)
                <input type="hidden" name="student_id[]" value="{{$student->id}}">
                  <tr>
                    <td>{{$student->rollNo}}</td>
                    <td>{{$student->class}}</td>
                    <td>{{$student->name}}</td>
                    <td>{{$student->firstName}} {{$student->lastName}}</td>
                    <td >{{$student->fee}}</td>
                    <td class="example"><input style="width: 69px;" name="discount[]" type="text" value="{{$student->discount_id}}"></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
           <input type="submit" class="btn btn-primary" value="Save changes" style="float: right;margin-top: 10px;">
      </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
       
      </div>
      
    </div>
  </div>
</div>




<!-- Modal -->
<div class="modal" style="display:none;" id="searchsd" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Student Search And Add </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="form-inline" method="post" action="{{url('/student/add/'.$family_id)}}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

          <i class="fas fa-search" aria-hidden="true"></i>
          <input class="form-control form-control-sm ml-3 w-50" type="text" placeholder="Search" id="qury" aria-label="Search">
          <input class="ml-3 w-20 btn btn-primary" type="button" onclick="searchstudent()" value=" Get Student">
         <br>
         <br>
         <br>
         <br>
          <table class="table table-striped" id="tbl" style="display:none">
            <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>RegiNo</th>
              <th>Class</th>
              <th>Section</th>
              
            </tr>
          </thead>
            <tbody id="lists">
              
            </tbody>


          </table>
          <br>
          <br>
          <br>
          <br>
          <input class="ml-3 w-70 btn btn-primary" type="submit" style="display:none" id="btnshow" value="Add Student To Family">

        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
       
      </div>
      
    </div>
  </div>
</div>


<!-- Modal -->
<div class="modal" style="display:none;" id="students_shift" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Shift Student To Other Family</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="post" action="{{url('/students/shift/'.$family_id)}}" >
           <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                  <label for="email">Family Phone:</label>
                  <input type="text" class="form-control" name="f_phone" id="phone" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="email">Family ID:</label>
                  <input type="text" class="form-control"  name="f_id" id="f_id" required>
                </div>

            </div>
            </div>
            <table id="studentList" class="table table-striped table-bordered" >
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Roll No</th>
                    <th>Class</th>
                    <th>section</th>
                    <th>Name</th>
                    <th>Father Name</th>
                    <th>Guardian's Contact</th>
                   
                  
                  </tr>
                </thead>
                <tbody>
                  @foreach($students as $student)
                    <tr>
                      <td><input type="checkbox" name="sid[]" class="form-control" value="{{$student->id}}"></td>
                      <td> {{$student->rollNo}}</td>
                      <td>{{$student->class}}</td>
                      <td>{{$student->name}}</td>
                      <td>{{$student->firstName}} {{$student->middleName}} {{$student->lastName}}</td>
                      <td>{{$student->fatherName}}</td>
                      <td>   {!! "<b> Father:</b> ". $student->fatherCellNo. " <br \><b >Mother: </b>". $student->motherCellNo. $student->localGuardianCell !!}</td>
                     
                      
                    </tr>
                  @endforeach
                </tbody>
              </table>
           <input type="submit" class="btn btn-primary" value="Save changes" style="float: right;margin-top: 10px;">
      </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
       
      </div>
      
    </div>
  </div>
</div>
@stop



@section('script')
<script src="{{url('/js/bootstrap-datepicker.js')}}"></script>
<script type="text/javascript">
$( document ).ready(function() {


$("#MyModal").modal();
     $('#exampleModal').on('shown.bs.modal', function() {
        $('#myInput').focus();
     });




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

function searchstudent(){

 // $('#myTable > tbody:last-child').append('<tr>...</tr><tr>...</tr>');
    var query = $('#qury').val();
   
    if(query != '')
    {
     var _token = $('input[name="_token"]').val();
     $.ajax({
      url:"{{ url('/family/student/search') }}",
      method:"POST",
      data:{query:query, _token:_token},
      success:function(data){
        $("#tbl").show();
        $("#btnshow").show();
       //$('#studentListd').fadeIn();  
       //$('#lists').html(data);
       $('#lists').append(data);
      }
     });
    }else{
       //$('#studentListd').fadeOut(); 
    }
    
}
</script>
@stop
