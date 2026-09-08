@extends('layouts.master')

@section('style')
@stop
@section('content')
<div class="row">
  <div class="box col-md-8">
    <div class="box-inner">
      <div data-original-title="" class="box-header well">
        <h2><i class="glyphicon glyphicon-book"></i> Student Information</h2>
      </div>
      <div class="box-content">
        @if (Session::get('error'))
         <div class="alert alert-danger">
            <ul>
            <button data-dismiss="alert" class="close" type="button">×</button>
              <strong> {{ Session::get('error')}}</strong>
            </ul>
          </div>
        @endif


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
        @if (Session::get('success'))
          <div class="alert alert-success">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <strong>Process Success.</strong><br>{{ Session::get('success')}}<br>
          </div>
        @endif
        @if (isset($student))
          <div class="row">
            <div class="col-md-8">
              <h1 class="text-center">Student's Information</h1>
            </div>
             <div class="col-md-4">
              <a href="{{url('/student/edit')}}/{{$student->id}}" title="edit"><h3 class="text-center"><i class="glyphicon glyphicon-pencil"></i></h3></a>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h4>Academic Details :</h4>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-4">
                
              </div>
              <div class="col-md-4">
                <img class="img responsive-img" style="height:150px;width:200px;" src="{{url('/public/images/'.$student->photo)}}" alt="Photo">
              </div>
              <div class="col-md-4">

              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
            <div class="col-md-3">
             <strong class="text-info font-16" >Registration No :</strong>
            </div>
            <div class="col-md-7">
              <label>{{$student->regiNo}}</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
                <strong class="text-info font-16" >Card/Roll No :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->rollNo}}</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
               <strong class="text-info font-16" >Sift :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->shift}}</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
                <div class="col-md-3">
                  <strong class="text-info font-16" >Session :</strong>
                </div>
              <div class="col-md-7">
                <label>{{$student->session}} </label>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
                <strong class="text-info font-16" >Group :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->group}} </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
                <div class="col-md-2"></div>
              <div class="col-md-3">
               <strong class="text-info font-16" >Class :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->class}} </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
               <strong class="text-info font-16" >Section :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->section_name}} </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h4>Basic Details :</h4>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
                  <strong class="text-info font-16" >Fulle Name :</strong>
              </div>
                <div class="col-md-7">
                 <label>{{$student->firstName}} {{$student->middleName}} {{$student->lastName}}</label>
                </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
                <strong class="text-info font-16" >Gender :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->gender}} </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
                <div class="col-md-3">
                  <strong class="text-info font-16" >Religion :</strong>
                </div>
                <div class="col-md-7">
                  <label>{{$student->religion}} </label>
                </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
                <strong class="text-info font-16" >Bloodgroup :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->bloodgroup}} </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
                <strong class="text-info font-16" >Nationality :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->nationality}} </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
               <strong class="text-info font-16" >Date Of Birth :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->dob}} </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
                <strong class="text-info font-16" >Extra Curicular Activity :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->extraActivity}} </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
               <strong class="text-info font-16" >Remarks :</strong>
              </div>
              <div class="col-md-7">
               <label>{{$student->remarks}} </label>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <h4>Guardian's Details :</h4>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
                <strong class="text-info font-16" >Father's Name :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->fatherName}} </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
                <strong class="text-info font-16" >Father's Cell No :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->fatherCellNo}} </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
                <strong class="text-info font-16" >Mother's Name :</strong>
              </div>
              <div class="col-md-7">
               <label>{{$student->motherName}} </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
                <strong class="text-info font-16" >Mother's Cell No :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->motherCellNo}} </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
                <strong class="text-info font-16" >Local Guardian :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->localGuardian}} </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
                <strong class="text-info font-16" >Local Guardian Cell No :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->localGuardianCell}} </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <h4>Address Details:</h4>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
                <strong class="text-info font-16" >Present Address :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->presentAddress}} </label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-2"></div>
              <div class="col-md-3">
                <strong class="text-info font-16" >Parmanent Address :</strong>
              </div>
              <div class="col-md-7">
                <label>{{$student->parmanentAddress}} </label>
              </div>
            </div>
          </div>


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

    <div class="box col-md-4">
      <div class="box-inner">
          <div data-original-title="" class="box-header well">
            <h2><i class="glyphicon glyphicon-book"></i>Student's Details </h2>
          </div>
          <div class="box-content">
            <div class="row">
              <div class="col-md-12">
                <h1 class="text-center">Student's Details</h1>
              </div>
            </div>
            <br>
            <div class="row">
              <div class="col-md-12">
                <h4>Today Status: @if($attendances) {{$attendances->status}} @else Attendance pending @endif</h4>
              </div>

            </div>
            <br>
            <div class="row">
              <div class="col-md-12">
                <a class="btn btn-danger btn-lg btn-block" href='{{url("/teacher/view-timetable/student?class=$student->class_code&section=$student->section")}}'><h4>Time Tables</h4></a>
              </div>
            </div>
            {{--<div class="row">
              <div class="col-md-12">
                <a class="btn btn-secondary btn-lg btn-block" href='{{url("/fee/collection?class_id=$student->class_code&section=$student->section&session=$student->session_id&type=Monthly&month=$month&fee_name=$fee_name&regiNo=$student->regiNo")}}'><h4>Add Fees</h4></a>
              </div>
            </div>--}}
            {{--<div class="row">
              <div class="col-md-12">
                <a class="btn btn-primary btn-lg btn-block" href='{{url("/fee/detail?class=$student->class_code&section=$student->section&regiNo=$student->regiNo")}}'><h4>Fees Detail</h4></a>
              </div>
            </div>--}}
            {{--@if(accounting_check()!='' && accounting_check()=='yes' )
              --}}
              <div class="row">
                <div class="col-md-12">
                  <a class="btn btn-primary btn-lg btn-block" href='{{url("/fee/vouchar?class=$student->class_code&section=$student->section&regiNo=$student->regiNo")}}'><h4>Create Fee Vouchar</h4></a>
                 
                  <a class="btn btn-success btn-lg btn-block" href='{{url("/fee/get_vouchar?class=$student->class_code&section=$student->section&session=$student->session&type=Monthly&month=$month&fee_name=$fee_name&regiNo=$student->regiNo")}}'><h4>Get Vouchar</h4></a>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <a class="btn btn-secondary btn-lg btn-block" href='{{url("/fee/vouchar/history?class=$student->class_code&section=$student->section&regiNo=$student->regiNo")}}'><h4>Fee Vouchar History</h4></a>
                </div>
              </div>
            {{--@endif--}}
            <div class="row">
              <div class="col-md-12">
                <a class="btn btn-warning btn-lg btn-block" href='{{url("attendance/monthly-report?_token=csrf_token()&search=yes&student_name=&print_view=1&class=$student->class_code&section=$student->section&shift=Morning&session=$student->session_id&yearMonth=$year-$month&type=count&regiNo=$student->regiNo")}}'><h4>Attendance Detail</h4></a>
              </div>
            </div>
          </div>
        </div>

        <div class="box-inner">
          <div data-original-title="" class="box-header well">
            <h2><i class="glyphicon glyphicon-book"></i>Send Sms </h2>
          </div>
          <div class="box-content">
            <div class="row">
              <div class="col-md-12">
              
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="input-group col-md-6">
                  <form role="form" action="{{url('/sms/send')}}" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="phone" value="{{ $student->fatherCellNo }}">
                    <input type="hidden" name="id" value="{{ $student->id }}">
                    <div class="form-group">
                      <label for="message">
                        Message [<span id="typing" class="text-info">160 characters remaining</span> ]
                      </label>
                      <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                        <textarea type="text" id="message" class="form-control" rows="5" maxlength="160" required name="message" placeholder="Message 160 letters" style="width: 366px; height: 212px;"></textarea>
                      </div>
                    </div>
                      <button class="btn btn-primary btn-lg " type="submit" style="margin-left: 75%;"><i class="glyphicon glyphicon-envelope"></i> Send</button>
                  </form>
                </div>
              </div>
            </div>
              </div>
            </div>
      </div>
  </div>
  </div>
