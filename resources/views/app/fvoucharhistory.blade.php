@extends('layouts.master')
@section('style')
<link href="{{url('/css/bootstrap-datepicker.css')}}" rel="stylesheet">
<style type="text/css">
#billItem thead th {
  color:#3986AC;
}

#invoice-POS{
 /*box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
  padding:2mm;
  margin: 0 auto;
  width: 44mm;
  background: #FFF;*/
  text-align: center;
  
}
#invoice-POS h1{
  font-size: 1em !important;
  color: #222 !important;
}
#invoice-POS h2{font-size: .9em !important;}
 #invoice-POS h3{
  font-size: .9em !important;
  font-weight: 300 !important;
  line-height: 2.5em !important;
}
#invoice-POS p{
  font-size: 1.3em !important;
  color: #000 !important;
  line-height: 2em !important;
}
  #top, #mid,#bot{ /* Targets all id with 'col-' */
  border-bottom: 1px solid #EEE !important; 
}
  #top{min-height: 100px !important;}
#mid{min-height: 80px !important;} 
#bot{ min-height: 50px !important;}

#top .logo{
  //float: left;
  height: 75px !important;
  width: 120px !important;
  background: url(http://school.ictcore.org/markssheetcontent/school-title.png) no-repeat;
  background-size: 130px 75px !important;
}
.clientlogo{
  float: left !important;
  height: 60px !important;
  width: 60px !important;
  background: url(http://michaeltruong.ca/images/client.jpg) no-repeat;
  background-size: 60px 60px !important;
  border-radius: 50px !important;
}
.info{
  display: block !important;
  //float:left !important;
  margin-left: 0 !important;
}
.title{
  float: right !important;
}
.title p{text-align: right !important;} 
#invoice-POS table{
  width: 100%;
  border-collapse: collapse !important;

}

 #invoice-POS td{
  //padding: 5px 0 5px 15px !important;
  //border: 1px solid #EEE !important;
   font-size: 1em !important;

}
.tabletitle{
  //padding: 5px !important;
  font-size: .9em !important; 
  background: #EEE !important;
}
.service{border-bottom: 1px solid #EEE !important;}
.item{width: 24mm !important;}
.itemtext{font-size: .5em !important;}

#legalcopy{
  margin-top: 5mm !important;
}
#invoice-POS th {
    text-align: center !important;
    font-size: 1em !important;
}
#invoice-POS .row {
   /* margin-right: -15px;
    margin-left: 662px !important;*/
    color:#000 !important;
}
</style>
@stop
@section('content')
@if (Session::get('success'))
<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
  <strong>Process Success.</strong> {{ Session::get('success')}}<br>

</div>
@endif
@if (count($errors) > 0)
<div class="alert alert-danger">
  <strong>Whoops!</strong> There were some problems.<br><br>
  <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif
