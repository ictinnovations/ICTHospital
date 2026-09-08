@extends('layouts.master')
@section('content')
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
                <h2><i class="glyphicon glyphicon-th"></i>Fee Notification Reminder</h2>

            </div>
             <div class="box-content">
                
                        <form role="form" action="{{url('/schedule')}}" method="post"  enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <br >
                                <div class="row">
                                <div class="col-md-12">
                                <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name"  class=" col-form-label">Date</label>
                                    <div class="input-group col-md-6">
                                       <input type="text" name="date" class="form-control" required value="{{$schedule->date}}" >
                                    </div>
                                </div>
                                </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="col-form-label">Time</label>
                                    <div class="input-group col-md-6">

                                        <input type="text" id="timepicker1" class="form-control"  name="time" value="{{$schedule->time}}" >
                                    </div>
                                </div>
                                </div>

                                <div class="col-md-3">
                                 <div class="form-group">
                                    <label for="name" class=" col-form-label">Month</label>
                                    <div class="input-group col-md-6">

                                        <input type="text" class="form-control"  value="{{$datee}}" readonly >
                                    </div>
                                </div>
                                </div>
                             <div class="col-md-3">
                                <div class="form-group">
                                    <label for="name" class="col-form-label">Year</label>
                                    <div class="input-group col-md-6">

                                        <input type="text" class="form-control" value="{{$year}}"  readonly>
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
                    

           
        </div>
@stop
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
<script>
$( document ).ready(function() {
   //$('#timepicker1').timepicker();
    $('#timepicker1').timepicker({
        timeFormat: 'HH:mm:ss',
    });
});
</script>
@stop
