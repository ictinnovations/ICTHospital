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
        <a href="{{url('/student/create')}}" style=" margin-left:85%;margin-top: -40px;" class="btn btn-info btn-lg">Add Student</a>
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-book"></i> Student List</h2>

            </div>
            <div class="box-content">

                <div class="row">
                    <div class="col-md-12">

                        <form role="form" action="{{url('/student/list')}}" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="row">
                                <div class="col-md-12">
                                    {{--<div class="col-md-3">
                                        <div class="form-group ">
                                            <label for="session">session</label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                                                <input type="text" value="{{date('Y')}}" id="session" required="true" class="form-control datepicker2" name="session" value="{{$formdata->session}}"   data-date-format="yyyy">
                                            </div>
                                        </div>
                                    </div>--}}
                                    <input type="hidden"  id="session"  class="form-control datepicker2" name="session" value="{{get_current_session()->id}}"  >
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label" for="class">Class</label>

                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-home blue"></i></span>
                                                {!!  Form::select('class',$classes,$formdata->class,['class'=>'form-control','id'=>'class','required'=>'true']) !!}
                                            </div>
                                        </div>
                                    </div>
                                     
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label" for="section">Section</label>

                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <?php  $data=[
                                                        'A'=>'A',
                                                        'B'=>'B',
                                                        'C'=>'C',
                                                        'D'=>'D',
                                                        'E'=>'E',
                                                        'F'=>'F',
                                                        'G'=>'G',
                                                        'H'=>'H',
                                                        'I'=>'I',
                                                        'J'=>'J'
                                                ];?>
                                                {!! Form::select('section',$data,$formdata->section,['class'=>'form-control','id'=>'section','required'=>'true']) !!}


                                            </div>
                                        </div>
                                    </div>

                                    <!--<div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label" for="shift">Shift</label>

                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <?php /* $data=[
                                                        'Day'=>'Day',
                                                        'Morning'=>'Morning'
                                                ]; */ ?>
                                                {!! Form::select('shift',$data,$formdata->shift,['class'=>'form-control','required'=>'true']) !!}


                                            </div>
                                        </div>
                                    </div>-->

                                    <input type="hidden" name="shift" value="Morning">
                                   

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-primary pull-right"  type="submit"><i class="glyphicon glyphicon-th"></i>Get List</button>

                                </div>
                            </div>
                            <br>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style="clear: both;margin-top: 18px;" >
              <table id="studentList" class="table table-striped table-bordered" >
                                                         <thead>
                                                             <tr>
                                                                <th>Regi No</th>
                                                                 <th>Roll No</th>
                                                                 <th>Class</th>
                                                                 <th>section</th>
                                                                 <th>Name</th>
                                                                 <th>Gender</th>
                                                                  <th>Father Name</th>
                                                                   <th>Guardian's Contact</th>
                                                                 <th>Present Address</th>
                                                                 <th>Family Id</th>
                                                                  <th>Action</th>
                                                             </tr>
                                                         </thead>
                                                         <tbody>
                                                           @foreach($students as $student)
                                                             <tr>
                                                                  <td>{{$student->regiNo}}</td>
                                                                     <td>{{$student->rollNo}}</td>
                                                                     <td>{{$student->class}}</td>
                                                                     <td>{{$student->name}}</td>
                                                               <td>{{$student->firstName}} {{$student->middleName}} {{$student->lastName}}</td>
                                                               <td>{{$student->gender}}</td>
                                                                  <td>{{$student->fatherName}}</td>
                                                                  <td>   {!! "<b> Father:</b> ". $student->fatherCellNo. " <br \><b >Mother: </b>". $student->motherCellNo. $student->localGuardianCell !!}</td>
                                                                  <td>{{$student->presentAddress}}</td>
                                                                  <td><a class='btn btn-success' href="{{url('/family/students')}}/{{$student->family_id}}">{{$student->family_id}}</a></td>
                                                       <td>
                                                  <a title='View' class='btn btn-success' href='{{url("/student/view")}}/{{$student->id}}'> <i class="glyphicon glyphicon-zoom-in icon-white"></i></a>&nbsp&nbsp<a title='Edit' class='btn btn-info' href='{{url("/student/edit")}}/{{$student->id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>
                                                    &nbsp&nbsp<a title='Delete' class='btn btn-danger' href='#' onclick="confirmed('{{$student->id}}')"> <i class="glyphicon glyphicon-trash icon-white"></i></a>
                                                    &nbsp&nbsp <a title='View' class='btn btn-success' href='{{url("/student/access")}}/{{$student->id}}'> <i class="glyphicon glyphicon-phone"></i></a>
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
@section('script')
<script src="{{url('/js/bootstrap-datepicker.js')}}"></script>
              <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

<script type="text/javascript">

function confirmed(student_id)
{
  //alert(family_id);
  //return confirm('Are you sure you want to generate family vouchar?');
  var x = confirm('Are you sure you want to delete this Student');
                if (x){
                   //window.location.href('{{url("/family/vouchars")}}/'+family_id);
                 // window.location = "{{url('/subject/delete')}}/"+subject_id;
                  // $("#billDetails").modal('show');
                  const swalWithBootstrapButtons = Swal.mixin({
  customClass: {
    confirmButton: 'btn btn-success',
    cancelButton: 'btn btn-danger'
  },
  buttonsStyling: false,
})

swalWithBootstrapButtons.fire({
  title: 'Are you sure?',
  text: "If you delete this Student",
  type: 'warning',
  showCancelButton: true,
  confirmButtonText: 'Yes, delete it!',
  cancelButtonText: 'No, cancel!',
  reverseButtons: true
}).then((result) => {
  if (result.value) {
    swalWithBootstrapButtons.fire(
      'Deleted!',
      'Your file has been deleted.',
      'success'
    ).then(function() {

      window.location = "{{url('/student/delete')}}/"+student_id;
                              
    });
  } else if (
    // Read more about handling dismissals
    result.dismiss === Swal.DismissReason.cancel
  ) {
    swalWithBootstrapButtons.fire(
      'Cancelled',
      'Student Not Deleted :)',
      'error'
    )
  }
})
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
      $('#studentList').removeClass( 'display' ).addClass('table table-striped table-bordered');
  $(".datepicker2").datepicker( {
    format: " yyyy", // Notice the Extra space at the beginning
    viewMode: "years",
    minViewMode: "years",
    autoclose:true

  });

  getsections();
  $('#class').on('change',function() {
    getsections();
  });
  $('#session').on('change',function() {
    getsections();
  });
});

function getsections()
{
    var aclass = $('#class').val();
    var session = $('#session').val();
   // alert(aclass);
    $.ajax({
      url: "{{url('/section/getList')}}"+'/'+aclass+'/'+session,
      data: {
        format: 'json'
      },
      error: function(error) {
        alert("Please fill all inputs correctly!");
      },
      dataType: 'json',
      success: function(data) {
        $('#section').empty();
       //$('#section').append($('<option>').text("--Select Section--").attr('value',""));
        $.each(data, function(i, section) {
          //console.log(student);
         
          
        var opt="<option value='"+section.id+"'>"+section.name +' (  ' + section.students +' ) '+ "</option>"

        
          //console.log(opt);
          $('#section').append(opt);

        });
        //console.log(data);

      },
      type: 'GET'
    });
};
</script>
@stop
