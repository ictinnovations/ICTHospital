@extends('layouts.master')
@section('style')
<link href="/css/bootstrap-datepicker.css" rel="stylesheet">

@stop
@section('content')
@if (Session::get('success'))
<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
  <strong>Process Success.</strong> {{ Session::get('success')}}<br><a href="/attendance/list">View List</a><br>

</div>
@endif
<div class="row">
  <div class="box col-md-12">
    <div class="box-inner">
      <div data-original-title="" class="box-header well">
        <h2><i class="glyphicon glyphicon-user"></i> Attendance Entry</h2>

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
        <form role="form" action="{{url('/attendance/create')}}" method="post" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <div class="row">
            <div class="col-md-12">



              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="class">Class</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-home blue"></i></span>
                    <select id="class" id="class" name="class" class="form-control" >
                      @foreach($classes2 as $class)
                      <option value="{{$class->code}}">{{$class->name}}</option>
                      @endforeach

                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="section">Section</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select id="section" name="section"  id="section" class="form-control" >
                      <option value="1">A</option>
                     

                    </select>


                  </div>
                </div>
              </div>

             <!-- <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="shift">Shift</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select id="shift" name="shift"  class="form-control" >
                      <option value="Day">Day</option>
                      <option value="Morning">Morning</option>
                    </select>

                  </div>
                </div>
              </div>-->


            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="col-md-4">
                <div class="form-group ">
                  <label for="session">session</label>
                  <div class="input-group">

                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                    <input type="text" value="{{date('Y')}}" id="session" required="true" class="form-control datepicker2" name="session"   data-date-format="yyyy">
                  </div>
                </div>
              </div>


              <div class="col-md-4">
                <div class="form-group ">
                  <label for="dob">Date</label>
                  <div class="input-group">

                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                    <input type="text" value="{{date('d-m-Y')}}"  class="form-control datepicker" id="datepicker" name="date" required  data-date-format="dd-mm-yyyy">
                  </div>


                </div>
              </div>
            </div>
          </div>


          <div class="row">
            <div class="col-md-12">
              <div class="green"><h4>Send Voice On Absent? <input type="checkbox" id="isSendvoice" name="isSendSMS"></h4></div>
            </div>
          </div>

            <?php /*<div class="col-md-4" id="divtemp" style="display:none">
                <div class="form-group">
                  <label class="control-label" for="message">Select Message</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select id="message" name="message"  class="form-control" >
                      <option value="">  Select Template </option>
                       @foreach($messages as $message)
                       <option value="{{$message->id}}">{{$message->name}}</option>
                       @endforeach

                    </select>


                  </div>
                </div>
              </div>
              */?>
          <div class="row">
            <div class="col-md-12">
              <div class="table-responsive">
                <table id="studentList" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>

                      <th>Registration No</th>
                      <th>Roll No</th>
                      <th>Name</th>

                      <th>Is Present? <input type="checkbox" id="allcheck" name="allcheck">All Select</th>

                       

                    </tr>
                  </thead>
                  <tbody>


                    <tbody>
                    </table>
                  </div>
                </div>

              </div>

              <!--button save -->
              <div class="row">
                <div class="col-md-12">

                  <button class="btn btn-primary pull-right" id="btnsave" type="submit"><i class="glyphicon glyphicon-plus"></i>Save</button>
                </div>
              </div>
            </form>

          
      @stop
      @section('script')
      <script src="/js/bootstrap-datepicker.js"></script>
        <script type="text/javascript">
         
       /* $(document).ready(function () {
         
        //Uncheck the CheckBox initially
        $('#isSendvoice').change(function () {
         
        if (this.checked) {
         
         
        $('#divtemp').show();
         
        }
         
        else {
        
         
        $('#divtemp').hide();
         
        }
         
        });
         
        });*/
         
        </script>
      <script type="text/javascript">
      var getStudent = function () {
        var aclass = $('#class').val();
        var section =  $('#section').val();
    //    var shift = $('#shift').val();
        var shift = 'Morning';

        var session = $('#session').val().trim();
        $.ajax({
          url: "{{url('/student/getList/')}}"+'/'+aclass+'/'+section+'/'+shift+'/'+session,
          data: {
            format: 'json'
          },
          error: function(error) {
            alert("Please fill all inputs correctly!");
          },
          dataType: 'json',
          success: function(data) {

            $("#studentList").find("tr:gt(0)").remove();
            if(data.length>0)
            {
              $('#btnsave').show();
            }
            for(var i =0;i < data.length;i++)
            {
              addRow(data[i],i);
            }

          },
          type: 'GET'
        });
      };
      $( document ).ready(function() {
       document.getElementById("datepicker").setAttribute("onkeyDown", "return false");
      getsections();
  $('#class').on('change',function() {
    getsections();
  });

        getStudent();
        $('#btnsave').hide();

        /*$("#datepicker").datepicker({
         // constrainInput: true,
         // autoclose:true,
          //todayHighlight: true,
          //hideIfNoPrevNext: true ,
          maxDate: new Date()
        });
        
        /* $("#datepicker").datepicker({
          //dateFormat: 'yy-mm-dd ',
          maxDate:'0'
        });*/


        var today = new Date();
        $('.datepicker').datepicker({
            //format: 'mm-dd-yyyy',
            autoclose:true,
            todayHighlight: true,
            endDate: "today",
            maxDate: today
        }).on('changeDate', function (ev) {
                $(this).datepicker('hide');
            });


        $('.datepicker').keyup(function () {
            if (this.value.match(/[^0-9]/g)) {
                this.value = this.value.replace(/[^0-9^-]/g, '');
            }
        });
        $(".datepicker2").datepicker( {
          format: " yyyy", // Notice the Extra space at the beginning
          viewMode: "years",
          minViewMode: "years",
          autoclose:true

        }).on('changeDate', function (ev) {
          getStudent();
        });
        $('#class').change(function() {
          getStudent();
        });
        $('#section').change(function() {
          getStudent();
        });
        $('#shift').change(function() {
          getStudent();
        });
         $('#session').on('change',function() {
          getsections();
        });
        $('#allcheck').change(function() {
          var isS=false;
          if ($('#isSendSMS').is(":checked"))
          {
            isS=true;
          }
          $('input:checkbox').not(this).prop('checked', this.checked);

          if(isS)
          {
            $('#isSendSMS').prop('checked',true);
          }
          else
          {

            $('#isSendSMS').prop('checked',false);
          }


        });

          /*$('#allcheckl').change(function() {
          var isS=false;
          if ($('#isSendSMS').is(":checked"))
          {
            isS=true;
          }
          $('input:checkbox').not(this).prop('checked', this.checked);

          if(isS)
          {
            $('#isSendSMS').prop('checked',true);
          }
          else
          {

            $('#isSendSMS').prop('checked',false);
          }


        });*/

      });



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
        alert("Please fill all inputs correctly!");
      },
      dataType: 'json',
      success: function(data) {
        $('#section').empty();
       //$('#section').append($('<option>').text("--Select Section--").attr('value',""));
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
      function addRow(data,index) {
        var table = document.getElementById('studentList');
        var rowCount = table.rows.length;
        var row = table.insertRow(rowCount);
        // var cell1 = row.insertCell(0);
        //  var chkbox = document.createElement("label");
        // chkbox.type = "checkbox";
        //chkbox.name="chkbox[]";
        // cell1.appendChild(chkbox);

        var cell2 = row.insertCell(0);
        var regiNo = document.createElement("label");

        regiNo.innerHTML=data['regiNo'];
        cell2.appendChild(regiNo);
        var hdregi = document.createElement("input");
        hdregi.name="regiNo[]";
        hdregi.value=data['regiNo'];
        hdregi.type="hidden";
        cell2.appendChild(hdregi);


        var cell3 = row.insertCell(1);
        var rollno = document.createElement("label");
        rollno.innerHTML=data['rollNo'];
        cell3.appendChild(rollno);
        /*   var hdroll = document.createElement("input");
        hdroll.name="rollNo[]";
        hdroll.value=data['rollNo'];
        hdroll.type="hidden";
        cell3.appendChild(hdroll);*/



        var cell4 = row.insertCell(2);
        var name = document.createElement("label");
        name.innerHTML=data['firstName']+' '+data['middleName']+' '+data['lastName'];
        cell4.appendChild(name);



        var cell5 = row.insertCell(3);
        var chkbox = document.createElement("input");
        chkbox.type = "checkbox";
        chkbox.checked=false;
        chkbox.name="present["+data['regiNo']+"]";
        chkbox.size="3";
        cell5.appendChild(chkbox);

      };

      </script>

      @stop
