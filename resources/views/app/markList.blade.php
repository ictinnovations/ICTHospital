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
@if (Session::get('noresult'))
<div class="alert alert-warning">
  <button data-dismiss="alert" class="close" type="button">×</button>
  <strong>{{ Session::get('noresult')}}</strong>

</div>
@endif
@if (isset($noResult))
<div class="alert alert-warning">
  <button data-dismiss="alert" class="close" type="button">×</button>
  <strong>{{$noResult['noresult']}}</strong>

</div>
@endif
<div class="row">
  <div class="box col-md-12">
    <div class="box-inner">
      <div data-original-title="" class="box-header well">
        <h2><i class="glyphicon glyphicon-book"></i> Marks List</h2>

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

        <form role="form" action="{{url('/mark/list')}}" method="post" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <div class="row">
            <div class="col-md-12">

              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="class">Class</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-home blue"></i></span>
                    @if(isset($classes2))
                    {{ Form::select('class',$classes2,$formdata->class,['class'=>'form-control','id'=>'class','required'=>'true'])}}
                    @else


                    <select id="class" id="class" name="class" required="true" class="form-control" >
                      @foreach($classes as $class)
                      <option value="{{$class->code}}">{{$class->name}}</option>
                      @endforeach

                    </select>
                    @endif                                 </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label" for="section">Section</label>

                    <div class="input-group">
                      <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                      <?php  $data=[
                        'A'=>'A',
                        
                      ];?>
                      {{ Form::select('section',$data,$formdata->section,['class'=>'form-control','id'=>'section','required'=>'true'])}}


                    </div>
                  </div>
                </div>

              <?php /*  <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label" for="shift">Shift</label>

                    <div class="input-group">
                      <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                      <?php  $data=[
                        'Day'=>'Day',
                        'Morning'=>'Morning'
                      ];?>
                      {{ Form::select('shift',$data,$formdata->shift,['class'=>'form-control','required'=>'true'])}}


                    </div>
                  </div>
                </div>

              </div> */ ?>

               <input type="hidden" value="Morning" name="shift">

               {{--<div class="col-md-4">
                  <div class="form-group ">
                    <label for="session">session</label>
                    <div class="input-group">

                      <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                      <input type="text" id="session" value="{{date('Y')}}" required="true" class="form-control datepicker2" name="session" value="{{$formdata->session}}"   data-date-format="yyyy">
                    </div>
                  </div>
                </div>--}}
                <input type="hidden" id="session"  class="form-control " name="session" value="{{get_current_session()->id}}"   data-date-format="yyyy">

             
            </div>
            <div class="row">
              <div class="col-md-12">
               
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label" for="subject">subject</label>

                    <div class="input-group">
                      <span class="input-group-addon"><i class="glyphicon glyphicon-book blue"></i></span>
                      @if(isset($subjects))
                      {{ Form::select('subject',$subjects,$formdata->subject,['class'=>'form-control','id'=>'subject','required'=>'true'])}}
                      @else
                      <select id="subject" id="subject" name="subject" required="true" class="form-control" >
                        <option value="">--Select Subjects--</option>

                      </select>
                      @endif
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label" for="exam">Examination</label>

                    <div class="input-group">
                      <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                      <?php  $data=[
                        ''=>'--Select Exam--',
                       
                      ];?>
                      {{ Form::select('exam',$data,$formdata->exam,['class'=>'form-control','id'=>'exam','required'=>'true'])}}


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
          </form>
          @if($marks)
          <div class="row">
            <div class="col-md-12">
              <table id="markList" class="table table-striped table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Regi No</th>
                    <th>Roll No</th>
                    <th>Name</th>
                    <th>Written</th>
                    <th>MCQ</th>
                    <th>Practical</th>
                    <th>SBA</th>
                    <th>Total</th>
                    <th>Grade</th>
                    <th>Point</th>
                    <th>Is Absent</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($marks as $mark)
                  <tr>
                    <td>{{$mark->regiNo}}</td>
                    <td>{{$mark->rollNo}}</td>
                    <td>{{$mark->firstName}} {{$mark->middleName}} {{$mark->lastName}}</td>
                    <td>{{$mark->written}}</td>
                    <td>{{$mark->mcq}}</td>
                    <td>{{$mark->practical}}</td>
                    <td>{{$mark->ca}}</td>
                    <td>{{$mark->total}}</td>

                    <td>{{$mark->grade}}</td>
                    <td>{{$mark->point}}</td>
                    <td>{{$mark->Absent}}</td>
                    <td>
                      <a title='Edit' class='btn btn-info' href='{{url("/mark/edit")}}/{{$mark->id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>
                    </td>
                    @endforeach
                  </tbody>
                </table>
              </div>

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
    var getSubjects = function () {
      var val = $('#class').val();

       // alert(val);
      $.ajax({
        url:"{{url('/class/getsubjects')}}"+'/'+val,
        type:'get',
        dataType: 'json',
        success: function( json ) {


          $('#subject').empty();
          $('#subject').append($('<option>').text("--Select Subject--").attr('value',""));
          $.each(json, function(i, subject) {
             console.log(subject);

            $('#subject').append($('<option>').text(subject.name).attr('value', subject.code));
          });
        }
      });
    };

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
        //alert("Please fill all inputs correctly!");
      },
      dataType: 'json',
      success: function(data) {
        $('#section').empty();
      // $('#section').append($('<option>').text("--Select Section--").attr('value',""));
        $.each(data, function(i, section) {
          //console.log(student);
         
          
            //var opt="<option value='"+section.id+"'>"+section.name + " </option>"
            var opt="<option value='"+section.id+"'>"+section.name +' (  ' + section.students +' ) '+ "</option>"

        
          //console.log(opt);
          $('#section').append(opt);

        });
        //console.log(data);

      },
      type: 'GET'
    });
};
 function getexam()
{
    var aclass = $('#class').val();
   // alert(aclass);
    $.ajax({
      url: "{{url('/exam/getList')}}"+'/'+aclass,
      data: {
        format: 'json'
      },
      error: function(error) {
        alert("Please fill all inputs correctly!");
      },
      dataType: 'json',
      success: function(data) {
        $('#exam').empty();
       $('#exam').append($('<option>').text("--Select Exam--").attr('value',""));
        $.each(data, function(i, exam) {
          //console.log(student);
         
          
            var opt="<option value='"+exam.id+"'>"+exam.type + " </option>"

        
          //console.log(opt);
          $('#exam').append(opt);

        });
        //console.log(data);

      },
      type: 'GET'
    });
};
    $( document ).ready(function() {
      
 
      $(".datepicker2").datepicker( {
        format: " yyyy", // Notice the Extra space at the beginning
        viewMode: "years",
        minViewMode: "years",
        autoclose:true

      });
      $('#markList').dataTable({
          "sPaginationType": "bootstrap",
      });
      $('#class').on('change', function (e) {
        getSubjects();
        getsections();
        getexam();
      });
      getSubjects();
       getsections();
        getexam();

         $('#session').on('change',function() {
          getsections();
        });
    });
    </script>
    @stop
