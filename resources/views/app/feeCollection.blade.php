@extends('layouts.master')
@section('style')
<style type="text/css">
  .table-responsive input {
    width:90px !important;
  }

</style>
<link href="/css/bootstrap-datepicker.css" rel="stylesheet">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
@stop
@section('content')
@if (Session::get('success'))
<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
  <strong>Process Success.</strong> {{ Session::get('success')}}<br><a href="/fees/view">View List</a><br>
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
<?php 
if(!empty($_GET)){

   $class1      = $_GET['class_id'];
   $section     = $_GET['section'];
   $session     = $_GET['session'];
   $month       = $_GET['month'];
   $type        = $_GET['type'];
   $fee         = $_GET['fee_name'];

}else{
    
  $class1       = '';
  $section      = '';
  $session      = '';
  $month        = $month;
  $type         = '';
  $fee          = '';
  //$regiNo     ='';
}
//echo "<pre>";print_r($_GET);
?>
<div class="row">
  <div class="box col-md-12">
    <div class="box-inner">
      <div data-original-title="" class="box-header well">
        <h2><i class="glyphicon glyphicon-list"></i> Fee Collection</h2>

      </div>
      <div class="box-content">

        <form role="form" action="{{url('/fee/collection')}}" method="post" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <div class="row">
            <div class="col-md-12">



              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="class">Class</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-home blue"></i></span>
                    <select id="class" id="class" name="class" class="form-control" >
                      @foreach($classes as $class)
                      <option value="{{$class->code}}" @if($class1==$class->code) Selected @endif>{{$class->name}}</option>
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
                    <select id="section" name="section"  class="form-control" >
                     @if(!empty($sections))
                      @foreach($sections as $sction)
                      <option value="{{$sction->id}}" @if($section==$sction->id) selected @endif>{{$sction->name}}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                </div>
              </div>

              <?php /*<div class="col-md-4">
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
              </div>


            </div>
           */?>
           <input type="hidden" value="Morning" name="shift">
             {{--<div class="col-md-4">
                <div class="form-group ">
                  <label for="session">session</label>
                  <div class="input-group">

                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                    <input value="{{date('Y')}}" type="text" id="session" required="true" class="form-control datepicker2" name="session"   data-date-format="yyyy">
                  </div>
                </div>
              </div>--}}
              <input value="{{get_current_session()->id}}" type="hidden" id="session" required="true" class="form-control " name="session"   data-date-format="yyyy">

 </div>
          
          <hr class="hrclass">
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="type">Type</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select id="type" name="type" class="form-control" required>
                      <option>--Select Fee Type--</option>
                      <option value="Other" @if($type=='Other') Selected @endif >Other</option>
                      <option value="Monthly" @if($type=="Monthly") Selected @endif Selected>Monthly</option>

                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="month">Month</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select id="month" name="month" class="form-control" style="display:none">

                      <option selected="selected" value="-1">--Select Month--</option>
                      <option value="1" @if($month=='1') selected @endif>January</option>
                      <option value="2" @if($month=='2') selected @endif>February</option>
                      <option value="3" @if($month=='3') selected @endif>March</option>
                      <option value="4" @if($month=='4')  selected @endif>April</option>
                      <option value="5" @if($month=='5')  selected @endif>May</option>
                      <option value="6" @if($month=='6')  selected @endif>June</option>
                      <option value="7" @if($month=='7')  selected @endif>July</option>
                      <option value="8" @if($month=='8')  selected @endif>August</option>
                      <option value="9" @if($month=='9')  selected @endif>September</option>
                      <option value="10" @if($month=='10') selected @endif>October</option>
                      <option value="11" @if($month=='11') selected @endif>November</option>
                      <option value="12" @if($month=='12') selected @endif>December</option>
                    </select>

                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label class="control-label" for="student">Fee Name</label>

                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <select id="fee" name="fee" class="form-control" required="true">
                    <option value="-1">--Select Fee--</option>
                  @foreach($fees as $fe)
                   <option value="{{$fe->id}}" @if($fe->id==$fee) Selected @endif>{{$fe->title}}</option>
                  @endforeach
                  </select>
                </div>
              </div>
            </div>

          </div>
        </div>
        <div class="row">
            <div class="col-md-12">
            
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="student">Student</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-book blue"></i></span>
                    <select id="student" name="student" class="form-control " required="true" >
                     @if($student)
                     <!--<option value='-1'>---Select Student---</option>-->
                     <option value="{{$student->regiNo}}">{{$student->firstName}} {{$student->lastName}} [{{$student->rollNo}}]</option>
                    @endif
                    </select>
                  </div>
                </div>
              </div>

             <!-- <div class="col-md-4">
                <div class="form-group ">
                  <label for="dob">Collection Date</label>
                  <div class="input-group">

                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                    <input type="text" value="{{date('Y-m-d')}}"  class="form-control datepicker" name="date" required  data-date-format="yyyy-mm-dd">
                  </div>


                </div>
              </div>-->

            </div>
          </div>
        <div id="feeInfoDiv" style="Display:none">
        <div class="row">
            <div class="col-md-12">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="feeAmount">Class Fee</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <input id="total_fee" type="text" class="form-control" readonly="true"  name="total_fee" placeholder="0.00">
                  </div>
                </div>
              </div>
              </div>
              </div>
          <div class="row">
            <div class="col-md-12">
            <div class="col-md-3" id="stddis">
                <div class="form-group">
                  <label for="discount">Student Fee Discount</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <input  type="text" id="discount" readonly class="form-control" name="discount"  placeholder="0.00">
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="feeAmount">Total Fee</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <input id="feeAmount" type="text" class="form-control" readonly="true"  name="feeAmount" placeholder="0.00">
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="LateFeeAmount">Late Fee Fine( <i id="LateFeeAmount1"> </i> )</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <input id="LateFeeAmount" type="text" class="form-control" name="LateFeeAmount" value="0.00" placeholder="0.00">
                  </div>
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <div class="input-group">
                    <button type="button" class="btn btn-primary" id="btnAddRow"  ><i class="glyphicon glyphicon-plus"></i> Add Fee</button>&nbsp;&nbsp;
                    <button type="button" class="btn btn-danger" id="btnDeleteRow" ><i class="glyphicon glyphicon-trash"></i> Remove Fee</button>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
        <hr class="hrclass">
        <div class="row">
          <div class="col-md-11">
            <div class="table-responsive" style="margin-left: 20px;">
              <table id="feeList" class="table table-striped table-bordered table-hover">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Month</th>
                    <th>Fee</th>
                    <th>Late Fee</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tbody>
                  </table>
                </div>
              </div>
            </div>
            <br>
            <br>
            <div class="row">
              <div class="col-md-12">
                <div class="col-md-6">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="col-md-6">
                        <label class="control-label" for="ctotal">Current Total:</label>
                      </div>
                      <div class="col-md-6">
                        <input type="text" class="form-control" id="ctotal" readOnly="true" name="ctotal" value="0.00">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="col-md-6">
                        <label class="control-label" for="previousdue">Previous Due:</label>
                      </div>
                      <div class="col-md-6">
                        <input type="text" class="form-control" id="previousdue" readOnly="true"  name="previousdue" value="0.00">
                      </div>
                    </div>
                  </div>
                  <div class="row" style="display:none">
                    <div class="col-md-12">
                      <div class="col-md-6">
                        <label class="control-label" for="gtotal">Grand Total:</label>
                      </div>
                      <div class="col-md-6">
                        <input type="text" class="form-control" id="gtotal" readOnly="true"  name="gtotal" value="0.00">
                      </div>
                    </div>
                  </div>
                  <div class="row" style="display:none">
                    <div class="col-md-12">
                      <div class="col-md-6">
                        <label class="control-label" for="paidamount">Paid Amount:</label>
                      </div>
                      <div class="col-md-6">
                        <input type="number" min='0' class="form-control" id="paidamount" required="true" name="paidamount" value="0.00">
                      </div>
                    </div>
                  </div>

                  <div class="row" >
                    <div class="col-md-12">
                      <div class="col-md-6">
                        <label class="control-label" for="paidamount">Payable Amount:</label>
                      </div>
                      <div class="col-md-6">
                        <input type="number" min='0' class="form-control" id="ctotal1" required="true" name="paidamount" value="0.00" readonly>
                      </div>
                    </div>
                  </div>
                  <div class="row" style="display:none">
                    <div class="col-md-12">
                      <div class="col-md-6">
                        <label class="control-label" for="dueamount">Due Amount:</label>
                      </div>
                      <div class="col-md-6">
                        <input type="text" class="form-control" id="dueamount" readOnly="true"  name="dueamount" value="0.00">
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="col-md-6">
                      </div>
                      <br>
                      <div class="col-md-6"><button class="btn btn-primary" id="btnsave1" name='save_sms'  value="save_sms" type="submit"><i class="glyphicon glyphicon-plus"></i>Save and SMS</button>
                   		   <button class="btn btn-primary pull-right" id="btnsave" name='only_save' value="only_save" type="submit"><i class="glyphicon glyphicon-plus" ></i>Save</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>


          </form>
