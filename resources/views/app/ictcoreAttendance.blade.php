@extends('layouts.master')
@section('content')
@if (Session::get('success'))

<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
    <strong>Process Success.</strong> {{ Session::get('success')}}><br>

</div>
@endif
<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-th"></i>Attendance Messages</h2>

            </div>
             <div class="box-content">
                
                        <form role="form" action="{{url('/ictcore/attendance')}}" method="post"  enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <br >
                                <!--<div class="form-group col-md-12 row">
                                    <label for="name"  class="col-sm-2 col-form-label">Title</label>
                                    <div class="input-group col-md-6">
                                       <input type="text" name="title" class="form-control" required value="{{$ictcore_attendance->name}}" >
                                    </div>
                                </div>-->
                              
                                <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Absent Notification</label>
                                     <input type="hidden" name="title_abent" value="absent" class="form-control" required value="{{$ictcore_attendance->name}}" >
                                    <div class="input-group col-md-6">

                                        <textarea type="text" class="form-control"  name="description_absent" placeholder="Class Description">{{$ictcore_attendance->description}}</textarea>
                                    </div>
                                </div>


                                 <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Upload Absent Student Message File</label>
                                    <div class="input-group col-md-6">

                                        <input type="file" class="form-control"  name="message_absent" placeholder="">
                                        @if($ictcore_attendance->recording !='')
                                          <input type="hidden" class="form-control"  name="message_absent1" value="{{ $ictcore_attendance->recording  }}" placeholder="">

                                       <div style="line-height:72px;">
                                        <audio controls>
                                          <source src=" {{url('/storage/app/public/messages/')}}/{{ $ictcore_attendance->recording}}" type="audio/wav">
                                            Your browser does not support the audio element.
                                        </audio>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Late Notification</label>
                                     <input type="hidden" name="title_late" value="late" class="form-control" required value="{{$ictcore_attendance->name}}" >
                                    <div class="input-group col-md-6">

                                        <textarea type="text" class="form-control"  name="description_late" placeholder="Class Description">{{$ictcore_attendance->late_description}}</textarea>
                                    </div>
                                </div>


                                 <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Upload Late Student Message File</label>
                                    <div class="input-group col-md-6">

                                        <input type="file" class="form-control"  name="message_late" placeholder="">
                                        @if($ictcore_attendance->late_file !='')
                                              <input type="hidden" class="form-control"  name="message_late1" value="{{ $ictcore_attendance->late_file  }}" placeholder="">

                                       <div style="line-height:72px;">
                                        <audio controls>
                                          <source src=" {{url('/storage/app/public/messages/')}}/{{$ictcore_attendance->recording}}" type="audio/wav">
                                            Your browser does not support the audio element.
                                        </audio>
                                        </div>
                                        @endif
                                    </div>
                                </div>


                                
                              
                                </div>

                                <div class="box-inner">
                                      <div data-original-title="" class="box-header well">
                                      <h2><i class="glyphicon glyphicon-th"></i>Fee Message</h2>

                                      </div>
                                      <div class="box-content">
                                        <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Description</label>
                                    <div class="input-group col-md-6">
                                      <input type="hidden" name="fee_title" value="fee_notification" class="form-control" required value="{{$ictcore_attendance->name}}" >

                                        <textarea type="text" class="form-control"  name="fee_description" placeholder="Class Description">{{$ictcore_fees->description}}</textarea>
                                    </div>
                                </div>


                                 <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Upload Message File</label>
                                    <div class="input-group col-md-6">

                                        <input type="file" class="form-control"  name="fee_message" placeholder="">
                                        @if($ictcore_fees->recording !='')
                                      <input type="hidden" class="form-control"  name="fee_message1" value="{{ $ictcore_fees->recording }}" placeholder="">
                                       <div style="line-height:72px;">
                                        <audio controls>
                                          <source src=" {{url('/storage/app/public/messages/')}}/{{$ictcore_fees->recording}}" type="audio/wav">
                                            Your browser does not support the audio element.
                                        </audio>
                                        </div>
                                        @endif
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
                                       </div>
                            </div>

                                   
                         </form>
                    
           
        </div>

@stop
