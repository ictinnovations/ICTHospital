@extends('layouts.master')
@section('style')
<link href="/css/bootstrap-datepicker.css" rel="stylesheet">

@stop
@section('content')
@if (Session::get('success'))
<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
    <strong>Process Success.</strong> {{ Session::get('success')}}<br><a href="/mark/list">View List</a><br>

</div>
@endif
<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-user"></i> Marks Entry</h2>

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
   <form role="form" action="{{url('/mark/m_create')}}" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                  <div class="row">
                  <div class="col-md-12">
                  <div class="col-md-7">
                  <div class="row">
                  <div class="col-md-12">

                    <div class="col-md-5">
                      <div class="form-group">
                      <label class="control-label" for="class">Class</label>

                      <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-home blue"></i></span>
                          <select id="class" name="class" class="form-control" >
                              <option value="">--Select Class--</option>

                          @foreach($classes as $class)
                              <option value="{{$class->code}}" @if($class_code==$class->code) selected @endif>{{$class->name}}</option>
                            @endforeach

                          </select>
                      </div>
                  </div>
                    </div>
                      {{--<div class="col-md-4">
                        <div class="form-group">
                        <label class="control-label" for="section">Section</label>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <select id="section" name="section"  class="form-control" >
                              <option value="">--Select Section--</option>
                              @if($section)
                              @foreach($sections as $sction)
                              <option value="{{$sction->id}}" @if($sction->id==$section) selected @endif>{{$sction->name}}</option>
                               @endforeach
                               @endif
                           </select>


                        </div>
                      </div>
                        </div>

                     <!--   <div class="col-md-3">
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
                          <input type="hidden" name="shift" value="Morning">
                  </div>
                </div>--}}
                {{--<div class="row">
                <div class="col-md-12">
                  <div class="col-md-3">
                    <div class="form-group ">
                     <label for="session">session</label>
                         <div class="input-group">

                          <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                            <input type="text" id="session" required="true" class="form-control datepicker2" name="session" value="{{$session}}"  data-date-format="yyyy">
                        </div>
                    </div>
                    </div>--}}
                    <input type="hidden" id="session" required="true" class="form-control datepicker2" name="session" value="{{$session}}"  data-date-format="yyyy">
                   {{--<div class="col-md-5">
                      <div class="form-group">
                      <label class="control-label" for="subject">subject</label>

                      <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-book blue"></i></span>
                          <select id="subject" name="subject" class="form-control" required="true">
                            <option value="">--Select Subject--</option>
                           


                          </select>
                      </div>
                  </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                      <label class="control-label" for="exam">Examination</label>

                      <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                          <select name="exam" id="exam" required="true" class="form-control" >
                           <option value="">-Select Exam-</option>
                           @if($exams)
                              @foreach($exams as $exm)
                              <option value="{{$exm->id}}" @if($exm->id==$exam) selected @endif>{{$exm->type}}</option>
                               @endforeach
                               @endif
                            	<!--<option value="">-Select Exam-</option>
                            <option value="Class Test">Class Test</option>
                            <option value="Model Test">Model Test</option>
                      			<option value="First Term">First Term</option>
                            	<option value="Mid Term">Mid Term</option>
                      			<option value="Final Exam">Final Exam</option>-->
                         </select>


                      </div>
                    </div>
                      </div>


                      <div class="col-md-4">
                      <div class="form-group">
                      <label class="control-label" for="exam">Total Marks</label>

                      <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                          <input name="total_marks" id="total_marks" required="true" class="form-control" >
                           

                      </div>
                    </div>
                      </div>--}}

                </div>
              </div>
            </div>
            <?php /* ?><div class="col-md-5">
               <div class="row">
                 <div class="col-md-2">

                 </div>
                 <div class="col-md-2">
                   <label>Marks</label>
                 </div>
                 <div class="col-md-2">
                    <label>Written</label>
                 </div>
                 <div class="col-md-2">
                  <label>MCQ</label>
                 </div>
                 <div class="col-md-2">
                    <label>Practical</label>
                 </div>
                 <div class="col-md-2">
                    <label>SBA</label>
                 </div>
               </div>
               <div class="row">
                 <div class="col-md-2">
                 <label>Full Marks</label>
                 </div>
                 <div class="col-md-2">
                   <label id="tfull">0</label>
                 </div>
                 <div class="col-md-2">
                    <label id="wfull">0</label>
                 </div>
                 <div class="col-md-2">
                  <label id="mfull">0</label>
                 </div>
                 <div class="col-md-2">
                    <label id="pfull">0</label>
                 </div>
                 <div class="col-md-2">
                    <label id="cfull">0</label>
                 </div>
               </div>
               <div class="row">
                 <div class="col-md-2">
                 <label>Pass Marks</label>
                 </div>
                 <div class="col-md-2">
                   <label id="tpass">0</label>
                 </div>
                 <div class="col-md-2">
                    <label id="wpass">0</label>
                 </div>
                 <div class="col-md-2">
                  <label id="mpass">0</label>
                 </div>
                 <div class="col-md-2">
                    <label id="ppass">0</label>
                 </div>
                 <div class="col-md-2">
                    <label id="cpass">0</label>
                 </div>
               </div>
            </div> <?php */ ?>
          </div>
        </div>

        <div class="row">
                           <div class="col-md-12">
                               <div class="table-responsive">
                                  <table id="" class="table table-striped table-bordered table-hover">
                                       <thead>
                                       <tr>

                                        
                                           <th>Section</th>
                                           <th>Sujects</th>
                                         
                                       </tr>
                                       </thead>
                                       <tbody id='sectionList'>


                                       </tbody>
                               </table>
                           </div>
                       </div>

        </div>


        <!--button save -->
        <div class="row">
         <div class="col-md-12">
           <button class="btn btn-primary pull-right" id="btnsave" type="submit"><i class="glyphicon glyphicon-plus"></i>Save</button>
             </form>
         </div>
       </div>
    </div>