<div class="row">
  <div class="box col-md-12">
    <div class="box-inner">
      <div data-original-title="" class="box-header well">
        <h2><i class="glyphicon glyphicon-list"></i> Vouchar List</h2>

      </div>
      <div class="box-content">

       
        @if($student->regiNo !="" && count($fees) < 1)
        <div class="alert alert-danger">
          <strong>Whoops!</strong> There are no fees entry for this student.<br><br>
        </div>
        @endif
        @if($fees)
        <div class="row">
          <div class="col-md-12">
          <a title='vouchar' class='btn btn-warning'  onclick="confirmed('{{$family_id}}');" href='#' style="float:right;margin-top:-30px;"> Get Voucher</a>
            <table id="feeList" class="table table-striped table-bordered table-hover">
              <thead>
                <tr>
                  <th>Payable Amount</th>
                  <th>Paid Amount</th>
                  <th>Due Amount</th>
                  <th>Status</th>
                  <th>Month</th>
                  <th>Pay Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($family_vouchers as $fee)
                <tr>
                  
                  <td>{{getdatainvoice($fee->bills,$fee->month)->payTotal}}</td>
                  <td>{{getdatainvoice($fee->bills,$fee->month)->paiTotal}}</td>
                  <td>{{getdatainvoice($fee->bills,$fee->month)->dueamount}}</td>
                  {{--<td>{{$fee->dueAmount}}</td>--}}
                   <td>
                   <?php
                    $paytotal  = getdatainvoice($fee->bills,$fee->month)->payTotal;
                    $paidtotal = getdatainvoice($fee->bills,$fee->month)->paiTotal;
                    $dueamount = getdatainvoice($fee->bills,$fee->month)->dueamount;
                      if($dueamount =="0.00" || $dueamount =="0"){
                          $status = 'paid';
                      }elseif($paidtotal=='0.00' ||$paidtotal=='' || $paidtotal==0){

                            $status = 'unpaid';
                      }else{
                          $status   = 'partially paid';
                      }
                      ?>
                   @if($status=='unpaid')
                   <button  class="btn btn-danger" >UnPaid</button>
                   @elseif($status=='paid') 
                   <button  class="btn btn-success" >Paid</button>
                   @else
                   <button  class="btn btn-warning" >Partially Paid</button>
                   @endif
                   </td>
                  <td>{{ \DateTime::createFromFormat('!m', $fee->month)->format('F')}}</td>
                  <td>{{$fee->date}}</td>

                  <td width="200">
                    


                  <a title='detail' href="#" onclick="details('{{$fee->id}}')" class='btn btn-info'> Detail</a>
                   @if($fee->status=='Unpaid')
                    <a title='Paid' href="#"  onclick="submitfrom('paid','{{$fee->id}}')"  class='btn btn-success'> Paid</a>
                    <form  id="fee_paid{{$fee->id}}" action='{{url("/family/paid")}}/{{$fee->id}}' method="post">
                    @else
                    <a title='unPaid'  href="#" class='btn btn-danger' @if(auth()->user()->group=='Admin') onclick="submitfrom('unpaid','{{$fee->id}}')" @endif  > UnPaid</a>
                    <form id="fee_unpaid{{$fee->id}}" action='{{url("/family/paid")}}/{{$fee->id}}?s=unpaid' method="post">

                    @endif
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="bills" value="{{$fee->bills}}">

                    </form>
                    
                    <a title='print'  href='{{url("/family/vouchar_print/")}}/{{$family_id}}/{{$fee->bills}}' class='btn btn-warning' target="_blank" >Print</a>

                  </td>
                  @endforeach
                </tbody>
              </table>
            </div>

          </div>
          <div class="row">
            <div class="col-md-12">
              <table class="table">

                <tbody>

                  <tr>
                    <td></td>
                    <td>Total Payable: <strong><i class="blue">{{$totals->payTotal }}</i></strong> rs.</td>
                    <td>Total Paid: <strong><i class="blue">{{$totals->paiTotal}}</i></strong> rs.</td>
                    <td>Total Due: <strong><i class="blue">{{$totals->dueamount}}</i></strong> rs.</td>
                    <td></td>

                    {{--<td>
                      <a title='Print' id="btnPrint" class='btn btn-info' target='_blank' href='{{url("/fees/report/std")}}/{{$student->regiNo}}'> <i class="glyphicon glyphicon-print icon-red"></i> Print</a>
                    </td>--}}
                  </tr>
                  </tbody>  
                </table>
              </div>

            </div>
            @endif
          </div>
        </div>
      </div>

@if($print)
        <div id="printableArea" style="display:none;">
            



