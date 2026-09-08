@extends('layouts.master')
@section('content')
   <link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css" />
@if (Session::get('success'))

<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
    <strong>Process Success.</strong> {{ Session::get('success')}}<br><br>

</div>
@endif
<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-th"></i>Notification Setting</h2>

            </div>
             <div class="box-content">
                @if($notification_types)
                        <form role="form" action="{{url('/notification_type')}}" method="post"  enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <br >
                                <div class="row">
                                <div class="col-md-12">
                                <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name"  class=" col-form-label">Attendance</label>
                                    <div class="input-group col-md-6">
                                       sms <input type="radio" name="attendance" @if(!empty($notification_types) && $notification_types[0]->type =='sms') checked @endif value="sms"  >
                                       voice <input type="radio" name="attendance" @if(!empty($notification_types) && $notification_types[0]->type=='voice') checked @endif value="voice" >
                                    </div>
                                    <label for="name"  class=" col-form-label">Schedule Setting</label>
                                    <div class="input-group col-md-6">
                                        <input type="text" name="time" value="{{$attendance_time}}" id="timepicker">
                                    </div>
                                </div>
                                </div>
                                 
                                  <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name"  class=" col-form-label">Fees</label>
                                    <div class="input-group col-md-6">
                                       sms <input type="radio" name="fess" @if( !empty($notification_types) && $notification_types[1]->type=='sms') checked @endif required value="sms"  >
                                       voice <input type="radio" name="fess" @if(!empty($notification_types) && $notification_types[1]->type=='voice') checked @endif required value="voice"  >
                                    </div>
                                    <!-------------------------------------->
                                     
                                <div class="form-group">
                                    <label for="name"  class=" col-form-label">Date</label>
                                    <div class="input-group col-md-6">
                                       <input type="text" name="date" class="form-control" required value="{{$schedule->date}}" >
                                    </div>
                                </div>
                            
                                <div class="form-group">
                                    <label for="name" class="col-form-label">Time</label>
                                    <div class="input-group col-md-6">

                                        <input type="text" id="timepicker1" class="form-control"  name="time_set" value="{{$schedule->time}}" >
                                    </div>
                                </div>
                               

                                
                                 <div class="form-group">
                                    <label for="name" class=" col-form-label">Month</label>
                                    <div class="input-group col-md-6">

                                        <input type="text" class="form-control"  value="{{$datee}}" readonly >
                                    </div>
                                </div>
                                
                         
                                <div class="form-group">
                                    <label for="name" class="col-form-label">Year</label>
                                    <div class="input-group col-md-6">

                                        <input type="text" class="form-control" value="{{$year}}"  readonly>
                                    </div>
                              
                                </div>

                                  <!---------------------------------->
                                </div>
                                </div>

                                <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name"  class=" col-form-label">Diary</label>
                                    <div class="input-group col-md-6">
                                    </div>
                                    <label for="name"  class=" col-form-label">Schedule Setting</label>
                                    <div class="input-group col-md-6">
                                        <input type="text" name="diary_time" value="{{$diary_time}}" id="timepicker2">
                                    </div>
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
                                        <button class="btn btn-primary pull-right" type="submit"><i class="glyphicon glyphicon-plus"></i>Save</button>
                                        <br>
                                    </div>
                         </form>
                    @else
                      <form role="form" action="{{url('/notification_type')}}" method="post"  enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <br >
                                <div class="row">
                                <div class="col-md-12">
                                <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name"  class=" col-form-label">Attendance</label>
                                    <div class="input-group col-md-6">
                                       sms <input type="radio" name="attendance" value="sms"  >
                                       voice <input type="radio" name="attendance"  value="voice" >
                                    
                                    </div>
                                     <label for="name"  class=" col-form-label">Schedule Setting</label>
                                    <div class="input-group col-md-6">
                                        <input type="text" name="time" value="{{$attendance_time}}" id="timepicker">
                                    </div>
                                </div>
                                </div>
                                 
                                  <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name"  class=" col-form-label">Fees</label>
                                    <div class="input-group col-md-6">
                                       sms <input type="radio" name="fess" required value="sms"  >
                                       voice <input type="radio" name="fess" required value="voice"  >
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label for="name"  class=" col-form-label">Date</label>
                                    <div class="input-group col-md-6">
                                       <input type="text" name="date" class="form-control" required value="{{$schedule->date}}" >
                                    </div>
                                </div>
                            
                                <div class="form-group">
                                    <label for="name" class="col-form-label">Time</label>
                                    <div class="input-group col-md-6">

                                        <input type="text" id="timepicker1" class="form-control"  name="time_set" value="{{$schedule->time}}" >
                                    </div>
                                </div>
                               

                                
                                 <div class="form-group">
                                    <label for="name" class=" col-form-label">Month</label>
                                    <div class="input-group col-md-6">

                                        <input type="text" class="form-control"  value="{{$datee}}" readonly >
                                    </div>
                                </div>
                                
                         
                                <div class="form-group">
                                    <label for="name" class="col-form-label">Year</label>
                                    <div class="input-group col-md-6">

                                        <input type="text" class="form-control" value="{{$year}}"  readonly>
                                    </div>
                              
                                </div>
                                </div>
                                 <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name"  class=" col-form-label">Diary</label>
                                    <div class="input-group col-md-6">
                                    </div>
                                    <label for="name"  class=" col-form-label">Schedule Setting</label>
                                    <div class="input-group col-md-6">
                                        <input type="text" name="diary_time" value="" id="timepicker2">
                                    </div>
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
                                        <button class="btn btn-primary pull-right" type="submit"><i class="glyphicon glyphicon-plus"></i>Save</button>
                                        <br>
                                    </div>
                         </form>
                    
                    @endif

           
        </div>
@stop
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
<script>
$( document ).ready(function() {
   //$('#timepicker1').timepicker();
    $('#timepicker').timepicker({
        timeFormat: 'HH:mm:ss',
    });

            $('#timepicker1').timepicker();
            @if($diary_time=='')
              $('#timepicker2').timepicker().val('');
            @else
              $('#timepicker2').timepicker();
            @endif
            /*$('#timepicker2').timepicker({
              timeFormat: 'HH:mm:ss',
            });*/
    
});
</script>
@stop
