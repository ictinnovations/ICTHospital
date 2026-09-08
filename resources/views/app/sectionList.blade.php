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
        <h2><i class="glyphicon glyphicon-home"></i> Section List</h2>
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

         @if($section)
              <form role="form" action="{{url('/section/update')}}" method="post">
                         <input type="hidden" name="_token" value="{{ csrf_token() }}">
                         <input type="hidden" name="id" value="{{$section->id }}">

                     <div class="row">
                     <div class="col-md-12">
                       <div class="col-md-4">
                           <div class="form-group">
                        <label for="name">Name <b>*</b></label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <input type="text" class="form-control" required name="name" value="{{old('name',$section->name)}}" placeholder="Class Name">
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
                            <!--  <label for="name">Numeric Value of Class[One=1,Six=6,Ten=10 etc]</label>-->
                              <label for="name">Class <b>*</b></label>
                              <div class="input-group">
                                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                  <!--<input type="number" min="1" max="10" class="form-control" required name="code" placeholder="One=1,Six=6,Ten=10 etc">-->
                                  
                                  <select class="form-control"  name="class" required >
                                  <option value="">---Select Class---</option>
                                   @foreach($class as $cls)
                                     <option value="{{$cls->code }}" @if($cls->code==old('class',$section->class_code)) selected @endif>{{ $cls->name}}</option>
                                     @endforeach
                                  </select>
                              </div>
                         </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                    <!--  <label for="name">Numeric Value of Class[One=1,Six=6,Ten=10 etc]</label>-->
                      <label for="name">Teachers <b>*</b></label>
                      <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                          <!--<input type="number" min="1" max="10" class="form-control" required name="code" placeholder="One=1,Six=6,Ten=10 etc">-->
                          
                          <select class="form-control"  name="teacher_id" required >
                          <option value="">---Select Class---</option>
                           @foreach($teachers as $teacher)
                             <option value="{{$teacher->id }}" @if($teacher->id==old('teacher_id',$section->teacher_id)) selected @endif>{{ $teacher->firstName}} {{$teacher->lastName}}</option>
                             @endforeach
                          </select>
                      </div>
                  </div>
                        </div>

                       
                       <div class="col-md-4">
                       <div class="form-group">
                        <label for="name">Description </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <textarea type="text" class="form-control" required name="description" placeholder="Class Description">{{old('description',$section->description)}}</textarea>
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

              <form role="form" action="{{url('/section/create')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="row">
                     <div class="col-md-12">
                       <div class="col-md-2">
                           <div class="form-group">
                         <label for="for">Section Name  <b>*</b></label>
                         <div class="input-group">
                             <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <input type="text" class="form-control" autofocus required name="name" placeholder="Class Name">
                         </div>
                     </div>
                       </div>
                       <div class="col-md-4">
                           <div class="form-group">
                         <label for="name">Class <b>*</b></label>
                      <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                          <!--<input type="number" min="1" max="10" class="form-control" required name="code" placeholder="One=1,Six=6,Ten=10 etc">-->
                          
                          <select class="form-control" id="mainClass"  name="class" required >
                          <option value="">---Select Class---</option>
                           @foreach($class as $cls)
                             <option value="{{$cls->code }}" @if(old('class')==$cls->code) selected @endif>{{ $cls->name}}</option>
                             @endforeach
                          </select>
                            <a href="#" class="btn btn-info" data-toggle="modal" data-target="#myModal">+</a>
                      </div>
                     </div>
                       </div>

                       <div class="col-md-4">
                         <div class="form-group">
                             <label for="name">Teachers <b>*</b></label>
                      <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                          <!--<input type="number" min="1" max="10" class="form-control" required name="code" placeholder="One=1,Six=6,Ten=10 etc">-->
                          
                          <select class="form-control"  name="teacher_id" id="mainTeacher" required >
                          <option value="">---Select Teacher---</option>
                           @foreach($teachers as $teacher)
                             <option value="{{$teacher->id }}" @if(old('teacher_id')==$teacher->id) selected @endif>{{ $teacher->firstName}} {{$teacher->lastName}}</option>
                             @endforeach
                          </select>
                          <a href="#" class="btn btn-info" data-toggle="modal" data-target="#teacherModaladd">+</a>

                      </div>
                         </div>
                       </div>
                       <div class="col-md-4">
                        <div class="form-group">
                        <label for="name">Description </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <textarea type="text" class="form-control"  name="description" placeholder="Class Description">{{old('description')}}</textarea>
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
              <th style="width:30%">Name</th>
               <th style="width:30%">Class</th>
              <th style="width:30%">Description</th>
              <th style="width:30%">Students</th>
              <th style="width:30%">Teacher</th>
             
              <th style="width:15%">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($sections as $section)
            
              <?php 
                $classinfo = gclass_name($section->class_code);
                /*echo "<pre>";print_r($classinfo); 
                 if (isset($classinfo->name)) {
                    echo $classinfo->name;
                } */
              ?>
             
            <tr>
              <td>{{$section->name}}</td>
              <td>@if(isset($classinfo->name)) {{ $classinfo->name }} @endif</td>
              <td>{{$section->description}}</td>
              <td>{{count_student($section->id,$section->class_code)}}</td>
              {{--<td>{{$section->students}}</td>--}}
              <td><a href="#" onclick="getteacherinfo('{{$section->teacher_id}}')">{{$section->firstName}} {{$section->lastName}}</a></td>

              <td>
                <a title='Edit' class='btn btn-info' href='{{url("/section/edit")}}/{{$section->id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>&nbsp&nbsp
                <a title='Delete' class='btn btn-danger' onclick="confirmed('{{$section->id}}')" href='#' > <i class="glyphicon glyphicon-trash icon-white"></i></a>&nbsp&nbsp
                <a title='view timetable' class='btn btn-success' href='{{url("/section/view-timetable")}}/{{$section->id}}'> <i class="glyphicon glyphicon-eye-open icon-white"></i></a>
              </td>
              </tr>
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
<!----------------- -->
<!-- The Modal -->
<div class="modal" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add Class</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        {{--<form role="form" action="{{url('/class/create')}}" method="post">--}}
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="form-group">
                        <label for="name">Name<b>*</b></label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <input type="text" class="form-control" autofocus required name="name" id="cl_name" value="{{old('name')}}" placeholder="Class Name">
                        </div>
                    </div>
                  <div class="form-group">
                      <label for="name">Numeric Value of Class[play=-2,nusery=-1,parp=0,One=1,Six=6,Ten=10 etc]<b>*</b></label>
                     <!-- <label for="name">Level</label>-->
                      <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                          <input type="number" min="-2" max="15" class="form-control" required name="code" id="cl_code" value="{{old('code')}}" placeholder="One=1,Six=6,Ten=10 etc">
                          
                         <?php /* <select class="form-control"  name="code" required >
                          <option value="">---Select Level---</option>
                           @foreach($levels as $level)
                             <option value="{{$level->name }}">{{ $level->name}}</option>
                             @endforeach
                          </select>  */ ?>


                      </div>
                  </div>
                  
                    <div class="form-group">
                        <label for="name">Description</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <textarea type="text" class="form-control" name="description" id="cl_des" placeholder="Class Description">{{old('description')}}</textarea>
                        </div>
                    </div>
                    <div class="clearfix"></div>
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
                                <div class="form-group">
                    <button class="btn btn-primary pull-right" onclick="saveclass();"type="button"><i class="glyphicon glyphicon-plus"></i>Add</button>
                    <br>
                  </div>
                {{--</form>--}}
      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<!-- The Modal -->
