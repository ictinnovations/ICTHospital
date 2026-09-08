@extends('layouts.master')
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
  <strong>Whoops.</strong><br>{{ Session::get('error')}}<br>
</div>

@endif
<div class="row">
  <div class="box col-md-12">

    <div class="box-inner">
      <div data-original-title="" class="box-header well">
        <h2><i class="glyphicon glyphicon-home"></i> Class List</h2>
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
         @if($class)
              <form role="form" action="{{url('/class/update')}}" method="post">
                         <input type="hidden" name="_token" value="{{ csrf_token() }}">
                   <input type="hidden" name="id" value="{{$class->id }}">

                     <div class="row">
                     <div class="col-md-12">
                       <div class="col-md-4">
                           <div class="form-group">
                         <label for="for">Name  <b>*</b></label>
                         <div class="input-group">
                             <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <input type="text" class="form-control" required name="name" value="{{$class->name}}" placeholder="Class Name">
                         </div>
                     </div>
                       </div>
                       {{--<div class="col-md-6">
                           <div class="form-group">
                        <label for="gpa" style="font-weight: 600 !important;">Numeric Value of Class <small>[play=-2,nusery=-1,parp=0,One=1,Six=6,Ten=10 etc]</small> <b>*</b></label>
                         <div class="input-group">
                             <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                              <select name="class" id="class" class="form-control" required>
                                @foreach($classes as $class)
                                <option value="{{$class->code}}" @if($exam->class_id == $class->id) selected @endif>{{$class->name}}</option>
                                @endforeach
                              </select>
                         </div>
                     </div>
                       </div>--}}
                       <div class="col-md-4">
                         <div class="form-group">
                             <label for="grade">Description  <b>*</b></label>
                             <div class="input-group">
                                 <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                  <textarea type="text" class="form-control" required name="description" placeholder="Class Description">{{$class->description}}</textarea>

                             </div>
                         </div>
                       </div>
                       
                       <div class="col-md-4">
                        <button class="btn btn-primary" type="submit" style="margin-top: 30px;"><i class="glyphicon glyphicon-plus"></i>Update</button>

                       </div>
                     </div>
                   </div>
                      </form>
                    @else
                    <form role="form" action="{{url('/class/create')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="row">
                     <div class="col-md-12">
                       <div class="col-md-2">
                           <div class="form-group">
                         <label for="for">Name  <b>*</b></label>
                         <div class="input-group">
                             <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <input type="text" class="form-control" autofocus required name="name" placeholder="Class Name">
                         </div>
                     </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-group">
                         <label for="gpa" style="font-weight: 600 !important;">Numeric Value of Class <small>[play=-2,nusery=-1,parp=0,One=1,Six=6,Ten=10 etc]</small> <b>*</b></label>
                         <div class="input-group">
                             <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <input type="number" min="-3" max="14" class="form-control" required name="code" placeholder="One=1,Six=6,Ten=10 etc">

                         </div>
                     </div>
                       </div>
                       <div class="col-md-2">
                         <div class="form-group">
                             <label for="grade">Description  <b>*</b></label>
                             <div class="input-group">
                                 <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                  <textarea type="text" class="form-control" required name="description" placeholder="Class Description"></textarea>

                             </div>
                         </div>
                       </div>
                       <div class="col-md-2">
                        <button class="btn btn-primary" type="submit" style="margin-top: 30px;"><i class="glyphicon glyphicon-plus"></i>Add</button>

                       </div>

                     </div>
                   </div>
                      <br>
                        </form>
                    @endif
        <table id="classList" class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th style="width:20%">Code</th>
              <th style="width:30%">Name</th>
              <th style="width:30%">Description</th>
              <th style="width:5%">Students</th>
              <th style="width:15%">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($Classes as $class)

            <tr>
              <td><a href="#"  onclick="get_sections_deatails('{{$class->code}}')">{{$class->code}}</a></td>
              <td>{{$class->name}}</td>
              <td>{{$class->description}}</td>
              <td>{{count_student('',$class->code)}}</td>
              {{--<td>{{$class->students}}</td>--}}

              <td>
                <a title='Edit' class='btn btn-info' href='{{url("/class/edit")}}/{{$class->id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>&nbsp&nbsp
                <a title='Delete' class='btn btn-danger' href='#' onclick="confirmed('{{$class->id}}')"> <i class="glyphicon glyphicon-trash icon-white"></i></a>&nbsp&nbsp
                <a title='View Diary' class='btn btn-warning' href='{{url("/class/diary/")}}/{{$class->code}}'> <i class="glyphicon glyphicon-zoom-in"></i></a>

              </td>
              @endforeach
            </tbody>
          </table>
          <br><br>
        </div>
      </div>
    </div>
  </div>
  @stop
  @section('model')

    <!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Class Section Detail</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
       <table id="classList" class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th style="width:30%">Name</th>
              <th style="width:30%">Description</th>
              <th style="width:30%">Students</th>
              <th style="width:30%">Teacher</th>
            </tr>
          </thead>
          <tbody id="details">
            
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
<!-- The Modal -->
<div class="modal" id="teacherModal">
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
              <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

  <script type="text/javascript">
  function get_sections_deatails(class_code){
    $.ajax({
      url:"{{ url('/get/section') }}"+"/"+class_code,
      method:"GET",
      //data:{name:class_name,code:class_code,description:class_des, _token:_token},
      success:function(data){
          $("#details").html(data);

          $('#myModal').modal('show');
      },

            error: function (textStatus, errorThrown) {
                alert(JSON.stringify(textStatus));
            }
     });

  }

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


  $( document ).ready(function() {
    $('#classList').dataTable({

       "sPaginationType": "bootstrap",
    });
  });


function confirmed(class_id)
{
  //alert(family_id);
  //return confirm('Are you sure you want to generate family vouchar?');
  var x = confirm('Are you sure you want to delete this Class');
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
  text: "If you delete this Class students marks and timetable of this Class disturb",
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

      window.location = "{{url('/class/delete')}}/"+class_id;
                              
    });
  } else if (
    // Read more about handling dismissals
    result.dismiss === Swal.DismissReason.cancel
  ) {
    swalWithBootstrapButtons.fire(
      'Cancelled',
      'Class Not Deleted :)',
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