</div>
@stop
@section('script')
<script type="text/javascript">
  $(function(){
    $('#profiletabs ul li a').on('click', function(e){
      e.preventDefault();
      var newcontent = $(this).attr('href');
      $('#profiletabs ul li a').removeClass('sel');
      $(this).addClass('sel');
      $('#content section').each(function(){
        if(!$(this).hasClass('hidden')) { $(this).addClass('hidden'); }
      });
      $(newcontent).removeClass('hidden');
    });
  });
  $( document ).ready(function() {
      var text_max = 160;
      $('#typing').html(text_max + ' characters remaining');
      $('#message').keyup(function() {
          var text_length    = $('#message').val().length;
          var text_remaining = text_max - text_length;
          if(text_remaining>0){
            $('#typing').removeClass();
            $('#typing').addClass('text-info');
          }
          else{
            $('#typing').removeClass();
            $('#typing').addClass('text-danger');
          }
          $('#typing').html(text_remaining + ' characters remaining');
      });
      $('#type').on('change',function(e) {
          var optionValue = $('#type').val()
          if( optionValue != "Custom"){
            var url = "/sms-type-info/"+ optionValue;
            $.getJSON(url, function(result){
              $('#sender').val(result['sender']);
              $('#message').val(result['message']);
            });
          } else {
            $('#sender').val('');
            $('#message').val('');
          }
      });
  });

</script>
@stop