<div class="modal" id="teacherModaladd">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Add Teacher</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
       {{--<form role="form" action="{{url('/teacher/create')}}" method="post" enctype="multipart/form-data">
          --}}
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
          <div class="row">
            <div class="col-md-12">
              <h3 class="text-info"> Teacher Detail</h3>
              <hr>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="fname">Full Name<b>*</b></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <input type="text" class="form-control" required name="fname" id="full_name" placeholder="First Name" value="{{old('fname')}}">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="gender">Gender</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="gender" class="form-control" id="gd"  >

                      <option value="Male" @if(old('gender')=="Male") selected @endif>Male</option>
                      <option value="Female" @if(old('gender')=="Female") selected @endif>Female</option>
                      <option value="Other" @if(old('gender')=="Other") selected @endif>Other</option>
                    </select>
                  </div>
                </div>
              </div>
              {{--<div class="col-md-6">
              <div class="form-group ">
                <label for="dob">Date Of Birth</label>
                <div class="input-group">

                  <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                  <input type="text"   class="form-control datepicker" name="dob" id="dob" value="{{old('dob')}}"   data-date-format="dd/mm/yyyy">
                </div>


              </div>
            </div>--}}
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-12">


            <div class="col-md-6">
              <div class="form-group">
                <label for="extraActivity">Phone<b>*</b> </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text"  class="form-control" required  name="phne" id="phone" value="{{old('phne')}}" placeholder="Enter Phone NO">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="remarks">Email </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="email" class="form-control"  name="emails" id="email" value="{{old('emails')}}" placeholder="Enter Email">
                </div>
              </div>
            </div>
          </div>
        </div>

           
        <div class="clearfix"></div>

        <div class="form-group">
          <button class="btn btn-primary pull-right" type="button" onclick="saveteacher()"><i class="glyphicon glyphicon-plus"></i>Add</button>
          <br>
        </div>
      {{--</form>--}}

      </div>

      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

