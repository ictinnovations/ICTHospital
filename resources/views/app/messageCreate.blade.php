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
                                             <option value="student">Student</option>
                                             <option value="teacher">Teacher</option>
                                             <!--<option value="parent">Parent</option>-->
                                             <option value="all_student">All Student</option>
                                             <option value="testing">Testing</option>
                                        </select>
                                    </div>
                                </div>
                             <div id="studen" >
                                <div class="form-group col-md-12 row" id="class" >
                                    <label for="name"  class="col-sm-2 col-form-label">Class</label>
                                    <div class="input-group col-md-6">
                                        <!--<select  name="class" id="class" class="form-control selectpicker" multiple="" data-hide-disabled="true"  data-actions-box="true" data-size="5" tabindex="-98">-->
                                        <select  name="class" id="classa" class="form-control">

                                            <option value="">Select Classes</option>
                                        @foreach($classes as $class)
                                            <option value="{{$class->code}}">{{$class->name }}</option>
                                        @endforeach
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
                                            <option value="student">Student</option>
                                            <option value="teacher">Teacher</option>
                                             <option value="all_student">All Student</option>
                                             <option value="testing">Testing</option>
                                        </select>
                                    </div>
                                </div>
                                 <div id="studen1" >
                                    <div class="form-group col-md-12 row" id="class" >
                                        <label for="name"  class="col-sm-2 col-form-label">Class</label>
                                        <div class="input-group col-md-6">
                                            <select  name="class" id="class1" class="form-control" >
                                                <option value="">Select Classes</option>
                                              @foreach($classes as $class)
                                                <option value="{{$class->code}}">{{$class->name }}</option>
                                              @endforeach
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
   $("#studen").hide();
    $("#testing").hide();
    $("#testing1").hide();

    $("#role").change(function()
    {
        var id=$(this).val();
        //var dataString = 'id='+ id;
       // alert(id);
         if(id=='teacher'){
            $("#studen").hide();
            $("#testing").hide();
        }else if(id=='student') {
         $("#studen").show();
         $("#testing").hide();
        }else if(id=='all_student') {
          $("#studen").hide();
          // $("#testing").show();
        }else{
           $("#studen").hide();
           $("#testing").show();
            //$("#studen").hide();
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
            //$("#studen").hide();
        }


    });

    $("#role1").change(function()
    {
        var id=$(this).val();
        //var dataString = 'id='+ id;
       // alert(id);
         if(id=='teacher'){
            $("#studen1").hide();
            $("#testing1").hide();
        }else if(id=='student') {
         $("#studen1").show();
         $("#testing1").hide();
         }else if(id=='all_student') {
          $("#studen1").hide();
          // $("#testing").show();
        }else{
           $("#studen1").hide();
           $("#testing1").show();
            //$("#studen").hide();
        }


    });

    var text_max = 99;
$('#textarea_feedback').html(text_max + ' characters remaining');

$('#textarea').keyup(function() {
    var text_length = $('#textarea').val().length;
    var text_remaining = text_length;

    $('#textarea_feedback').html(text_remaining + ' characters remaining');
});

    $('#classa').on('change',function() {
    getsections();
  });
  $('#class1').on('change',function() {
    getsections1();
  });
});

function getsections()
{
    var aclass = $('#classa').val();
    //alert(aclass);
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
       var options = [];
        $.each(data, function(i, section) {
          //console.log(student);
         
          
            var opt ="<option value='"+section.id+"'>"+section.name + " </option>"

        
          //console.log(opt);
       //  var data = $('#section').append(opt);
         
         options.push(opt);
          
        
          //alert(786);

        });
          $("#section").html(options).selectpicker('refresh');
        //console.log(data);

      },
      type: 'GET'
    });
};

function getsections1()
{
    var aclass = $('#class1').val();
    //alert(aclass);
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
        $('#section1').empty();
       //$('#section').append($('<option>').text("--Select Section--").attr('value',""));
       var options1 = [];
        $.each(data, function(i, section) {
          //console.log(student);
         
          
            var opt="<option value='"+section.id+"'>"+section.name + " </option>"
            
            options1.push(opt);

        
          //console.log(opt);
        //  $('#section1').append(opt);

        });
        $("#section1").html(options1).selectpicker('refresh');
        //console.log(data);

      },
      type: 'GET'
    });
};
</script>
@stop

