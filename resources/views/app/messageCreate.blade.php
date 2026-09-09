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
                <h2><i class="glyphicon glyphicon-th"></i> Send Notification </h2>
            </div>
            <div class="box-content">
                <ul class="nav nav-tabs" id="myTab">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#email">Voice</a></li>
                    <li class="nav-item" ><a class="nav-link" data-toggle="tab" href="#sms">SMS</a></li>
                </ul>

                <div id="myTabContent" class="tab-content">
                    <div class="tab-pane active" id="email">
                        <form role="form" action="{{url('/message')}}" method="post"  enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="type" value="voice">
                                <br >
                                <div class="form-group col-md-12 row">
                                    <label for="name"  class="col-sm-2 col-form-label">Message type</label>
                                    <div class="input-group col-md-6">
                                       Quick Message <input type="radio" name='stpye' value="quick" >
                                       Campaign  <input type="radio" name='stpye' value="campaign" checked>
                                    </div>
                                </div>
                                <div class="form-group col-md-12 row">
                                    <label for="name"  class="col-sm-2 col-form-label">Role</label>
                                    <div class="input-group col-md-6">
                                        <select name="role" id="role" class="form-control" >
                                             <option value="">Select Users Type</option>
                                             <option value="patient">Patient</option>
                                             <option value="doctor">Doctor</option>
                                             
                                             <option value="all_patient">All Patients</option>
                                             <option value="testing">Testing</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group col-md-12 row" id="class" >
                                    <label for="name"  class="col-sm-2 col-form-label">Section</label>
                                    <div class="input-group col-md-6">
                                        <!--<select  name="section[]" id="section" class="form-control selectpicker" multiple="" data-hide-disabled="true" data-actions-box="true" data-size="5" tabindex="-99">-->
                                        <select  name="section[]" id="section" class="form-control selectpicker" multiple="" data-hide-disabled="true" data-actions-box="true" data-size="5" tabindex="-99" >
                                             <option value="">Select Sections</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                               <div class="form-group col-md-12 row" id="testing" >
                                <label for="name"  class="col-sm-2 col-form-label">Phone Numbers</label>
                                <div class="input-group col-md-6">
                                    <input  name="phone_number" placeholder="example:923001234567"  class="form-control">

                                </div>

                            </div>

                              <div class="form-group col-md-12 row" id="class" >
                                <label for="name"  class="col-sm-2 col-form-label">Message Title</label>
                                <div class="input-group col-md-6">
                                    <input  name="mess_name" required class="">

                                </div>

                            </div>

                           

                                <div class="form-group col-md-12 row">
                                    <label for="name" class="col-sm-2 col-form-label">Message</label>
                                    <div class="input-group col-md-6">

                                     <select  name="message" id="message" class="form-control"  data-hide-disabled="true" data-actions-box="true" data-size="5" tabindex="-99">
                                             
                                            <option value="">Select Message</option>
                                            <option value="other">New Upload</option>
                                    @foreach($messages as $message)
                                        <option value="{{$message->id}}">{{$message->name}}</option>
                                    @endforeach
                                    </select>                                   
                                 </div>
                                </div>

                                
                               <div class="form-group col-md-12 row" id="upload" >
                                <label for="message_file"  class="col-sm-2 col-form-label">Uplad Voice Message <small>Only wav file suported</small></label>
                                <div class="input-group col-md-6">
                                    <input type="file"  id="message_file" name="message_file"  class="form-control">

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
                                        <button class="btn btn-primary pull-right" type="submit"><i class="glyphicon glyphicon-send"></i>Send</button>
                                        <br>
                                    </div>
                         </form>
                    </div>
                    <div class="tab-pane" id="sms">
                       <form role="form" action="{{url('/message')}}" method="post">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                 <input type="hidden" name="type" value="sms">
                                <br >
                                <div class="form-group col-md-12 row">
                                    <label for="name"  class="col-sm-2 col-form-label">Message type</label>
                                    <div class="input-group col-md-6">
                                       Quick Message <input type="radio" name='stpye' value="quick" >
                                       Campaign  <input type="radio" name='stpye' value="campaign" checked>
                                    </div>
                                </div>
                                <div class="form-group col-md-12 row">
                                    <label for="name"  class="col-sm-2 col-form-label">Role</label>
                                    <div class="input-group col-md-6">
                                        <select name="role" id="role1" class="form-control" tabindex="-1">
                                            <option value="">Select Users Type</option>
                                            <option value="patient">Patient</option>
                                            <option value="doctor">Doctor</option>
                                             <option value="all_patient">All Patients</option>
                                             <option value="testing">Testing</option>
                                        </select>
                                    </div>
                                </div>
                                    <div class="form-group col-md-12 row" id="class" >
                                        <label for="name"  class="col-sm-2 col-form-label">Section</label>
                                        <div class="input-group col-md-6">
                                            <select  name="section[]" id="section1" class="form-control selectpicker" multiple="" data-hide-disabled="true" data-actions-box="true" data-size="5" tabindex="-99">
                                                 <option value="">Select Sections</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                              <div class="form-group col-md-12 row" id="testing1" >
                                <label for="name"  class="col-sm-2 col-form-label">Phone Numbers</label>
                                <div class="input-group col-md-6">
                                    <input  name="phone_number" placeholder="example:923001234567"  class="form-control">

                                </div>

                            </div>

                              <div class="form-group col-md-12 row" id="class" >
                                <label for="name"  class="col-sm-2 col-form-label">Message Title</label>
                                <div class="input-group col-md-6">
                                    <input  name="mess_name" required class="">
                                </div>
                            </div>



                                <div class="form-group col-md-12 row">
                                    <label for="name" class="col-sm-2 col-form-label">Message</label>
                                    <div class="input-group col-md-6">

                                     <textarea class="from-control" id="textarea" name="message" style="width: 684px; height: 209px;"></textarea>   
                                   <!--  <div id="textarea_feedback"></div>  -->                       
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
                                        <button class="btn btn-primary pull-right" type="submit"><i class="glyphicon glyphicon-send"></i>Send Sms</button>
                                        <br>
                                    </div>
                         </form>
                    </div>

                </div>
            </div>
        </div>

</div>
</div>
<script>

$(document).ready(function()
{


  $('.selectpicker').selectpicker({
    style: 'btn-default',
    size: 4
});

   $("#upload").hide();
    $("#testing").hide();
    $("#testing1").hide();

    $("#role").change(function()
    {
        var id=$(this).val();
        //var dataString = 'id='+ id;
       // alert(id);
         if(id=='teacher'){
            $("#testing").hide();
        }else if(id=='student') {
         $("#testing").hide();
        }else if(id=='all_student') {
          // $("#testing").show();
        }else{
           $("#testing").show();
        }


    });
    $("#message").change(function()
    {
        var id=$(this).val();
        //var dataString = 'id='+ id;
       // alert(id);
         if(id=='other'){
            $("#upload").show();
           
         }else{
          $("#upload").hide();
        }


    });

    $("#role1").change(function()
    {
        var id=$(this).val();
        //var dataString = 'id='+ id;
       // alert(id);
         if(id=='teacher'){
            $("#testing1").hide();
        }else if(id=='student') {
         $("#testing1").hide();
         }else if(id=='all_student') {
          // $("#testing").show();
        }else{
           $("#testing1").show();
        }


    });

    var text_max = 99;
$('#textarea_feedback').html(text_max + ' characters remaining');

$('#textarea').keyup(function() {
    var text_length = $('#textarea').val().length;
    var text_remaining = text_length;

    $('#textarea_feedback').html(text_remaining + ' characters remaining');
});
});


</script>
@stop

