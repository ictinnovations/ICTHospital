@extends('layouts.master')
@section('style')
    <link href="/css/bootstrap-datepicker.css" rel="stylesheet">
      <link href="/css/timetable.css" rel="stylesheet">
<style>
b {color:red}
</style>
@stop
@section('content')
    @if (Session::get('success'))
        <div class="alert alert-success">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <strong>Process Success.</strong> {{ Session::get('success')}}<br><a href="{{url('/teacher/list')}}">View List</a><br>

        </div>
    @endif
    <?php 
    //echo "<pre>";print_r(Session::all());

    ?>
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
    @if($timetable)
     <form role="form" action="{{url('/timetable/update')}}" method="post" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="tid" value="{{ $timetable->id }}">
        
          <div class="row">
            <div class="col-md-12">
              <h3 class="text-info"> Time Table</h3>
              <hr>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="fname">Teacher <b>*</b></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="teacher" class="form-control" required>
                     <option value="">---Select Teacher---</option>
                    @foreach($teachers as $teacher)
                      <option value="{{$teacher->id}}" @if($timetable->teacher_id == $teacher->id) selected @endif>{{$teacher->firstName}} {{$teacher->lastName}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="gender">Class <b>*</b></label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="class" id="class" class="form-control" required >

                      @foreach($classes as $class)
                      <option value="{{$class->code}}" @if($timetable->class_id==$class->code) selected @endif>{{$class->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="section">Section <b>*</b></label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="section" id="section1" class="form-control" required >
                     
                       @foreach($sections as $section)
                      <option value="{{$section->id}}" @if($timetable->section_id==$section->id) selected @endif>{{$section->name}}</option>
                      @endforeach 
                    
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="bloodgroup">Subject <b>*</b></label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="subject" id="subject1" class="form-control " required >
                       @foreach($subjects as $subject)
                      <option value="{{$subject->id}}" @if($timetable->subject_id==$subject->id) selected @endif>{{$subject->name}}</option>
                      @endforeach 
                    </select>

                  </select>
                </div>
              </div>
            </div>

          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-4">
              <div class="form-group">
                <label for="nationality">Start Time <b>*</b></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                  <input type="text" id="timepicker1" class="form-control" value="{{$timetable->stattime}}" required  name="startt">
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group ">
                <label for="dob">End Time <b>*</b></label>
                <div class="input-group">

                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i> </span>
                  <input type="text" id="timepicker2"  class="form-control datepicker" name="endt" value="{{$timetable->endtime}}" required  data-date-format="">
                </div>


              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group ">
                <label for="photo">Day <b>*</b></label>
                <select name="day" class="form-control"  data-hide-disabled="true" data-size="5">
                <option value="">---Select Day---</option>
                <option value="monday" @if($timetable->day=="monday") selected @endif>Monday</option>
                <option value="tuesday" @if($timetable->day=="tuesday") selected @endif>Tuesday</option>
                <option value="wednesday" @if($timetable->day=="wednesday") selected @endif>Wednesday</option>
                <option value="thursday" @if($timetable->day=="thursday") selected @endif>Thursday</option>
                <option value="friday" @if($timetable->day=="friday") selected @endif>Friday</option>
                <option value="saturday" @if($timetable->day=="saturday") selected @endif>Saturday</option>
                <option value="sunday" @if($timetable->day=="sunday") selected @endif>Sunday</option>
                </select>
              </div>
            </div>

          </div>
        </div>
        <div class="clearfix"></div>

        <div class="form-group">
          <button class="btn btn-primary pull-right" type="submit"><i class="glyphicon glyphicon-plus"></i>Update</button>
          <br>
        </div>
      </form>
      @else
          <form role="form" action="{{url('/teacher/create_timetable')}}" method="post" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
          <div class="row">
            <div class="col-md-12">
              <h3 class="text-info"> Time Table</h3>
              <hr>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="fname">Teacher <b>*</b></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="teacher" class="form-control" required>
                   <option value="">---Select Teacher---</option>
                   
                    @foreach($teachers as $teacher)
                      <option value="{{$teacher->id}}" @if(old('teacher',$timetable->teacher_id)==$teacher->id) selected @endif>{{$teacher->firstName}} {{$teacher->lastName}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="gender">Class <b>*</b></label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="class" id="class" class="form-control" required >

                      @foreach($classes as $class)
                      <option value="{{$class->code}}" @if(old('class',$timetable->class_id)==$class->code) selected @endif>{{$class->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="section">Section <b>*</b></label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="section" id="section" class="form-control" required >
                      <option value=""></option>
                    @foreach($sections as $section)
                    <option value="{{$section->id}}" @if($section->id==$timetable->section_id) selected @endif>{{$section->name}}</option>
                    @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="bloodgroup">Subject <b>*</b></label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="subject" id="subject" class="form-control " required >
                     <?php /* @foreach($subjects as $subject)
                      <option value="{{$subject->id}}">{{$subject->name}}</option>
                      @endforeach */?>
                      
                    @foreach($subjects as $subject)
                    <option value="{{$subject->id}}" @if($subject->id==$timetable->subject_id) selected @endif>{{$subject->name}}</option>
                    @endforeach
                    </select>

                  </select>
                </div>
              </div>
            </div>

          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="col-md-4">
              <div class="form-group">
                <label for="nationality">Start Time <b>*</b></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>
                  <input type="text" id="timepicker1" class="form-control" value="{{old('startt',$timetable->stattime)}}" required  name="startt">
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group ">
                <label for="dob">End Time <b>*</b></label>
                <div class="input-group">

                  <span class="input-group-addon"><i class="glyphicon glyphicon-time"></i> </span>
                  <input type="text" id="timepicker2"  class="form-control datepicker" name="endt" value="{{old('endt',$timetable->endtime)}}" required  data-date-format="">
                </div>


              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group ">
                <label for="photo">Day <b>*</b></label>
                <select name="day" class="form-control"  data-hide-disabled="true" data-size="5" required>
                
                  <option value="">---Select Day---</option>
                  <option value="monday"    @if(old('day',$timetable->day)=="monday")    selected @endif>Monday</option>
                  <option value="tuesday"   @if(old('day',$timetable->day)=="tuesday")   selected @endif>Tuesday</option>
                  <option value="wednesday" @if(old('day',$timetable->day)=="wednesday") selected @endif>Wednesday</option>
                  <option value="thursday"  @if(old('day',$timetable->day)=="thursday")  selected @endif>Thursday</option>
                  <option value="friday"    @if(old('day',$timetable->day)=="friday")    selected @endif>Friday</option>
                  <option value="saturday"  @if(old('day',$timetable->day)=="saturday")  selected @endif>Saturday</option>
                  <option value="sunday"    @if(old('day',$timetable->day)=="sunday")    selected @endif>Sunday</option>
                  
                </select>
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

      @endif





   
@stop
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
<script>
   $( document ).ready(function() {
 $('#timepicker1').timepicker();
  $('#timepicker2').timepicker();
  $('.selectpicker').selectpicker();

 getsections();
 getsubjects();
  $('#class').on('change',function() {
    getsections();
    getsections1();
    getsubjects();
    getsubjects1();
  });
    });
function getsections()
{
    var aclass = $('#class').val();
   // alert(aclass);
    $.ajax({
      url: "{{url('/section/getList')}}"+'/'+aclass,
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
         
          
            var opt="<option value='"+section.id+"'>"+section.name + " </option>"

        
          //console.log(opt);
          $('#section').append(opt);

        });
        //console.log(data);

      },
      type: 'GET'
    });
};

function getsections1()
{
    var aclass = $('#class').val();
   // alert(aclass);
    $.ajax({
      url: "{{url('/section/getList/')}}"+'/'+aclass,
      data: {
        format: 'json'
      },
      error: function(error) {
        alert("Please fill all inputs correctly!");
      },
      dataType: 'json',
      success: function(data) {
        $('#section1').empty();
       //$('#section').append($('<option>').text("--Select Section--").attr('value',""));
        $.each(data, function(i, section) {
          //console.log(student);
         
          
            var opt="<option value='"+section.id+"'>"+section.name + " </option>"

        
          //console.log(opt);
          $('#section1').append(opt);

        });
        //console.log(data);

      },
      type: 'GET'
    });
};
function getsubjects()
{
    var aclass = $('#class').val();
   // alert(aclass);
    $.ajax({
      url: "{{url('/subject/getList')}}"+'/'+aclass,
      data: {
        format: 'json'
      },
      error: function(error) {
        alert("Please fill all inputs correctly!");
      },
      dataType: 'json',
      success: function(data) {
        $('#subject').empty();
       //$('#section').append($('<option>').text("--Select Section--").attr('value',""));
        $.each(data, function(i, subject) {
          //console.log(student);
         
          
            var opt="<option value='"+subject.id+"'>"+subject.name + " </option>"

        
          //console.log(opt);
          $('#subject').append(opt);

        });
        //console.log(data);

      },
      type: 'GET'
    });
};


function getsubjects1()
{
    var aclass = $('#class').val();
   // alert(aclass);
    $.ajax({
      url: "{{url('/subject/getList')}}"+'/'+aclass,
      data: {
        format: 'json'
      },
      error: function(error) {
        alert("Please fill all inputs correctly!");
      },
      dataType: 'json',
      success: function(data) {
        $('#subject1').empty();
       //$('#section').append($('<option>').text("--Select Section--").attr('value',""));
        $.each(data, function(i, subject) {
          //console.log(student);
         
          
            var opt="<option value='"+subject.id+"'>"+subject.name + " </option>"

        
          //console.log(opt);
          $('#subject1').append(opt);

        });
        //console.log(data);

      },
      type: 'GET'
    });
};
</script>
@stop