<!----------------- -->

  @stop
  @section('script')
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

  <script type="text/javascript">

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

function confirmed(section_id)
{
  //alert(family_id);
  //return confirm('Are you sure you want to generate family vouchar?');
  var x = confirm('Are you sure you want to delete this section');
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
  text: "If you delete this section students marks and timetable of this section disturb",
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

      window.location = "{{url('/section/delete')}}/"+section_id;
                              
    });
  } else if (
    // Read more about handling dismissals
    result.dismiss === Swal.DismissReason.cancel
  ) {
    swalWithBootstrapButtons.fire(
      'Cancelled',
      'Section Not Deleted :)',
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


function saveclass()
  {
    var class_name = $("#cl_name").val();
    var class_code = $("#cl_code").val();
    var class_des = $("#cl_des").val();
  
    if(class_name != '' && class_code!= '' )
    {
     var _token = $('input[name="_token"]').val();
     $.ajax({
      url:"{{ url('/ajaxcreate/create') }}",
      method:"POST",
      data:{name:class_name,code:class_code,description:class_des, _token:_token},
      success:function(data){
       // alert(JSON.stringify(data));

        if(data.message=="success"){
          Swal.fire(
                    'Class Created',
                    'You clicked the button!',
                    'success'
                  ).then(function() {
                    //location.reload();
                  //$("#myModald"+billId+ ".close").click();
                  //$('body').removeClass('modal-open');
                  //$('.modal-backdrop').remove();
                  $("#myModal .close").click()
                });
          $("#mainClass").html(data.classlist);

        }

        //$("#tbl").show();
        //$("#btnshow").show();
       //$('#studentListd').fadeIn();  
       //$('#lists').html(data);
       //$('#lists').append(data);
      },

            error: function (textStatus, errorThrown) {
                //callbackfn("Error getting the data");
                alert(JSON.stringify(textStatus));
            }
     });
    }else{
       //$('#studentListd').fadeOut(); 
       alert('please fill all required field');
    }
  } 
  function saveteacher()
  {
    var full_name = $("#full_name").val();
    var gd = $("#gd").val();
    var dob = '';
    var phone = $("#phone").val();
    var email = '';
  
    if( full_name!='' && phone!= ''  )
    {
     var _token = $('input[name="_token"]').val();
     $.ajax({
      url:"{{ url('/teacher/ajaxcreate') }}",
      method:"POST",
      data:{fname:full_name,gender:gd,dob:dob,phne:phone,emails:email, _token:_token},
      success:function(data){
       // alert(JSON.stringify(data));

        if(data.message=="success"){
          Swal.fire(
                    'Class Created',
                    'You clicked the button!',
                    'success'
                  ).then(function() {
                    //location.reload();
                  //$("#myModald"+billId+ ".close").click();
                  //$('body').removeClass('modal-open');
                  //$('.modal-backdrop').remove();
                  $("#teacherModaladd .close").click()
                });
          $("#mainTeacher").html(data.teacherList);

        }

        //$("#tbl").show();
        //$("#btnshow").show();
       //$('#studentListd').fadeIn();  
       //$('#lists').html(data);
       //$('#lists').append(data);
      },

            error: function (textStatus, errorThrown) {
                //callbackfn("Error getting the data");
                alert(JSON.stringify(textStatus));
            }
     });
    }else{
       //$('#studentListd').fadeOut(); 
       alert('please fill all required field');
    }
  }
  </script>
  @stop
