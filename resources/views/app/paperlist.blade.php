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
  <strong>{{ Session::get('error')}}</strong>

</div>
@endif
@if (isset($error))
<div class="alert alert-warning">
  <button data-dismiss="alert" class="close" type="button">×</button>
  <strong>{{$error['error']}}</strong>

</div>
@endif
<div class="row">
  <div class="box col-md-12">
    <div class="box-inner">
      <div data-original-title="" class="box-header well">
        <h2><i class="glyphicon glyphicon-book"></i> Questions List</h2>

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

        <form role="form" action="{{url('/question/list')}}" method="post"  enctype="multipart/form-data">
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


               <div class="col-md-4">
                  <div class="form-group ">
                    <label for="session">session</label>
                    <div class="input-group">

                      <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                      <input type="text" id="session" value="{{date('Y')}}" required="true" class="form-control datepicker2" name="session" value=""   data-date-format="yyyy">
                    </div>
                  </div>
                </div>
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
             
            </div>
            </div>
            <div class="row">
              <div class="col-md-12">
               
                
                <div class="col-md-4">
                  <div class="form-group">
                    <label class="control-label" for="exam">Chaper</label>

                    <div class="input-group">
                      <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                      
                      <select   name="chapter[]" id="chapter" class="form-control selectpicker" multiple data-actions-box="true" data-hide-disabled="true" data-size="5"  required="true">
                      </select>

                    </div>
                  </div>
                </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="">Levels</label>
                        <select name="level[]" class="form-control selectpicker" multiple data-actions-box="true" data-hide-disabled="true" data-size="5" required>
                          <option value="">---Select a Level---</option>
                          <option value="simple">Simple</option>
                          <option value="normal">Normal</option>
                          <option value="hard">Hard</option>
                        </select>
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
          @if($questions )
          <div class="row">
            <div class="col-md-12" style="clear: both;margin-top: 18px;" >
              <table id="studentList" class="table table-striped table-bordered" >
                <thead>
                <tr>
                  <th>Class</th>
                  <th>Subject</th>
                  <th>Question</th>
                  <th>Chapter</th>
                  <th>Type</th>
                  <th>Level</th>
                  <th>Number</th>
                  <th>Session</th>

                  <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($questions as $question)
                  <tr>
                    <td>{{$question->class_code}}</td>
                    <td>{{$question->name}}</td>
                    <td>{{$question->question_name}}</td>
                    <td>{{$question->chapter}}</td>
                    <td>@if($question->question_type==1) Long @elseif($question->question_type==2) MCQ @else Short @endif</td>
                    <td>{{$question->level}}</td>
                    <td>{{$question->points}}</td>
                    <td>{{$question->session}}</td>

                    <td>
                    <?php 

                    
                    ?>
                    <a title='View' class='btn btn-success' href='{{url("/question/edit/$question->id")}}'> <i class="glyphicon glyphicon-pencil icon-white"></i></a>&nbsp&nbsp
                   <a title='Delete' class='btn btn-danger' href='{{url("/question/delete/$question->id")}}' onclick="return confirm('Are you sure you want to delete ?');"> <i class="glyphicon glyphicon-trash icon-white"></i></a>
                    {{--&nbsp&nbsp <a title='View' class='btn btn-success' href=''> <i class="glyphicon glyphicon-phone"></i></a>--}}
                    <?php /*&nbsp&nbsp <a title='View' class='btn btn-success' href='{{url("/fee/collections?class_id=$student->class_code&section=$student->section_id&session=$student->session&type=Monthly&month=$month&fee_name=$fee_name")}}'> <i class="glyphicon glyphicon-phone"></i></a>
                    */ ?>
                    </td>
                  </tr>
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
     $( document ).ready(function() {
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
 
      $(".datepicker2").datepicker( {
        format: " yyyy", // Notice the Extra space at the beginning
        viewMode: "years",
        minViewMode: "years",
        autoclose:true

      });
      $('#markList').dataTable();
      $('#class').on('change', function (e) {
        getSubjects();
        getchapter();
       // getexam();
        getsections();
        
      });
      $('#section').on('change', function (e) {
          //getSubjects();
          //getsections();
          //getexam();
      });
      $('#subject').on('change', function (e) {
          //getSubjects();
          //getsections();
          //alert(43);
          //getexam();
          getchapter();
      });
          getSubjects();
          // getexam();
          getsections();
         

        $('#session').on('change',function() {
        //  getexam();
          getsections();
          
        });
         //getexam();
    });
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

            $('#subject').append($('<option>').text(subject.name).attr('value', subject.id));
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
function getchapter()
{
     var aclass = $('#class').val();
     var subject = $('#subject').val();

     //alert(section);
    $.ajax({
      url: "{{url('/chapter/getList')}}"+'/'+aclass+'?subject='+subject,
      data: {
        format: 'json'
      },
      error: function(error) {
        alert("Please fill all inputs correctly!");
      },
      dataType: 'json',
      success: function(data) {
       $('#chapter').empty();
       $('#chapter').append($('<option>').text("--Select Exam--").attr('value',""));
       var options = [];
       $.each(data, function(i, exam) {
          //console.log(student);
         
          
            var opt="<option value='"+exam.chapter+"'>"+exam.chapter + " </option>"

        
          //console.log(opt);
          //$('#chapter').append(opt);
           options.push(opt);

        });
        //console.log(data);
       $("#chapter").html(options).selectpicker('refresh');

      },
      type: 'GET'
    });
};
   
    </script>
    @stop
