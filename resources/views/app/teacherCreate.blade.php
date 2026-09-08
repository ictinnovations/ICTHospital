@extends('layouts.master')
@section('style')
<link href="{{url('/css/bootstrap-datepicker.css')}}" rel="stylesheet">
<style type="text/css">
  
 b {color:red}
</style>

@stop
@section('content')
@if (Session::get('success'))
<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
  <strong>Process Success.</strong> {{ Session::get('success')}}<br><a href="/teacher/list">View List</a><br>

</div>
@endif
<div class="row">
  <div class="box col-md-12">
    <div class="box-inner">
      <div data-original-title="" class="box-header well">
        <h2><i class="glyphicon glyphicon-user"></i> Teacher Add</h2>

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








        <form role="form" action="{{url('/teacher/create')}}" method="post" enctype="multipart/form-data">
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
                    <input type="text" class="form-control" required name="fname" placeholder="First Name" value="{{old('fname')}}">
                  </div>
                </div>
              </div>
              {{--<div class="col-md-6">
                <div class="form-group">
                  <label for="lname">Last Name<b>*</b></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <input type="text" class="form-control" required name="lname" placeholder="Last Name" value="{{old('lname')}}">
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
                    <select name="gender" class="form-control"  >

                      <option value="Male" @if(old('gender')=="Male") selected @endif>Male</option>
                      <option value="Female" @if(old('gender')=="Female") selected @endif>Female</option>
                      <option value="Other" @if(old('gender')=="Other") selected @endif>Other</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
              <div class="form-group ">
                <label for="dob">Date Of Birth</label>
                <div class="input-group">

                  <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                  <input type="text"   class="form-control datepicker" name="dob" value="{{old('dob')}}"   data-date-format="dd/mm/yyyy">
                </div>


              </div>
            </div>


              
              

          </div>
        </div>
        
        <div class="row">
          <div class="col-md-12">


            <div class="col-md-6">
              <div class="form-group">
                <label for="extraActivity">Phone<b>*</b> </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text"  class="form-control" required  name="phne" value="{{old('phne')}}" placeholder="Enter Phone NO">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="remarks">Email </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="email" class="form-control"  name="emails" value="{{old('emails')}}" placeholder="Enter Email">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            

            

            <div class="col-md-12">
              <div class="form-group ">
                <label for="photo">Photo</label>
                <input id="photo" name="photo"  type="file">
              </div>
            </div>

          </div>
        </div>



        <div class="row">
          <div class="col-md-12">
            <div class="col-md-6">
              <div class="form-group">
                <label for="presentAddress">Address</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker blue"></i></span>
                  <textarea type="text" class="form-control"  name="presentAddress" placeholder="Address">{{old('presentAddress')}}</textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
       
        <div class="row">
            <div class="col-md-12">
              <h3 class="text-info"> Other Details</h3>
              <hr>
            </div>
      <div class="row">
        <div class="col-md-12">

        <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="religion">Religion</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="religion" class="form-control"  >
                      <option value="Islam" selected >Islam</option>
                      <option value="Hindu" @if(old('religion')=="Hindu") selected @endif>Hindu</option>
                      <option value="Cristian" @if(old('religion')=="Cristian") selected @endif>Cristian</option>
                      <option value="Buddhist" @if(old('religion')=="Buddhist") selected @endif>Buddhist</option>
                      <option value="Other" @if(old('religion')=="Other") selected @endif>Other</option>
                    </select>
                  </div>
                </div>
              </div>
            <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="bloodgroup">Bloodgroup</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="bloodgroup" class="form-control"  >
                    <option value=''>--- Select Bloodgroup---</option>
                      <option value="A+" @if(old('bloodgroup')=="A+") selected @endif>A+</option>
                      <option value="A-" @if(old('bloodgroup')=="A-") selected @endif>A-</option>
                      <option value="B+" @if(old('bloodgroup')=="B+") selected @endif>B+</option>
                      <option value="B-" @if(old('bloodgroup')=="B-") selected @endif>B-</option>
                      <option value="AB+" @if(old('bloodgroup')=="B-") selected @endif>AB+</option>
                      <option value="AB-" @if(old('bloodgroup')=="AB-") selected @endif>AB-</option>
                      <option value="O+" @if(old('bloodgroup')=="O+") selected @endif>O+</option>
                      <option value="O-" @if(old('bloodgroup')=="O-") selected @endif>O-</option>
                    </select>

                  
                </div>
              </div>
            </div>
           

            <div class="col-md-4">
              <div class="form-group">
                <label for="nationality">Nationality</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control" value="{{old('nationality','Pakistani')}}"   name="nationality" placeholder="Nationality">
                </div>
              </div>
            </div>
        </div>
      </div>

        <div class="clearfix"></div>

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
@section('script')
<!--<script>
// Add MOre fields script
/*$(document).ready(function(){
    
    $('#add').click(function(){
        
        var inp = $('#box');
        
        var i = $('input').size() + 1;
        
        $('<div id="box' + i +'"><input type="text" id="name" class="name" name="name' + i +'" placeholder="Input '+i+'"/><i class="glyphicon glyphicon-minus add"  id="remove"></i> </div>').appendTo(inp);
        
        i++;
        
    });
    
    
    
    $('body').on('click','#remove',function(){
        
        $(this).parent('div').remove();

        
    });

        
});*/

</script>-->
<script src="{{url('/js/bootstrap-datepicker.js')}}"></script>
<script type="text/javascript">
 /*var getStdRegiRollNo = function(){
   var aclass = $('#class').val();
   var session = $('#session').val().trim();
   var section=$('#section').val().trim();
   $.ajax({
     url: '/student/getRegi/'+aclass+'/'+session+'/'+section,
     data: {
       format: 'json'
     },
     error: function(error) {
       alert(error);
     },
     dataType: 'json',
     success: function(data) {
       $('#regiNo').val(data[0]);
       $('#rollNo').val(data[1]);
     },
     type: 'GET'
   });
 };*/
$( document ).ready(function() {
  //getStdRegiRollNo();
  $('.datepicker').datepicker({autoclose:true});
  $(".datepicker2").datepicker( {
    format: " yyyy", // Notice the Extra space at the beginning
    viewMode: "years",
    minViewMode: "years",
    autoclose:true
  }).on('changeDate', function (ev) {
    getStdRegiRollNo();
  });
  /*$('#class').on('change',function() {
    getStdRegiRollNo();
  });
  $('#section').on('change',function() {
    getStdRegiRollNo();
  });*/
});

</script>
@stop
