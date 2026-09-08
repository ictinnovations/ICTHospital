@extends('layouts.master')
@section('style')
<link href="/css/bootstrap-datepicker.css" rel="stylesheet">

@stop
@section('content')
@if (Session::get('success'))
<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
    <strong>Process Success.</strong> {{ Session::get('success')}}<br><a href="#">View List</a><br>

</div>
@endif
<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-user"></i> Diary</h2>

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
   <form role="form" action="{{url('/class/diary/save')}}" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="row">
                           <div class="col-md-12">
                               <div class="table-responsive">
                                  <table id="" class="table table-striped table-bordered table-hover">
                                       <thead>
                                       <tr>

                                        
                                           <th>Section</th>
                                           <th>Teacher</th>
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
      getstudent();
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

//alert("{{$class}}");
    var aclass = "{{$class}}";
    
      
     $.ajax({
           url: "{{url('/class/section/')}}"+'/'+aclass,
           data: {
               format: 'json'
           },
           error: function(error) {
              alert(JSON.stringify(error));
           },
           
           success: function(data) {
            //alert(JSON.stringify(data));

               if(data=='404'){
              alert('data Not Found');
              $("#btnsave").hide();
            }else{
              $("#sectionList").html(data);
              $("#btnsave").show();
            }


              //$("#sectionList").html(data);
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