<div id="invoice-POS">
    
    <center id="top">
      <div class="logo"></div>
      <div class="info"> 
        <h2></h2>
      </div><!--End Info-->
    </center><!--End InvoiceTop-->
    
    @foreach($family_vouchers as $fee)
    @if($fee->bills==$ids)
    <?php 
    $month = \DateTime::createFromFormat('!m', $fee->month)->format('F');
    ?>
    @endif
    @endforeach
    <div id="mid">
      <div class="info">
        <h2>Fee Vouchar</h2>
       <?php /*  <div class="row">
        <div class="col-md-2">Family ID </div>
        <div class="col-md-2">{{$family_id}}</div>
        </div>
        <div class="row">
        <div class="col-md-2">Father Name</div>
        <div class="col-md-2">{{$fthername->fatherName}}</div>
        </div>
        <div class="row">
        <div class="col-md-2"> Month</div>
        <div class="col-md-2">{{$month}}</div>
        </div>
        <div class="row">
        <div class="col-md-2">Paid Date</div>
        <div class="col-md-3"><?php echo date('m/d/Y h:i:s a', time()); ?></div>
        </div>*/?>

        <p> 
           <p> Family ID    : {{$family_id}}</p></br>
           <p> Father Name  : {{$fthername->fatherName}}ytyty</p></br>
           <p> Month        : {{$month}}</p></br>
           <p> Paid Date    : {{ date('m/d/Y h:i:s a', time()) }}</p></br>
        </p> 
       
    </div><!--End Invoice Mid-->
    
    <div id="bot">

          <div id="table">
            <table>
            <thead>
              <tr class="tabletitle">
                <th class="item"><h2>Student</h2></th>
                <th class="item"><h2>Payable</h2></th>
                <th class="item"><h2>Paid</h2></th>
                <!-- <td class="Rate"><h2>Sub Total</h2></td> -->
              </tr>
            <thead>
            <tbody>
            @foreach($print_vouchar as $vouchar)
              <tr class="service">
                <td class="tableitem"><p class="itemtext">{{$vouchar->firstName}} {{$vouchar->lastName}} ({{gclass_name($vouchar->class)->name}})-({{gsection_name($vouchar->section)->name}})</p></td>
                <td class="tableitem"><p class="itemtext">{{$vouchar->payableAmount}}</p></td>
                <td class="tableitem"><p class="itemtext">{{$vouchar->paidAmount}}</p></td>
                <!-- <td class="tableitem"><p class="itemtext">$375.00</p></td> -->
              </tr>
              @endforeach
             
              </tbody>
            </table>
          </div><!--End Table-->

           <div id="legalcopy">
            <!-- <p class="legal"><strong>Thank you for your business!</strong>  Payment is expected within 31 days; please process this invoice within that time. There will be a 5% interest charge per month on late invoices. 
            </p> -->
            <div class="sig-n-stamp">
                  <p>Accountant Sig : ___________ </p>
                  <p>For Detail : 03157180220</p>
              </div>
          
          </div>

        </div><!--End InvoiceBot-->
  </div><!--End Invoice-->
</div>
</div>

      </div>
@endif

      @stop
      @section('model')
          <div id="modelshow"></div>
          <div id="moddel"></div>

          <div id="billDetails" class="modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            {{--<div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title">Confirmation</h4>
            </div>--}}

            <div class="modal-header">
              <h4 class="modal-title">Confirmation</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table id="billItem" class="table table-striped table-bordered table-hover">
                      <thead>
                        <tr>
                          <th>Amount</th>
                          <th>Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tbody>
                        </tbody>
                        </table>
                      </div>
                    </div>

                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>
              </div>
            </div>
          </div>

      
      @stop
    <!-- Modal Goes here -->
     {{--<div id="billDetails" class="modal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title">Confirmation</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table id="billItem" class="table table-striped table-bordered table-hover">
                      <thead>
                        <tr>
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
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                </div>
              </div>
            </div>
          </div>--}}
          @section('script')
          <script src="{{url('/js/bootstrap-datepicker.js')}}"></script>
          <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
          <script type="text/javascript">
