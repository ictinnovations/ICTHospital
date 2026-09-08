@extends('layouts.master')
@section('style')
    <link href="{{url('/css/bootstrap-datepicker.css')}}" rel="stylesheet">

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
            {{$formdata->session}}
        </div>
    @endif

    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-book"></i> Gradesheet</h2>

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

                    <form role="form" action="{{url('/gradesheet')}}" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        {{--<input value="{{date('Y')}}" type="text" id="session" required="true" class="form-control datepicker2" name="session" value="{{$formdata->session}}"  data-date-format="yyyy">
                        --}}
                        <input type="hidden" id="session"  class="form-control " name="session" value="{{get_current_session()->id}}"   data-date-format="yyyy">
                        <input type="hidden" id="class_f"  class="form-control " name="class_f" value="{{$formdata->class}}"   data-date-format="yyyy">
                        <input type="hidden" id="section_f"  class="form-control " name="section_f" value="{{$formdata->section}}"   data-date-format="yyyy">
                        <input type="hidden" id="regiNo_f"  class="form-control " name="regiNo_f" value="{{$regiNo}}"   data-date-format="yyyy">

                        <div class="row">
                            <div class="col-md-12">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label" for="class">Class</label>

                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-home blue"></i></span>
                                          {{ Form::select('class',$classes,$formdata->class,['class'=>'form-control','id'=>'class','required'=>'true'])}}
                                           {{--<select class="form-control" name="class" required id="class">
                                             @foreach($classes as $key=>$class)
                                             <option vlaue="{{$key}}" @if($formdata->class==$key) selected @endif>{{$class}} </option>
                                           
                                            @endforeach
                                           </select>--}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label" for="section">Section</label>

                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                            <?php  $data=[
                                                    '1'=>'A',
                                                    'B'=>'B',
                                                    'C'=>'C',
                                                    'D'=>'D',
                                                    'E'=>'E',
                                                    'F'=>'F',
                                                    'G'=>'G',
                                                    'H'=>'H',
                                                    'I'=>'I',
                                                    'J'=>'J'
                                            ];?>
                                            {{ Form::select('section',$data,$formdata->section,['class'=>'form-control','id'=>'section','required'=>'true'])}}


                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <label for="session">Result Type</label>
                                        <div class="input-group">
 
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                                        <select name="type" id="type"  class ='form-control'   >
                                        <option value="sigle" @if($formdata->type == 'sigle') selected @endif>Single</option>
                                        @if($gradsystem=='manual')
                                        <option value="compined"  @if($formdata->type == 'compined') selected @endif>Compined</option>
                                        @endif
                                        </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label" for="exam">Examination</label>

                                        <div class="input-group" id="single">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                            <?php  $data=[
                                                    'Class Test'=>'Class Test',
                                                    'Model Test'=>'Model Test',
                                                    'First Term'=>'First Term',
                                                    'Mid Term'=>'Mid Term',
                                                    'Final Exam'=>'Final Exam'
                                            ];?>
                                            {{ Form::select('exam',$data,$formdata->exam,['class'=>'form-control','id'=>'exam','required'=>'true'])}}
                                        </div>
                                        <div class="input-group" id="compined">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                            <select id="exam1" name="exam[]" class="form-control selectpicker" id="section" multiple data-actions-box="true" data-hide-disabled="true" data-size="5"  required="true">
                                                <?php /*  @foreach($section as $sec)
                                                <option value="{{$sec->id}}">{{$sec->name}}</option>
                                                @endforeach*/?>
                                                <option value="">--Select Section--</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-9">
                                <button class="btn btn-primary pull-right"  type="submit"><i class="glyphicon glyphicon-th"></i>Get List</button>

                            </div>

                            <div class="col-md-3">
                                <button class="btn btn-primary pull-right" name="send_sms" value="yes"  type="submit"><i class="glyphicon glyphicon-th"></i>Send Sms</button>

                            </div>
                        </div>
                    </form>
                    @if($students)
                        <div class="row">
                            <div class="col-md-12">
                                <table id="markList" class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>Regi No</th>
                                        <th>Roll No</th>
                                        <th>Name</th>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <th>Shift</th>
                                        <th>Group</th>
                                         <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($students as $student)
                                        <tr>
                                            <td>{{$student->regiNo}}</td>
                                            <td>{{$student->rollNo}}</td>
                                            <td>{{$student->firstName}} {{$student->middleName}} {{$student->lastName}}</td>
                                            <td>{{$formdata->postclass}}</td>
                                            <td>{{$student->section}}</td>
                                            <td>{{$student->shift}}</td>
                                            <td>{{$student->group}}</td>

                                            <td>
                                                @if($gradsystem=='' || $gradsystem=='auto')
                                                  <a title='Print' target="_blank" class='btn btn-info' href='{{url("/gradesheet/print")}}/{{$student->regiNo}}/{{$formdata->exam}}/{{$formdata->class}}'> <i class="glyphicon glyphicon-print icon-printer"></i></a>
                                                @else
                                                  <a title='Print' target="_blank" class='btn btn-info' href='{{url("/gradesheet/m_print")}}/{{$student->regiNo}}/{{$formdata->exam}}/{{$formdata->class}}?type={{ $type}}&examps_ids={{$exams_ids}}'> <i class="glyphicon glyphicon-print icon-printer"></i></a>
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
    <script src="{{url('/js/bootstrap-datepicker.js')}}"></script>
    <script type="text/javascript">
      $('#exam1').prop('required',false);
      <?php if($gradsystem=='auto' || $gradsystem==''){ ?>
        $('#exam1').prop('required',false);
        <?php } ?>
      $('#compined').hide();
      $('#single').show(); 
       //$('#exam').empty();
       //$('#exam1').empty();
     $("#type").trigger('change'); 
        $( document ).ready(function() {
          //$('#exam1').hide();
        $("#type").change(function()
        {
            var id=$(this).val();
          //  alert(id);
            if(id==='compined'){
                //$('#exam').empty();
                $('#exam').prop('required',false);
                $('#exam1').prop('required',true);
                 $("#exam").val('');

                

                $('#compined').show();
                $('#single').hide();
              // $('#exam').attr('multiple','multiple');
                //$('#exam').hide();
                //$('#exam1').show();
                /*$('#exam').attr({
               'multiple','multiple'
                
             }); */
            }else{
               //$('#exam1').hide();
               // $('#exam').show();
               // $("#exam1").selectpicker('refresh');
             $('#exam1').prop('required',false);
             $('#exam').prop('required',true);

             $('#exam1').selectpicker('deselectAll');

                $('#exam1').selectpicker('refresh');
                 // $("#exam1").val='';
                $('#compined').hide();
                $('#single').show(); 
            }
        });


            $(".datepicker2").datepicker( {
                format: " yyyy", // Notice the Extra space at the beginning
                viewMode: "years",
                minViewMode: "years",
                autoclose:true

            });
            $('#markList').dataTable({
                "sPaginationType": "bootstrap",
            });
            
      $('#class').on('change', function (e) {
    
                getsections();
                getexam();
      });
      $('#section').on('change', function (e) {
		
		
		          getexam();
	      });
     
       getsections();
        getexam();
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
        //alert("{{$formdata->section}}");
        $('#section').empty();
        $('#section').append($('<option>').text("--Select Section--").attr('value',""));
        $.each(data, function(i, section) {
          //console.log(section.id);
         if(section.id=={{$formdata->section}}){ var selected='selected' }else{ var selected=''; }
         // console.log(selected);
        var opt="<option value='"+section.id+"' " +selected+ " >"+section.name + " </option>"

        
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
    var section = $('#section').val();
    //alert(section);
    $.ajax({
      url: "{{url('/exam/getList')}}"+'/'+aclass+'?section='+section,
      data: {
        format: 'json'
      },
      error: function(error) {
        alert("Please fill all inputs correctly!");
      },
      dataType: 'json',
      success: function(data) {
        $('#exam').empty();
        $('#exam1').empty();
       var options = [];
       $('#exam').append($('<option>').text("--Select Exam--").attr('value',""));
        $.each(data, function(i, exam) {
          //console.log(student);
         
          
            var opt="<option value='"+exam.id+"'>"+exam.type + " </option>"

        
          //console.log(opt);
          $('#exam').append(opt);
          //$('#exam1').append(opt);
          options.push(opt);

        });
        $("#exam1").html(options).selectpicker('refresh');

        //console.log(data);

      },
      type: 'GET'
    });
};
    </script>
@stop