</div>
</div></div>
@stop
@section('script')
<script src="{{url('/js/bootstrap-datepicker.js')}}"></script>
<script type="text/javascript">
//
    $( document ).ready(function() {
      //$('#class').trigger('change');
      getstudent();
     <?php if($session){ ?>
      getstudent();
      // subject();
      <?php } ?>
     //  getsections();
  $('#class').on('change',function() {
    //alert(434);
    getstudent();
   // getsections();
    //getexam();
    //subject();
  });
  $('#session').on('change',function() {
        //  getstudent();
        });
       $('#btnsave').hide();
       /* $('#class').on('change', function (e) {
           
            });*/

$(".datepicker2").datepicker( {
    format: " yyyy", // Notice the Extra space at the beginning
    viewMode: "years",
    minViewMode: "years",
    autoclose:true

}).on('changeDate', function (ev) {
     /*var aclass = $('#class').val();
     var section =  $('#section').val();
    // var shift = $('#shift').val();

    var shift =  "Morning";
    
     var session = $('#session').val().trim();
     $.ajax({
           url: '/student/getList/'+aclass+'/'+section+'/'+shift+'/'+session,
           data: {
               format: 'json'
           },
           error: function(error) {
              alert(error);
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
       });*/
         getstudent();

        });

        $( "#subject" ).change(function() {
         
          $.ajax({
                url: "{{url('/subject/getmarks')}}"+'/'+$('#subject').val()+'/'+$('#class').val(),
                data: {
                    format: 'json'
                },
                error: function(error) {
                   console.log(error);
                },
                dataType: 'json',
                success: function(data) {
                  $('#tfull').text(data[0]['totalfull']);
                  $('#tpass').text(data[0]['totalpass']);

                  $('#wfull').text(data[0]['wfull']);
                  $('#wpass').text(data[0]['wpass']);

                  $('#mfull').text(data[0]['mfull']);
                  $('#mpass').text(data[0]['mpass']);

                  $('#pfull').text(data[0]['pfull']);
                  $('#ppass').text(data[0]['ppass']);

                  $('#cfull').text(data[0]['sfull']);
                  $('#cpass').text(data[0]['spass']);
                  getstudent();
                },
                type: 'GET'
            });



             });



    });
 function subject()
 {
   var val = $('#class').val();
            $.ajax({
                url:"{{url('/class/getsubjects')}}"+'/'+val,
                type:'get',
                dataType: 'json',
                success: function( json ) {
                    $('#subject').empty();
                    $('#subject').append($('<option>').text("--Select Subject--").attr('value',""));
                    $.each(json, function(i, subject) {
                        // console.log(subject);

                        $('#subject').append($('<option>').text(subject.name).attr('value', subject.code));
                    });
                }
            });
 }