@if($print)
             var printContents       = document.getElementById("printableArea").innerHTML;
             var originalContents    = document.body.innerHTML;

             document.body.innerHTML = printContents;

             window.print();
             @endif

             //document.body.innerHTML = originalContents;

              function checkm(type){
                //alert(type);
                if(type == 'multi'){

                  $("#multis").show();
                }else{
                   $("#multis").hide();
                }
              }

              function confirmed(family_id)
              {
                //alert(family_id);
                //return confirm('Are you sure you want to generate family vouchar?');
                var x = confirm('Are you sure you want to generate family vouchar?');
                              if (x){
                                 //window.location.href('{{url("/family/vouchars")}}/'+family_id);
                                //window.location = "{{url('/family/vouchars')}}/"+family_id;
                                
                                $.ajax({
                                    url: "{{url('/f_vouchar/model')}}"+'/'+family_id,
                                    data: {
                                      //format: 'json'
                                    },
                                    error: function(error) {
                                      alert("Please fill all inputs correctly!");
                                    },
                                    //dataType: 'json',
                                    success: function(data) {
                                      console.log(data);
                                     $('#modelshow').html(data);
                                      $("#myModal"+family_id).modal('show');

                                    },
                                    type: 'GET'
                                });
                                // $("#billDetails").modal('show');
                          

                               return true
                             }
                              else{
                                return false;
                              }
              }

              function invoicepaidhistory(billId){
               // var billId=$(this).text();
                //alert(billId);
                $('.modal-title').html('"'+billId+'" bill details information');
                $.ajax({
                  url: "{{url('/fees/history/')}}"+'/'+billId,
                  data: {
                    format: 'json'
                  },
                  error: function(error) {
                    alert(JSON.stringify(error));
                    alert("Please fill all inputs correctly!");
                  },
                  dataType: 'json',
                  success: function(data) {
                    console.log(data);
                    $("#billItem").find("tr:gt(0)").remove();
                    for(var i =0;i < data.length;i++)
                    {
                      addRow1(data[i],i);
                    }

                  },
                  type: 'GET'
                });
                $("#billDetails").modal('show');
              }
         

         function details(id){

            $.ajax({
                  url: "{{url('/voucher/detail')}}"+'/'+id,
                  data: {
                    //format: 'json'
                  },
                  error: function(error) {
                    alert("Please fill all inputs correctly!");
                  },
                  //dataType: 'json',
                  success: function(data) {
                    console.log(data);
                   $('#modelshow').html(data);
                    $("#details"+id).modal('show');

                  },
                  type: 'GET'
              });
         }

          function submitfrom(type,id){
            if(type=='unpaid'){

              var x = confirm("Are you sure you want to Unpaid this vouchar?");
                if (x){
                   document.getElementById('fee_unpaid'+id).submit();
                 return true
               }
                else{
                  return false;
                }
              }
           /// document.getElementById('fee_unpaid').submit();
            else{
              var x = confirm("Are you sure you want to Paid this vouchar?");
                  if (x){
                      document.getElementById('fee_paid'+id).submit();
                  }else{
                    return false;
                  }
                }
                          
            }

            function feecol(billId) {
              //alert(33);
                          var billId=billId;
                         // $('.modal-title').html('"'+billId+'" bill details information');
                          
                          $.ajax({
                            url: "{{url('/fees/invoice/details/')}}"+'/'+billId,
                            data: {
                              //format: 'json'
                            },
                            error: function(error) {
                              alert(JSON.stringify(error));
                            },
                            //dataType: 'json',
                            success: function(data) {
                              console.log(data);
                             // $("#moddel").find("tr:gt(0)").remove();
                              $("#moddel").html(data);
                              $("#myModald"+billId).modal('show');
                              

                            },
                            type: 'GET',

                            
                          });
                          //alert(("#myModald"+billId));
                          console.log("#myModald"+billId);
                          
                        }

          function feecollection()
          {
            //
              var billNo = $('#billNo').val();
              //alert(billNo);
               var collectionAmount = $('#collectionAmount').val();
               var payableAmount = $('#payableAmount').val();
              // var postid = $('#post_id').val();
              //"{{url('/fees/invoice/details/')}}"+'/'+billId,
                $.ajax({
                   type: "POST",
                   url: "{{url('/fees/invoice/collect/')}}"+'/'+billNo,
                   data: {collectionAmount:collectionAmount,payableAmount:payableAmount},
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

                   success: function( msg ) {
                     //$("myModaldB003").modal().hide();
                  //alert(msg);
                     if(msg=='419'){
                      $("#oldtable").show();
                      $("#moddel .close").click();
                      Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Collection Amount greater then Payable amount',
                        footer: ''
                      })
                     }else if(msg=='404'){
                      $("#oldtable").show();
                      $("#moddel .close").click();
                      Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Collection Amount Not empty or 0',
                        footer: ''
                      })
                     }

                     else{
                     $("#oldtable").hide();
                     //$("#moddel").modal().hide();
                       $("#moddel .close").click();
                        //$("#ajax_data").remove();
                      //alert( JSON.stringify(msg) );
                      //console.log(msg);
                      //.// alert( "#myModald"+billNo+".close" );
                     //  obj.find('tbody').empty().append(msg);
                       var table= '';
                      $("#newtable").html(msg);
                     var  table =  $('#feeList1').dataTable({
                       
                         "sPaginationType": "bootstrap",


                      });
                     

                      table.$(".btninvoice").click(function(){
                          var billId=$(this).val();
                         // $('.modal-title').html('"'+billId+'" bill details information');
                          
                          $.ajax({
                            url: "{{url('/fees/invoice/details/')}}"+'/'+billId,
                            data: {
                              //format: 'json'
                            },
                            error: function(error) {
                              alert(JSON.stringify(error));
                            },
                            //dataType: 'json',
                            success: function(data) {
                              console.log(data);
                             // $("#moddel").find("tr:gt(0)").remove();
                              //$("#moddel").html(data);
                              //$("#myModald"+billId).modal('show');
                              

                            },
                            type: 'GET',

                            
                          });
                          //alert(("#myModald"+billId));
                          //console.log("#myModald"+billId);
                          //$("#myModald"+billId+ ".close").click();
                        });
                           

                       Swal.fire(
                                  'Invoice Paid',
                                  'You clicked the button!',
                                  'success'
                                ).then(function() {
                                  //location.reload();
                                //$("#myModald"+billId+ ".close").click();
                                //$('body').removeClass('modal-open');
                                //$('.modal-backdrop').remove();
                              });
                       
                               
                       // location.reload(true);
                      
                   }
                   }
                });
           }

            
         /* $('#details').on('hidden.bs.modal', function () {
              window.alert('hidden event fired!');
            });*/
          var stdRegiNo="{{$student->regiNo}}";
          $( document ).ready(function() {

            $(".btninvoice").click(function(){
             // alert(33);
                          var billId=$(this).val();
                         // $('.modal-title').html('"'+billId+'" bill details information');
                          
                          $.ajax({
                            url: "{{url('/fees/invoice/details/')}}"+'/'+billId,
                            data: {
                              //format: 'json'
                            },
                            error: function(error) {
                              alert(JSON.stringify(error));
                            },
                            //dataType: 'json',
                            success: function(data) {
                              console.log(data);
                             // $("#moddel").find("tr:gt(0)").remove();
                              $("#moddel").html(data);
                              $("#myModald"+billId).modal('show');
                              

                            },
                            type: 'GET',

                            
                          });
                          //alert(("#myModald"+billId));
                          console.log("#myModald"+billId);
                          
                        });

            getsections();
            $('#class').on('change',function() {
              getsections();
            });
            $('#feeList').dataTable({
               "sPaginationType": "bootstrap",
            });
            //var session = $('#session').val().trim();
              //getstudents();
            $(".datepicker2").datepicker( {
              format: " yyyy", // Notice the Extra space at the beginning
              viewMode: "years",
              minViewMode: "years",
              autoclose:true

            }).on('changeDate', function (ev) {

              //getstudents();

            });
            $('#class').change(function () {
              //getstudents();
            });
            $('#section').change(function () {
             // getstudents();
            });
            $('#shift').change(function () {
             // getstudents();
            });
            $('#student option').filter(function() {
              return ($(this).val() == stdRegiNo); //To select Blue
            }).prop('selected', true);

            $(".btnbill").click(function(){
              var billId=$(this).text();
              $('.modal-title').html('"'+billId+'" bill details information');
              $.ajax({
                url: "{{url('/fees/details/')}}"+'/'+billId,
                data: {
                  format: 'json'
                },
                error: function(error) {
                  alert("Please fill all inputs correctly!");
                },
                dataType: 'json',
                success: function(data) {
                  //console.log(data);
                  $("#billItem").find("tr:gt(0)").remove();
                  for(var i =0;i < data.length;i++)
                  {
                    addRow(data[i],i);
                  }

                },
                type: 'GET'
              });
              $("#billDetails").modal('show');
            });
          });
          /*function getstudents()
          {
            var aclass = $('#class').val();
            var section =  $('#section').val();
            var shift = 'Morning';
            var session = $('#session').val().trim();
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
                $('#student').empty();
                $('#student').append($('<option>').text("--Select Student--").attr('value',""));
                $.each(data, function(i, student) {
                  //console.log(student);
                  if(student.regiNo==stdRegiNo)
                  {
                    var opt="<option value='"+student.regiNo+"' selected>"+student.firstName+" "+student.middleName+" "+student.lastName+"["+student.rollNo+"] </option>"
                  }
                  else {
                    var opt="<option value='"+student.regiNo+"'>"+student.firstName+" "+student.middleName+" "+student.lastName+"["+student.rollNo+"] </option>"

                  }
                  //console.log(opt);
                  $('#student').append(opt);

                });
                //console.log(data);

              },
              type: 'GET'
            });
          };*/

          function addRow1(data,index) {
            var table = document.getElementById('billItem');
            var rowCount = table.rows.length;
            var row = table.insertRow(rowCount);

            var cell2 = row.insertCell(0);
            var title = document.createElement("label");

            title.innerHTML=data['amount'];
            cell2.appendChild(title);

            var cell3 = row.insertCell(1);
            var date1 = document.createElement("label");
            date1.innerHTML=data['date'];
            cell3.appendChild(date1);


           
          };

          function addRow(data,index) {
            var table = document.getElementById('billItem');
            var rowCount = table.rows.length;
            var row = table.insertRow(rowCount);

            var cell2 = row.insertCell(0);
            var title = document.createElement("label");

            title.innerHTML=data['title'];
            cell2.appendChild(title);

            var cell3 = row.insertCell(1);
            var month = document.createElement("label");
            month.innerHTML=getTXTmonth(data['month']);
            cell3.appendChild(month);


            var cell4 = row.insertCell(2);
            var fee = document.createElement("label");
            fee.innerHTML=data['fee'];
            cell4.appendChild(fee);

            var cell5 = row.insertCell(3);
            var lateFee = document.createElement("label");
            lateFee.innerHTML=data['lateFee'];
            cell5.appendChild(lateFee);

            var cell6 = row.insertCell(4);
            var total = document.createElement("label");
            total.innerHTML=data['total'];
            cell6.appendChild(total);
          };


          function getTXTmonth(mindex)
          {
            if(mindex=="1")
            {
              return "January";
            }
            else if(mindex=="2")
            {
              return "February";
            }
            else if(mindex=="3")
            {
              return "March";
            }
            else if(mindex=="4")
            {
              return "April";
            }
            else if(mindex=="5")
            {
              return "May";
            }
            else if(mindex=="6")
            {
              return "June";
            }
            else if(mindex=="7")
            {
              return "July";
            }
            else if(mindex=="8")
            {
              return "August";
            }
            else if(mindex=="9")
            {
              return "September";
            }
            else if(mindex=="10")
            {
              return "October";
            }
            else if(mindex=="11")
            {
              return "November";
            }
            else if(mindex=="12")
            {
              return "December";
            }
            else {
              return "Not Monthly Fee";
            }


          };

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
                    alert("Please fill all inputs correctly!");
                  },
                  dataType: 'json',
                  success: function(data) {
                    $('#section').empty();
                   //$('#section').append($('<option>').text("--Select Section--").attr('value',""));
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
