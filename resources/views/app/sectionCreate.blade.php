@extends('layouts.master')
@section('style')
<style>
b {color:red}
</style>
@stop
@section('content')
@if (Session::get('success'))

<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
    <strong>Process Success.</strong> {{ Session::get('success')}}<br><a href="/section/list">View List</a><br>

</div>
@endif
<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-home"></i> Section Create</h2>

            </div>
            <div class="box-content">
              <form role="form" action="{{url('/section/create')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="form-group">
                        <label for="name">Section Name <b>*</b></label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <input type="text" class="form-control" autofocus required name="name" value="{{old('name')}}" placeholder="Section Name">
                        </div>
                    </div>
                    
                      <div class="form-group">
                    <!--  <label for="name">Numeric Value of Class[One=1,Six=6,Ten=10 etc]</label>-->
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
                  
                   <div class="form-group">
                    <!--  <label for="name">Numeric Value of Class[One=1,Six=6,Ten=10 etc]</label>-->
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
                          <a href="#" class="btn btn-info" data-toggle="modal" data-target="#teacherModal">+</a>

                      </div>
                  </div>
                 

                    <div class="form-group">
                        <label for="name">Description </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <textarea type="text" class="form-control"  name="description" placeholder="Class Description">{{old('description')}}</textarea>
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
                    <button class="btn btn-primary pull-right" type="submit"><i class="glyphicon glyphicon-plus"></i>Add</button>
                    <br>
                  </div>
                </form>
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
<div class="modal" id="teacherModal">
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
@stop
@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

<script type="text/javascript">
  
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
                  $("#teacherModal .close").click()
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
