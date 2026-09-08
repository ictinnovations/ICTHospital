@extends('layouts.master')
@section('style')
    <link href="/css/bootstrap-datepicker.css" rel="stylesheet">

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

            <a href="{{url('/teacher/create')}}" style=" margin-left:85%;margin-top: -40px;" class="btn btn-info btn-lg">Add Teacher</a>

        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-book"></i> Teacher List</h2>

            </div>
            <div class="box-content">

                <div class="row">
                    <div class="col-md-12">

                     <!--   <form role="form" action="/student/list" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label" for="class">Class</label>

                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-home blue"></i></span>
                                              <?php /*  {!!  Form::select('class',$classes,$formdata->class,['class'=>'form-control','required'=>'true']) !!} */ ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label" for="section">Section</label>

                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <?php  /*$data=[
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
                                                ]; */?>
                                               <?php /* {!! Form::select('section',$data,$formdata->section,['class'=>'form-control','required'=>'true']) !!}  */?>


                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label" for="shift">Shift</label>

                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <?php /* $data=[
                                                        'Day'=>'Day',
                                                        'Morning'=>'Morning'
                                                ]; */?>
                                               <?php /* {!! Form::select('shift',$data,$formdata->shift,['class'=>'form-control','required'=>'true']) !!}
           
*/?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group ">
                                            <label for="session">session</label>
                                            <div class="input-group">

                                                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                                               <?php /* <input type="text" value="{{date('Y')}}" id="session" required="true" class="form-control datepicker2" name="session" value="{{$formdata->session}}"   data-date-format="yyyy"> */ ?>
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
                        </form>-->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
              <table id="studentList" class="table table-striped table-bordered table-hover">
                                                         <thead>
                                                             <tr>
                                                              <th>Name</th>
                                                                 <th>Gender</th>
                                                                  <th>Religion</th>
                                                                   <th>Phone</th>
                                                                   <th>Guardian's Contact</th>
                                                                 <th>Present Address</th>
                                                                  <th>Action</th>
                                                             </tr>
                                                         </thead>
                                                         <tbody>
                                                           @foreach($teachers as $teacher)
                                                             <tr>
                                                                 
                                                               <td>{{$teacher->firstName}}  {{$teacher->lastName}}</td>
                                                               <td>{{$teacher->gender}}</td>
                                                               <td>{{$teacher->religion}}</td>
                                                               <td>{{$teacher->phone}}</td>
                                                               <td>{{$teacher->fatherCellNo }}</td>
                                                               <td>{{$teacher->presentAddress}}</td>
                                                      <td>
                                                        <a title='View' class='btn btn-success' href='{{url("/teacher/view")}}/{{$teacher->id}}'> <i class="glyphicon glyphicon-zoom-in icon-white"></i></a>&nbsp&nbsp
                                                        <a title='Edit' class='btn btn-info' href='{{url("/teacher/edit")}}/{{$teacher->id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>&nbsp&nbsp
                                                        <a title='Delete' class='btn btn-danger' href='#' onclick="confirmed('{{$teacher->id}}')"> <i class="glyphicon glyphicon-trash icon-white"></i></a>&nbsp&nbsp
                                                        <a title='view timetable' class='btn btn-success' href='{{url("/teacher/view-timetable")}}/{{$teacher->id}}'> <i class="glyphicon glyphicon-eye-open icon-white"></i></a>&nbsp&nbsp
                                                        <a title='Mobile Access'  class='btn btn-success' href='{{url("/teacher/access")}}/{{$teacher->id}}'> <i class="glyphicon glyphicon-phone"></i></a>&nbsp&nbsp
                                                        <a title='Create Diary'   class='btn btn-primary' href='{{url("/teacher/diary")}}/{{$teacher->id}}'> <i class="glyphicon glyphicon-folder-open"></i></a>&nbsp&nbsp
                                                        <a title='View Diary'     class='btn btn-warning' href='{{url("/teacher/diary/show")}}/{{$teacher->id}}'> <i class="glyphicon glyphicon-zoom-in"></i></a>
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
@section('script')
    <script src="/js/bootstrap-datepicker.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

<script type="text/javascript">
    $( document ).ready(function() {
        $('#studentList').dataTable({
          "sPaginationType": "bootstrap",
        });
        $(".datepicker2").datepicker( {
            format: " yyyy", // Notice the Extra space at the beginning
            viewMode: "years",
            minViewMode: "years",
            autoclose:true

        });
    });


    function confirmed(teacher_id)
{
  //alert(family_id);
  //return confirm('Are you sure you want to generate family vouchar?');
  var x = confirm('Are you sure you want to delete this teacher');
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
  text: "If you delete this teacher timetable and section of this teacher assign disturbed",
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

      window.location = "{{url('/teacher/delete')}}/"+teacher_id;
                              
    });
  } else if (
    // Read more about handling dismissals
    result.dismiss === Swal.DismissReason.cancel
  ) {
    swalWithBootstrapButtons.fire(
      'Cancelled',
      'teacher Not Deleted :)',
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
</script>
@stop
