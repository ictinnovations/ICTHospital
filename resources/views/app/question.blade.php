@extends('layouts.master')
@section('style')
<link href="{{ URL::asset('/css/teckquiz.css') }}" rel="stylesheet">
<link href="{{url('/css/bootstrap-datepicker.css')}}" rel="stylesheet">
<style>
b {color:red}
</style>
@stop
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
                <h2><i class="glyphicon glyphicon-home"></i> Question Create</h2>

            </div>
            <div class="box-content">
        <form action="{{url('/question/create')}}" method="POST" class="form">
        {{ csrf_field() }}
        <div class="col-md-12">
            <div class="form-group">
                <label for="">Quiz Name <b>*</b></label>
                <input name="q_name" type="text" class="form-control" value="{{old('q_name')}}" required autofocus>
            </div>
            <div class="form-group">
                <label for="">Class <b>*</b></label>
                <select name="class_id" id="class_id" class="form-control" required>
                    <option value="">---Select Class---</option>
                    @foreach ($classes as $classe)
                    <option value="{{ $classe->code }}" @if(old('class_id')==$classe->code) selected @endif>{{ $classe->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="">Session <b>*</b></label>
                <input type="text"    name="session" class="form-control datepicker2" value="{{old('session',date('Y'))}}" required>                      
            </div>
            <div class="form-group">
                <label for="">Subject <b>*</b></label>
                <select id="subject" name="subject" class="form-control" required="true">
                  <option value="">--Select Subject--</option>
                </select>
            </div>
            <div class="form-group">
                <label for="">Chapter <b>*</b></label>
                <input type="text" name="chapter" class="form-control" value="{{old('chapter')}}" required>
            </div>
            <div class="form-group">
                <label for="">Levels <b>*</b></label>
                <select name="level" class="form-control" required>
                    <option value="">---Select a Level---</option>
                    <option value="simple" @if(old('level')=='simple') selected @endif>Simple</option>
                    <option value="normal" @if(old('level')=='normal') selected @endif>Normal</option>
                    <option value="hard" @if(old('level')=='hard') selected @endif>Hard</option>
                </select>
            </div>
        </div>

        <div class="col-12" id="question">
            <h3>Questions </h3>
            <div class="row">
                <div class="col-md-9">
                    <label for="">Question <b>*</b></label>
                    <textarea class="form-control"name="question[0]" id="question[0]" cols="30" rows="5" placeholder="Input question here..." required></textarea>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Question Type <b>*</b></label>

                        <select name="qt[0]" id="qt-0" class="form-control qt" required>
                            <option value="">---Select a question type---</option>
                            <option value="1">Long Question</option>
                            <option value="2">Multiple Choice</option>
                            <option value="3">Short Question</option>
                        </select>
                    </div>
                    <div class="form-group form-inline">
                        <label for="" class="pr-2">Points:</label><input type="number" class="form-control" min="1" value="1" name="points[]" style="max-width: 100px">
                        
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary btn-block btn-sm ml-1" onclick="addQuestion()">Add Another Question</button>
                    </div>
                </div>
                
                <div class="col-md-6" id="i-0" style="padding-top: 10px; display: none">
                    <label for="">Correct answer</label>
                    <input name="i[0]" type="text" class="form-control">
                </div>
                <div class="multiple-choice" id="mc-0" style="display: none">
                    <div class="col-md-12" style="padding-top: 10px;">
                        <div class="row">
                            <div class="col-sm-3"><label>Choice 1</label><input name="mc[0][0]" type="text" class="form-control"></div>
                            <div class="col-sm-3"><label>Choice 2</label><input name="mc[0][1]" type="text" class="form-control"></div>
                            <div class="col-sm-3"><label>Choice 3</label><input name="mc[0][2]" type="text" class="form-control"></div>
                            <div class="col-sm-3"><label>Choice 4</label><input name="mc[0][3]" type="text" class="form-control"></div>
                        </div>
                        <div class="row" style="padding-top: 10px;">
                            <div class="col-md-8">
                                <label for="">Correct choice</label>
                                <select name="c-mc[0]" id="c-mc[0]" class="form-control">
                                    <option value="1">Choice 1</option>
                                    <option value="2">Choice 2</option>
                                    <option value="3">Choice 3</option>
                                    <option value="4">Choice 4</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" id="tf-0" style="padding-top: 10px;  display: none">
                    <label for="">Correct answer</label>
                    <input type="text" class="form-control" name="tf[0]" id="">

                    {{--<select name="tf[0]" id="" class="form-control">
                        <option value="1">True</option>
                        <option value="0">False</option>
                    </select>--}}
                </div>
                <script>
                    $("#qt-0").change(function () {
                        $("#i-0").css('display', 'none');
                        $("#mc-0").css('display', 'none');
                        $("#tf-0").css('display', 'none');

                        if($(this).val() == 1){
                            $("#i-0").css('display', 'inline');
                        }
                        else if ($(this).val() == 2){
                             $("#mc-0").css('display', 'inline');
                        }
                        else if($(this).val() == 3){
                            $("#tf-0").css('display', 'inline');
                        }
                    });
                </script>
            </div>
            <hr>
        </div>
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary btn-block">Submit</button>
        </div>
    </form>






        </div>
    </div>
</div>
</div>
@stop
@section('script')

    <script src="{{ URL::asset('js/jquery.validate.min.js') }}"></script>
  <script src="{{url('/js/bootstrap-datepicker.js')}}"></script>
<script>
$(".datepicker2").datepicker( {
              format: " yyyy", // Notice the Extra space at the beginning
              viewMode: "years",
              minViewMode: "years",
              autoclose:true

            }).on('changeDate', function (ev) {

              

            });
    var newId = 1;
    var template = jQuery.validator.format(`
        <div class="row">
                <div class="col-md-9">
                    <label for="">Question <b>*</b></label>
                    <textarea class="form-control"name="question[{0}]" id="" cols="30" rows="5" placeholder="Input question here..." required></textarea>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">Question Type <b>*</b></label>

                        <select name="qt[{0}]" id="qt-{0}" class="form-control qt" required>
                            <option value="">---Select a question type---</option>
                            <option value="1">Long Question</option>
                            <option value="2">Multiple Choice</option>
                            <option value="3">Short Question</option>
                        </select>
                    </div>
                    <div class="form-group form-inline">
                        <label for="" class="pr-2">Points:</label><input type="number" class="form-control" min="1" name="points[]" value="1" style="max-width: 100px">
                        
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary btn-block btn-sm ml-1" onclick="addQuestion()">Add Another Question</button>
                    </div>
                </div>
                
                <div class="col-md-6" id="i-{0}" style="padding-top: 10px; display: none">
                    <label for="">Correct answer</label>
                    <input name="i[{0}]" type="text" class="form-control">
                </div>
                <div class="multiple-choice" id="mc-{0}" style="display: none">
                    <div class="col-md-12" style="padding-top: 10px;">
                        <div class="row">
                            <div class="col-sm-3"><label>Choice 1</label><input name="mc[{0}][0]" type="text" class="form-control"></div>
                            <div class="col-sm-3"><label>Choice 2</label><input name="mc[{0}][1]" type="text" class="form-control"></div>
                            <div class="col-sm-3"><label>Choice 3</label><input name="mc[{0}][2]" type="text" class="form-control"></div>
                            <div class="col-sm-3"><label>Choice 4</label><input name="mc[{0}][3]" type="text" class="form-control"></div>
                        </div>
                        <div class="row" style="padding-top: 10px;">
                            <div class="col-md-8">
                                <label for="">Correct choice</label>
                                <select name="c-mc[{0}]" id="c-mc[{0}]" class="form-control">
                                    <option value="1">Choice 1</option>
                                    <option value="2">Choice 2</option>
                                    <option value="3">Choice 3</option>
                                    <option value="4">Choice 4</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3" id="tf-{0}" style="padding-top: 10px;  display: none">
                    <label for="">Correct answer</label>
                    <input type="text" id="" class="form-control" name="tf[{0}]">
                    {{--<select name="tf[{0}]" id="" class="form-control">
                        <option value="1">True</option>
                        <option value="0">False</option>
                    </select>--}}
                </div>
                <script>
                    $("#qt-{0}").change(function () {
                        $("#i-{0}").css('display', 'none');
                        $("#mc-{0}").css('display', 'none');
                        $("#tf-{0}").css('display', 'none');

                        if($(this).val() == 1){
                            $("#i-{0}").css('display', 'inline');
                        }
                        else if ($(this).val() == 2){
                             $("#mc-{0}").css('display', 'inline');
                        }
                        else if($(this).val() == 3){
                            $("#tf-{0}").css('display', 'inline');
                        }
                    });
                <\/script>
        </div>
        <hr>
    `);

    function addQuestion(){
        $('#question').append(template(newId));
        newId++;
    }
$( document ).ready(function() {
 $("#class_id").trigger('change'); 
$('#class_id').on('change', function (e) {
    //alert(34);
    getsections();
    subject();
 });
 });
 function subject()
 {
   var val = $('#class_id').val();
            $.ajax({
                url:"{{url('/class/getsubjects')}}"+'/'+val,
                type:'get',
                dataType: 'json',
                success: function( json ) {
                    $('#subject').empty();
                    $('#subject').append($('<option>').text("--Select Subject--").attr('value',""));
                    $.each(json, function(i, subject) {
                        // console.log(subject);

                        $('#subject').append($('<option>').text(subject.name).attr('value', subject.id));
                    });
                }
            });
 }

function getsections()
{

    var aclass = $('#class_id').val();
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
          var selected ='';
          //console.log(section.id);
         
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

</script>
@stop