function getstudent()
{


var aclass = $('#class').val();
     var section =  $('#section').val();
    // var shift = $('#shift').val();
    
    var shift =  "Morning";
    
     var session = $('#session').val().trim();
      //alert(session);
     $.ajax({
           //url: "{{url('/student/getsList')}}"+'/'+aclass+'/'+section+'/'+shift+'/'+session,
           url: "{{url('/marks/section')}}"+'/'+aclass,
           data: {
               format: 'json'
           },
           error: function(error) {
              //alert(54);
           },
           
           success: function(data) {
            //alert(45);
             //$("#sectionList").find("tr:gt(0)").remove();
             /*$("#sectionList").find("tr:gt(0)").remove();
             if(data.length>0)
             {
               $('#btnsave').show();
             }
             for(var i =0;i < data.length;i++)
              {
                addRow(data[i],i);
              }*/
              //$("#sectionList").append();
              $("#sectionList").html(data);
           },
           type: 'GET'
       });





}
function getsections()
{
    var aclass = $('#class').val();
     var session = $('#session').val();
     if(session==''){
       session =2018;
     }
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

    function addRow(data,index) {
     var table = document.getElementById('studentList');
     var rowCount = table.rows.length;
     var row = table.insertRow(rowCount);
    // var cell1 = row.insertCell(0);
  //  var chkbox = document.createElement("label");
    // chkbox.type = "checkbox";
     //chkbox.name="chkbox[]";
    // cell1.appendChild(chkbox);
    var tm = $('#tfull').text();

    if(tm ==''){
    tm = 25;
    }
    var wm = $('#wfull').text();
    if(wm ==''){
    wm = 25;
    }
    var mm=$('#mfull').text();
    if(mm ==''){
    mm = 25;
    }
    var pm=$('#pfull').text();
    if(pm ==''){
    pm = 25;
    }
    var cm = $('#cfull').text();
    if(cm ==''){
    cm = 25;
    }
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
     var written = document.createElement("input");
     written.type="number";

     written.name = "written[]";
     written.required = "true";
     written.size="2";
     written.maxlength="2";
     written.max = $('#total_marks').val();
     written.min = 0;
     written.class="form-control";
     cell5.appendChild(written);

     /*var cell6 = row.insertCell(4);
     var mcq = document.createElement("input");
     mcq.type="number";

     mcq.name = "mcq[]";
     mcq.required = "true";
     mcq.size="2";
      mcq.max = mm;
     cell6.appendChild(mcq);

     var cell7 = row.insertCell(5);
     var practical = document.createElement("input");
     practical.type="number";

     practical.name = "practical[]";
     practical.required = "true";
      practical.size="2";
      practical.max = pm;
     cell7.appendChild(practical);

     var cell8 = row.insertCell(6);
     var ca = document.createElement("input");
     ca.type="number";

     ca.name = "ca[]";
     ca.required = "true";
     ca.max = cm;
     ca.size="2";
     cell8.appendChild(ca);
*/
      var cell9 = row.insertCell(4);
     var chkbox = document.createElement("input");
     chkbox.type = "text";
        chkbox.placeholder="No";
        chkbox.value="No";
      chkbox.name="absent[]";
      chkbox.size="3";
      cell9.appendChild(chkbox);
 };

</script>

@stop
