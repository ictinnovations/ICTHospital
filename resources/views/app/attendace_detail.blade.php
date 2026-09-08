@extends('layouts.master')
@section('style')
    <link href="{{ URL::asset('/css/bootstrap-datepicker.css')}}" rel="stylesheet">

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
                    <h2><i class="glyphicon glyphicon-book"></i> Attendance List</h2>

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

                   
                    @if($attendances_detail)
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
                                    @foreach($attendances_detail as $atd)
                                        <tr>
                                            <td>{{$atd->regiNo}}</td>
                                            <td>{{$atd->firstName}}  {{$atd->lastName}}</td>
                                             <td>{{$atd->class}}</td>
                                            <td>{{$atd->section}}</td>
                                            <td>{{$atd->fatherName}} </td>
                                            <td>{{$atd->fatherCellNo}} </td>
                                            <td>
                                              @if($atd->status=="Present")
                                              <span class="text-success">Present</span>
                                              @else

                                              <span class="text-danger">{{$atd->status}}</span>

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
             $('#attendanceList').dataTable({
                "sPaginationType": "bootstrap",
            });
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
