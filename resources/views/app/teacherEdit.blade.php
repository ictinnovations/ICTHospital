@extends('layouts.master')
@section('style')
<link href="/css/bootstrap-datepicker.css" rel="stylesheet">
<style type="text/css">
  
 b {color:red}
</style>
@stop
@section('content')

<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-user"></i> Edit Teacher</h2>

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
              @if (isset($teacher))
              <form role="form" action="{{url('/teacher/update')}}" method="post" enctype="multipart/form-data">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="id" value="{{ $teacher->id }}">
                  <input type="hidden" name="oldphoto" value="{{ $teacher->photo }}">
               
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
                          <label for="fname">Full Name <b>*</b></label>
                          <div class="input-group">
                              <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                              <input type="text" class="form-control" value="{{$teacher->firstName}}" required name="fname" placeholder="First Name">
                          </div>
                      </div>
                    </div>
                    
                    {{--<div class="col-md-6">
                      <div class="form-group">
                          <label for="lname">Last Name</label>
                          <div class="input-group">
                              <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                              <input type="text" class="form-control" value="{{$teacher->lastName}}" required name="lname" placeholder="Last Name">
                          </div>
                      </div>
                      </div>--}}

                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="col-md-6">
                        <div class="form-group">
                        <label class="control-label" for="gender">Gender</label>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <?php  $data=[
                              'Male'=>'Male',
                              'Female'=>'Female',
                                'Other'=>'Other'

                              ];?>
                              {{ Form::select('gender',$data,$teacher->gender,['class'=>'form-control'])}}

                        </div>
                      </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group ">
                                             <label for="dob">Date Of Birth</label>
                                                 <div class="input-group">

                                                  <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                                                    <input type="text" value="{{$teacher->dob}}"  class="form-control datepicker" name="dob"   data-date-format="dd/mm/yyyy">
                                                </div>


                                         </div>
                            </div>

                        
                      

                      </div>
                    </div>
                    <div class="row">
                    <div class="col-md-12">


                      <div class="col-md-6">
                        <div class="form-group">
                            <label for="extraActivity">Phone <b>*</b> </label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                <input type="text" class="form-control" value="{{$teacher->phone}}"  name="phone" placeholder="Enter phone No">
                            </div>
                        </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                              <label for="remarks">Email </label>
                              <div class="input-group">
                                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                  <input type="text" class="form-control"  value="{{$teacher->email}}"   name="email" placeholder="Email">
                              </div>
                          </div>
                          </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-12">
                        

                          
                            <div class="col-md-4">
                              <div class="form-group ">
                              <label for="photo">Photo</label>
                              <input id="photo" name="photo"  type="file">
                              </div>
                            </div>
                            <div class="col-md-6">
                        <div class="form-group">
                            <label for="presentAddress">Address</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker blue"></i></span>
                                <textarea type="text" class="form-control"  name="presentAddress" placeholder="Address">{{$teacher->presentAddress}}</textarea>
                            </div>
                        </div>
                        </div>

                    </div>
                  </div>
                  


                      
               
              <div class="row">
                <div class="col-md-12">
                    <h3 class="text-info"> Other Detail</h3>
                    <hr>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                          
                      <div class="col-md-4">
                          <div class="form-group">
                          <label class="control-label" for="religion">Religion</label>

                          <div class="input-group">
                              <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                              <?php  $data=[
                                'Islam'=>'Islam',
                                'Hindu'=>'Hindu',
                                'Cristian'=>'Cristian',
                                'Buddhist'=>'Buddhist',
                                  'Other'=>'Other'

                                ];?>
                                {{ Form::select('religion',$data,$teacher->religion,['class'=>'form-control'])}}

                          </div>
                        </div>
                          </div>

                          <div class="col-md-4">
                        <div class="form-group">
                        <label class="control-label" for="bloodgroup">Bloodgroup</label>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <?php  $data=[
                             '' =>'--- Select Bloodgroup---',
                              'A+'=>'A+',
                              'A-'=>'A-',
                              'B+'=>'B+',
                              'B+'=>'B+',
                              'AB+'=>'AB+',
                              'AB-'=>'AB-',
                                  'O+'=>'O+',
                                    'O-'=>'O-',

                              ];?>
                              {{ Form::select('bloodgroup',$data,$teacher->bloodgroup,['class'=>'form-control'])}}

                        </div>
                      </div>
                        </div>

                        <div class="col-md-4">
                          <div class="form-group">
                              <label for="nationality">Nationality</label>
                              <div class="input-group">
                                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                  <input type="text" class="form-control" value="{{$teacher->nationality}}"   name="nationality" placeholder="Nationality">
                              </div>
                          </div>
                        </div>  
              </div>
            </div>


                    <div class="clearfix"></div>

                                <div class="form-group">
                    <button class="btn btn-primary pull-right" type="submit"><i class="glyphicon glyphicon-check"></i>Update</button>
                    <br>
                  </div>
                </form>
              @else
                      <div class="alert alert-danger">
                          <strong>Whoops!</strong>There is no such Student!<br><br>
                          <ul>
                              @foreach ($errors->all() as $error)
                                  <li>{{ $error }}</li>
                              @endforeach
                          </ul>
                      </div>
             @endif






        </div>
    </div>
</div>
</div>
@stop
@section('script')
<script src="/js/bootstrap-datepicker.js"></script>
<script type="text/javascript">

    $( document ).ready(function() {

      $('.datepicker').datepicker({autoclose:true});
      $(".datepicker2").datepicker( {
    format: " yyyy", // Notice the Extra space at the beginning
    viewMode: "years",
    minViewMode: "years",
    autoclose:true
});


    });


</script>
@stop