<p id="disc" style="display:none"></p>
        </div>
      </div>
    </div>
    @stop
    @section('script')

    <script src="{{url('/js/bootstrap-datepicker.js')}}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
    <script type="text/javascript">
    $( document ).ready(function() {
       
        $('#feeInfoDiv').hide();
        if ($('#type').val()=="Monthly")
        {
          $('#month').show();
        }
        else
        {
          $('#month').hide();
        }
        /*var aclass = $('#class').val();
        var type =  $('#type').val();
        $.ajax({
          url: "{{url('/fee/getListjson/')}}"+'/'+aclass+'/'+type,
          data: {
            format: 'json'
          },
          error: function(error) {
            alert("Please fill all inputs correctly!");
          },
          dataType: 'json',
          success: function(data) {
            $('#fee').empty();
            $('#fee').append($('<option>').text("--Select Fee--").attr('value',"-1"));
            $.each(data, function(i, fee) {
              // console.log(fee);
             var sel='';
             
              $('#fee').append($('<option>').text(fee.title).attr('value', fee.id).attr('selected',sel));
            });
            //console.log(data);

          },
          type: 'GET'
        });
*/

      });






    function btnSaveIsvisibale()
    {
      var table = document.getElementById('feeList');
      var rowCount = table.rows.length;
    //  console.log(rowCount);
      if(rowCount>1)
      {
        $('#btnsave').show();
        $('#btnsave1').show();
      }
      else {
        $('#btnsave').hide();
        $('#btnsave1').hide();
      }
    }
    var getStudents = function () {
      var aclass = $('#class').val();
      var section =  $('#section').val();
     // var shift = $('#shift').val();
      var shift = 'Morning';

      var session = $('#session').val().trim();
      if(section!=''){
      $.ajax({
        url: "{{url('/student/getList')}}"+'/'+aclass+'/'+section+'/'+shift+'/'+session,
        data: {
          format: 'json'
        },
        error: function(error) {
          alert("Please fill all inputs correctly!");
        },
        dataType: 'json',
        success: function(data) {
         // .selectpicker('refresh');
          $('#student').empty();
          $('#student').append($('<option>').text("--Select Student--").attr('value',""));
          $.each(data, function(i, student) {
            // console.log(student);

            $('#student').append($('<option>').text(student.firstName+" "+student.middleName+" "+student.lastName+"["+student.rollNo+"]").attr('value', student.regiNo));
              
          });
         // $('#student').selectpicker('refresh');
          //console.log(data);

        },
        type: 'GET'
      });
    }

    };
     /*$('#student').change(function() {
      var ids = $('#fee').val();
        if (ids!="-1")
        {
          $('#feeInfoDiv').show();
        }
        else
        {
          $('#feeInfoDiv').hide();
        }
       
       // alert("ewe"+ids);
        console.log("{{url('/fee/getFeeInfo')}}"+'/'+ids);
        //damnt = 0;
        alert( "yy" + $("#disc").html());
        $.ajax({
          url: "{{url('/fee/getFeeInfo')}}"+'/'+ids,
          data: {
            format: 'json'
          },
          error: function(error) {
            alert("Please fill all inputs correctly!");
          },
          dataType: 'json',
          success: function(data) {
            //$('#LateFeeAmount').val(data[0].Latefee);
            $('#LateFeeAmount1').html(data[0].Latefee);
            //$('#feeAmount').val(data[0].fee);
            var damnt =  $("#disc").html();
            alert("www"+damnt);
            if($("#disc").html()!=''){
              //alert("hello testing");
              //alert(damnt);
              var fee = data[0].fee/100 * damnt;
              var total = data[0].fee - fee;
               $('#total_fee').val(data[0].fee);
               $('#feeAmount').val(total);
               $('#paidamount').val(total);
               $('#per').html(damnt);
               $('#discount').val(fee);
          
           }else{
            $('#paidamount').val(data[0].fee);
           }
            //console.log(data);

          },
          type: 'GET'
        });


      });*/
      $("#student").on('change',function(){
       // alert(34);
       

        var student_reg = $("#student").val();
        var ids = $('#fee').val();
        alert(ids);
        $('#feeInfoDiv').show();
        if (ids!="-1")
        {
        $('#feeInfoDiv').show();
      //  alert('select fee Name');
        }
        else
        {
        $('#feeInfoDiv').hide();
        alert('select fee Name');
        }

        // alert("ewe"+ids);
        //console.log("{{url('/fee/getFeeInfo')}}"+'/'+ids);
        //damnt = 0;
        //alert( "yy" + $("#disc").html());
        // alert(student_reg);
        // $('#disc').html(0);
        $.ajax({
          url: "{{url('/fee/getdiscountjson')}}"+'/'+student_reg,
          data: {
          format: 'json'
          },
          error: function(error) {
            alert("Please fill all inputs correctly!");
          },
          dataType: 'json',
          success: function(data) {

            //  $('#fee').append($('<option>').text("--Select Fee--").attr('value',"-1"));
            var dic_amount = JSON.stringify(data.discount_id);
            //alert("sd"+dic_amount);
            /*if(dic_amount=='null'){
              dic_amount = 0;
            }*/
            var dis = 0;
            if(dic_amount==2){
              $('#disc').html(10);
              dis=10;
            }
            else if(dic_amount==3){
              $('#disc').html(20);
              dis=20;
            }
            else if(dic_amount==4){
              $('#disc').html(30);
              dis=30;
            }
            else if(dic_amount==5){
              $('#disc').html(40);
              dis=40;
            }
            else if(dic_amount==6){
              $('#disc').html(50);
               dis=50;
            }
            else if(dic_amount==7){
              $('#disc').html(60);
              dis=60;
            }
            else  if(dic_amount==8){
              $('#disc').html(65);
              dis=65;
            }
            else if(dic_amount==9){
              $('#disc').html(90);
              dis=90;
            }
            else if(dic_amount=='null'){
              $('#disc').html(0);
              dis=0;
            }else{
              $('#disc').html(0);
              dis=0;
            }
            //$('#fee').empty();
            //alert(dic_amount);
            //console.log(data);
            $.ajax({
              url: "{{url('/fee/getFeeInfo')}}"+'/'+ids,
              data: {
              format: 'json'
              },
              error: function(error) {
               alert("Please fill all inputs correctly!");
              },
              dataType: 'json',
              success: function(data) {
                //$('#LateFeeAmount').val(data[0].Latefee);
                $('#LateFeeAmount1').html(data[0].Latefee);
                //alert( $('#LateFeeAmount1').html(data[0].Latefee));
                //$('#feeAmount').val(data[0].fee);
                var damnt =  $("#disc").html();
                //alert("www"+damnt);
                if($("#disc").html()!=''){
                //alert("hello testing");
                //alert(damnt);
                var fee = dic_amount;
                if (fee == 'null'){
                  fee=0;
                }
                if($("#type")=='Monthly'){
                var total = data[0].fee - fee;
                }else{
                  var total = data[0].fee ;
                }
                //alert(fee);
                $('#total_fee').val(data[0].fee);
                
                $('#feeAmount').val(total);
                //$('#paidamount').val(total);
                $('#per').html(damnt);
                $('#discount').val(fee);
                }else{
                $('#paidamount').val(data[0].fee);
                }
                //console.log(data);
              },
              type: 'GET'
            });
          },
          type: 'GET'
        });
      
      });
    $( document ).ready(function() {
     <?php if(!empty($student)){ ?>
      $('#student').trigger('change');
       <?php } ?>
        <?php if(empty($sections)){ ?>
        getsections();
        <?php } ?>
              $('#class').on('change',function() {
                getsections();
                $('#type').trigger("change");
              });
      btnSaveIsvisibale();
      <?php if(empty($student)){ ?>
      getStudents();
      <?php } ?>
      $(".datepicker").datepicker({autoclose:true,todayHighlight: true});
      $(".datepicker2").datepicker( {
        format: " yyyy", // Notice the Extra space at the beginning
        viewMode: "years",
        minViewMode: "years",
        autoclose:true

      }).on('changeDate', function (ev) {
        getStudents();
      });
      $('#class').change(function () {
        getStudents();
      });
      $('#section').change(function () {
        getStudents();
      });
      $('#shift').change(function () {
        getStudents();
      });
      //get fee list
      $('#type').change(function() {
        $('#feeInfoDiv').hide();
        if ($('#type').val()=="Monthly")
        {
          $('#month').show();
        }
        else
        {
          $('#month').hide();
          document.getElementById("month").value = "-1";
        }
        var aclass  =  $('#class').val();
        var type    =  $('#type').val();
        $.ajax({
          url: "{{url('/fee/getListjson/')}}"+'/'+aclass+'/'+type,
          data: {
            format: 'json'
          },
          error: function(error) {
            alert("Please fill all inputs correctly!");
          },
          dataType: 'json',
          success: function(data) {
            $('#fee').empty();
            $('#fee').append($('<option>').text("--Select Fee--").attr('value',"-1"));
            $.each(data, function(i, fee) {
              // console.log(fee);

              $('#fee').append($('<option>').text(fee.title).attr('value', fee.id));
            });
            //console.log(data);

          },
          type: 'GET'
        });


      });
      //get fee info
      //#,
      
      //add fee to grid
      $( "#btnAddRow" ).click(function() {
        //  console.log($('#fee').val());

        var table = document.getElementById('feeList');
        var rowCount = table.rows.length;
        var row = table.insertRow(rowCount);
       var late = 0;
        //total fee

         if(isNaN($('#LateFeeAmount').val())==true || $('#LateFeeAmount').val()==''){
        // alert('nan');

         late =0;
        }else{
          //alert('notnan');
          
            late=$('#LateFeeAmount').val();
        }


        var totalFee   = parseFloat($('#feeAmount').val())+parseFloat(late);
        var cell1      = row.insertCell(0);
        var chkbox     = document.createElement("input");
        chkbox.type    = "checkbox";
        chkbox.checked = false;
        chkbox.name    = "sl[]";
        chkbox.size    = "3";
        cell1.appendChild(chkbox);

        var cell2      = row.insertCell(1);
        var title      = document.createElement("input");
        title.name     = "gridFeeTitle[]";
        title.readOnly = "true";
        title.value    = $('#fee option:selected').text();
        cell2.appendChild(title);


        /*  var hdregi = document.createElement("input");
        hdregi.name="regiNo[]";
        hdregi.value=data['regiNo'];
        hdregi.type="hidden";
        cell2.appendChild(hdregi);*/


        var cell3      = row.insertCell(2);
        var month      = document.createElement("input");
        month.name     = "gridMonth[]";
        month.readOnly = "true";
        month.value    = $('#month option:selected').val();
        cell3.appendChild(month);
        /*   var hdroll = document.createElement("input");
        hdroll.name="rollNo[]";
        hdroll.value=data['rollNo'];
        hdroll.type="hidden";
        cell3.appendChild(hdroll);*/

        var cell4 = row.insertCell(3);
        var feeAmount = document.createElement("input");
        feeAmount.name="gridFeeAmount[]";
        feeAmount.readOnly="true";
        feeAmount.value=$('#feeAmount').val();
        cell4.appendChild(feeAmount);

        var cell5 = row.insertCell(4);
        var LateFeeAmount = document.createElement("input");
        LateFeeAmount.name="gridLateFeeAmount[]";
        LateFeeAmount.readOnly="true";
        if(isNaN($('#LateFeeAmount').val())==true || $('#LateFeeAmount').val()==''){
        // alert('nan');
         LateFeeAmount.value=0;
        }else{
          //alert('notnan');
          
            LateFeeAmount.value=$('#LateFeeAmount').val();
        }
        cell5.appendChild(LateFeeAmount);
       
        
          // alert(LateFeeAmount.value);
       // LateFeeAmount.value=$('#LateFeeAmount').val();
        

        var cell6 = row.insertCell(5);
        var total = document.createElement("input");
        total.name="gridTotal[]";
        total.readOnly="true";
        total.value=totalFee;
        cell6.appendChild(total);
        
        //var cell6 = row.insertCell(5);
        var feetype = document.createElement("input");
        feetype.name="feetype[]";
        feetype.readOnly="true";
        feetype.type="hidden";
        feetype.value=$('#fee option:selected').val();;
        cell6.appendChild(feetype);

        var type1 = document.createElement("input");
        type1.name="type1[]";
        type1.readOnly="true";
        type1.type="hidden";
        type1.value=$('#type option:selected').val();;
        cell6.appendChild(type1);

        /*var cell7 = row.insertCell(6);
        var feetype = document.createElement("input");
        feetype.name="feetype[]";
        feetype.readOnly="true";
        feetype.type="text";
        total.value=$('#fee option:selected').val();;
        cell7.appendChild(feetype);*/

        //add to total fee below
        var ctotal= parseFloat($('#ctotal').val());

        $('#ctotal').val(ctotal+totalFee);
        $('#ctotal1').val(ctotal+totalFee);
        //alert(ctotal+totalFee);
        addTotalWithDue();
        btnSaveIsvisibale();
        $('#month').val(-1);

      });
      //remove fee to grid
      $( "#btnDeleteRow" ).click(function() {
        try {
          var table = document.getElementById("feeList");

          var rowCount = table.rows.length;

          for(var i=0; i<rowCount; i++) {
            var row = table.rows[i];
            var chkbox = row.getElementsByTagName('input')[0];
            //  console.log(chkbox);
            if(null != chkbox && true == chkbox.checked) {
              var ftotal = parseFloat(row.getElementsByTagName('input')[5].value);
              var ctotal= parseFloat($('#ctotal').val());
              $('#ctotal').val(ctotal-ftotal);
             

              table.deleteRow(i);
              rowCount--;
              i--;
              addTotalWithDue();
            }
          }
          btnSaveIsvisibale();
        }catch(e) {
          alert(e);
        }
      });

      //get previous due
      $('#student').change(function() {
        var aclass = $('#class').val();
        var stdId  = $('#student').val();

        $.ajax({
          url: "{{url('/fee/getDue')}}"+'/'+aclass+'/'+stdId,
          data: {
            format: 'json'
          },
          error: function(error) {
            alert(error.message);
          },
          dataType: 'json',
          success: function(data) {
            $('#previousdue').val(data);
            console.log(data);
          },
          type: 'GET'
        });
      });
      function addTotalWithDue() {
        try {

          var gtotal = parseFloat($('#previousdue').val())+parseFloat($('#ctotal').val());
          $('#gtotal').val(gtotal);

        }
        catch (e) {
          // statements to handle any exceptions
          alert(e.message); // pass exception object to error handler
        }
      };
      $('#paidamount').on('input change keyup paste mouseup propertychange', function() {
        try {
          var paidamount =parseFloat($('#paidamount').val());
           // if(isNaN(paidamount))
           //{
            // throw "Invalid Number Format!";
           //}
           //else {
            var grandTotal = parseFloat($('#gtotal').val());
            var due = grandTotal-paidamount;
            //alert(paidamount);
            if(isNaN(paidamount) || paidamount==''){
             $('#dueamount').val(due);
            }else{
            $('#dueamount').val(due);
          }
          // }

        }
        catch (e) {
          // statements to handle any exceptions
          alert(e); // pass exception object to error handler
        }
      });
    });
function getsections()
{
    var aclass = $('#class').val();
   // alert(aclass);
    $.ajax({
      url: "{{url('/section/getList')}}"+'/'+aclass,
      data: {
        format: 'json'
      },
      error: function(error) {
        //alert("Please fill all inputs correctly!");
      },
      dataType: 'json',
      success: function(data) {
        $('#section').empty();
      $('#section').append($('<option>').text("--Select Section--").attr('value',""));
        $.each(data, function(i, section) {
          //console.log(student);
         
          
            var opt="<option value='"+section.id+"'>"+section.name + " </option>"

        
          //console.log(opt);
          $('#section').append(opt);

        });
        //console.log(data);

      },
      type: 'GET'
    });
};

    </script>

    @stop
