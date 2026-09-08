<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <title>Marks</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- The styles -->
    <link id="bs-css" href="{{url('')}}/css/bootstrap-cerulean.min.css" rel="stylesheet">

    <link href="{{url('')}}/css/charisma-app.css" rel="stylesheet">
   <link href="{{url('')}}/css/bootstrap-datepicker.css" rel="stylesheet">

    <style>
        @media print
        {
            .no-print, .no-print *
            {
                display: none !important;
            }
        }
        #footer
        {

            width:100%;
            height:50px;
            position:absolute;
            bottom:0;
            left:0;
        }
        .logo
        {
            height:80px;
            width: 100px;
        }
        #attendanceList{
            font-size: 11px;
            font-weight: bold;
        }
        #attendanceList th,#attendanceList td{
            text-align: center;
        }
        #attendanceList tr td{
            padding: 2px;
        }
        body {
            -webkit-print-color-adjust: exact;
            padding: 0;
            margin: 0;
        }
        .rInfo{
            padding-right: 10px;
        }

    </style>
</head>
<body>
    {{--<div class="row">--}}
    {{--<div class="col-md-12">--}}
    {{--<a  class="btn btn-danger no-print" href="/teacher-attendance/list"><i class=""></i>Back</a>--}}
    {{--</div>--}}
    {{--</div>--}}

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
            <form role="form" action="" method="get" enctype="multipart/form-data">
                <input type="hidden" name="class" value="{{ $_GET['class'] }}">
                <input type="hidden" name="sub_id" value="{{$_GET['sub_id'] }}">
                <input type="hidden" name="section" value="{{$_GET['section'] }}">

                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="exam">Examination</label>
                                <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                    <select name="exam" id="exam" required="true" class="form-control" >
                                        <option value="">-Select Exam-</option>
                                        @if($exams)
                                        @foreach($exams as $exm)
                                        <option value="{{$exm->id}}" @if($param1==$exm->id) selected @endif >{{$exm->type}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="exam">Total Marks</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                    <input name="total_marks" id="total_marks" value="{{$param2}}" required="true" class="form-control" >
                                </div>
                            </div>
                        </div>
                         {{--<div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label" for="exam">Session</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                    <input name="session" id="session" value="{{$session}}" required="true" class="form-control datepicker2" >
                                </div>
                            </div>
                        </div>--}}
                        <input type="hidden" id="session"  class="form-control " name="session" value="{{get_current_session()->id}}"   data-date-format="yyyy">

                         <div class="col-md-3">
                            <input type="submit" name="show" class="btn" style="margin-top: 25px;" value="Show Student">
                         </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
        
        <form role="form" action="{{url('new/mark/create')}}" method="post" enctype="multipart/form-data">
             <input type="hidden" name="session"  value="{{$session}}"  >
             <input type="hidden"  name="total_marks" value="{{$param2}}"  >
             <input type="hidden"  name="exam" value="{{$param1}}"  >
             <input type="hidden"  name="section" value="{{$section}}"  >
             <input type="hidden"  name="class" value="{{$class_code}}"  >
             <input type="hidden"  name="subject" value="{{$subject_id}}"  >
             <input type="hidden"  name="shift" value="Morning"  >
            <table id="" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Marks</th>
                        <th>IsAbsent</th>
                        <th>Send Sms</th>
                      
                    </tr>
                </thead>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <tbody id='studentList'>
                @if($students)
                @foreach($students as $student)
                    <tr>
                        <td>{{$student->fullname}}</td>
                        <input type="hidden"  name="regiNo[]" value="{{$student->student_id}}" > 

                        <td>
                         <input type="number" data-student="{{$student->student_id}}" min=0 max="{{$_GET['total_marks']}}" name="written[]" value="{{$student->obtain_marks}}" style="width: 10%;" required> /{{$_GET['total_marks']}}
                        </td>

                        <td>
                         Yes <input type="radio"  name="absent[{{$student->student_id}}]" value="yes"> 
                         No  <input type="radio"  name="absent[{{$student->student_id}}]" value="no" checked > 
                        </td>

                       <td>
                         Yes <input type="radio"  name="sms[{{$student->student_id}}]" value="yes" checked style="width: 10%;"> 
                         No  <input type="radio"  name="sms[{{$student->student_id}}]" value="no"  style="width: 10%;"> 
                        
                        </td>
                        
                    </tr>
                @endforeach
                @endif
                </tbody>
            </table>
            
         
           <button class="btn btn-primary pull-right" id="btnsave" type="submit"><i class="glyphicon glyphicon-plus"></i>Save</button>
  
       
        </form>

        </div>
    </div>
</div>



<script src="{{url('')}}/bower_components/jquery/jquery.min.js"></script>
<script src="{{url('')}}/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="{{url('/js/bootstrap-datepicker.js')}}"></script>
<script type="text/javascript">
//
$( document ).ready(function() {
    $(".datepicker2").datepicker( {
        format: " yyyy", // Notice the Extra space at the beginning
        viewMode: "years",
        minViewMode: "years",
        autoclose:true

    }).on('changeDate', function (ev) {

    });
});
</script>
</body>
</html>
