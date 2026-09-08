@extends('layouts.master')
@section('style')
    <link href="{{ URL::asset('/css/bootstrap-datepicker.css')}}" rel="stylesheet">
<style type="text/css">
    
  


</style>
@stop
@section('content')
    @if (Session::get('success'))
        <div class="alert alert-success">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <strong>Process Success.</strong><br>{{ Session::get('success')}}<br>
        </div>
    @endif
    @if (Session::get('noresult'))
        <div class="alert alert-warning">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <strong>{{ Session::get('noresult')}}</strong>

        </div>
    @endif
    @if (isset($noResult))
        <div class="alert alert-warning">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <strong>{{$noResult['noresult']}}</strong>

        </div>
    @endif



    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-book"></i> Fee Detail List</h2>

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

                   
                    @if($fee_detail)
                     @if($status!="Paid")
                    <form action="{{url('/fee/unpaid_notification')}}" method="post">
                          <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="result" value="">
                        <input type="hidden" name="all" value="yes">
                        <div class="col-md-2">
                            <div class="form-group">
                              <label class="control-label" for="">&nbsp;</label>

                              <div class="input-group">
                                <button class="btn btn-primary pull-right" name="action" value="voice" id="btnsave" onclick="return confirm('Are you sure you want to send notification?');" type="submit" ><i class="glyphicon glyphicon-th"></i> Send voice Notification  </button>
                                

                              </div>
                              <div class="input-group" style="margin-top: -38px;/* margin-bottom: -178px; */margin-left: 210px;">
                                <button class="btn btn-primary pull-right" name='action' value='sms' id="btnsave" onclick="return confirm('Are you sure you want to send notification?');" type="submit" ><i class="glyphicon glyphicon-th"></i> Send Sms Notification  </button>
                                

                              </div>
                            </div>
                          </div>
                     </form>
                     @endif
                        <div class="row">
                            <div class="col-md-12">
                                <table id="attendanceList" class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Regi No</th>
                                        <th>Name</th>
                                         <th>Class</th>
                                        <th>Section</th>
                                        <th>FatherName</th>
                                        <th>Phone#</th>
                                        <th>Status</th>
                                       
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($fee_detail as $atd)
                                        <tr>
                                            <td>{{$atd->regiNo}}</td>
                                            <td>{{$atd->firstName}}  {{$atd->lastName}}</td>
                                            <td>{{$atd->class}}</td>
                                            <td>{{$atd->section_name}}</td>
                                            <td>{{$atd->fatherName}} </td>
                                            <td>{{$atd->fatherCellNo}} </td>
                                            <td>
                                            <?php /*
                                              @if($status=="Paid")
                                              {{--<span class="text-success">{{$status}}</span>--}}
                                              <span class="role paid">{{$status}}</span>
                                              @else

                                              {{--<span class="text-danger">{{$status}}</span>--}}
                                              <span class="role unpaid">{{$status}}</span>

                                              @endif
                                              */ ?>

                                                @if($statusn=="Paid" || $statusn=='paid')
                                              <?php

                                                  if($atd->payableAmount===$atd->paidAmount || $atd->paidAmount>=$atd->payableAmount){
                                                      $status = 'paid';
                                                  }elseif($atd->paidAmount=='0.00' ||$atd->paidAmount==''){

                                                        $status = 'unpaid';
                                                  }else{
                                                      $status = 'partially paid';
                                                  }
                                                  ?>
                                                  @if($status=='paid' || $status=='Paid')<button  class="btn btn-success" >{{$status}}</button>@elseif($status=='partially paid')<button  class="btn btn-warning" >{{$status}}</button>@else <button  class="btn btn-danger" >{{$status}}</button>@endif
                                                @else
                                                <span class="role unpaid">{{$statusn}}</span>

                                                @endif
                                            </td>
                                           

                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    @endif


                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script src="{{ URL::asset('/js/bootstrap-datepicker.js')}}"></script>
     <script type="text/javascript">
        $( document ).ready(function() {

               getsections();
              $('#class').on('change',function() {
                getsections();
              });
            $(".datepicker2").datepicker( {
                format: " yyyy", // Notice the Extra space at the beginning
                viewMode: "years",
                minViewMode: "years",
                autoclose:true

            });
            $(".datepicker").datepicker({
                autoclose:true,
                todayHighlight: true

            });
            $('#attendanceList').dataTable({

                  //pagingType: "simple",
                //pagingType: "simple",
                "pageLength": 100,
                //  "pagingType": "full_numbers",
                dom: 'Bfrtip',
                
       
       


            buttons: [
            {
                extend: 'print',
                customize: function ( win ) {
                    $(win.document.body)
                        .css( 'font-size', '10pt' )
                        .prepend(
                            @if($status!='Paid')
                            '<h2>Fee Defaulter List <small>{{$month_n}}-{{$year}}</small></h2>'
                           @else
                          '<h2>Fee Paid List <small>{{$month_n}}-{{$year}}</small></h2>'
                           @endif
                        );
 
                    $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );
                }
            }
        ],
        // "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-12'i><'col-md-12 center-block'p>>",
        "sPaginationType": "bootstrap",
            });

            $( "#btnPrint" ).click(function() {
                var aclass  =   $('#class').val();
                var section =   $('#section').val();
                //var shift = $('#shift').val();
                var shift   = 'Morning';
                var session = $('#session').val().trim();
                var subject = $('#subject').val();
                var atedate = $('#date').val().trim();

                if(aclass!="" && section !="" && shift !="" && session !="" && atedate!="")
                {

                   var exurl="{{url('/attendance/printlist')}}"+'/'+aclass+'/'+section+'/'+shift+'/'+session+'/'+atedate;

                    var win = window.open(exurl, '_blank');
                    win.focus();

                }
                else
                {
                    alert('Not valid');
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
