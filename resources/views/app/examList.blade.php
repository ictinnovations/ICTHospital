@extends('layouts.master')
@section('content')
@if (Session::get('success'))
<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
  <strong>Process Success.</strong><br>{{ Session::get('success')}}<br>
</div>

@endif
<div class="row">
  <div class="box col-md-12">
    <div class="box-inner">
      <div data-original-title="" class="box-header well">
        <h2><i class="glyphicon glyphicon-home"></i> Exam List</h2>
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
                   @if($exam)
              <form role="form" action="{{url('/exam/update')}}" method="post">
                         <input type="hidden" name="_token" value="{{ csrf_token() }}">
                         <input type="hidden" name="id" value="{{$exam->id }}">

                     <div class="row">
                     <div class="col-md-12">
                       <div class="col-md-4">
                           <div class="form-group">
                         <label for="for">Exam type  <b>*</b></label>
                         <div class="input-group">
                             <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                             <input type="text" class="form-control" autofocus required name="type" value="{{old('type',$exam->type)}}" placeholder="Type">
                         </div>
                     </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                         <label for="gpa">Class <b>*</b></label>
                         <div class="input-group">
                             <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                              <select name="class" id="class" class="form-control" required>
                                @foreach($classes as $class)
                                <option value="{{$class->code}}" @if($exam->class_id == $class->id) selected @endif>{{$class->name}}</option>
                                @endforeach
                              </select>
                         </div>
                     </div>
                       </div>
                       <div class="col-md-2">
                         <div class="form-group">
                             <label for="grade">Section  <b>*</b></label>
                             <div class="input-group">
                                 <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                    <select id="section" name="section" class="form-control" required="true">
                                     @foreach($sections as $sec)
                                      <option value="{{$sec->id}}" @if($exam->section_id == $sec->id) selected @endif>{{$sec->name}}</option>
                                      @endforeach
                                    </select>
                             </div>
                         </div>
                       </div>
                       
                       <div class="col-md-4">
                        <button class="btn btn-primary" type="submit" style="margin-top: 30px;"><i class="glyphicon glyphicon-plus"></i>Update</button>

                       </div>
                     </div>
                   </div>
                      </form>
                    @else
                    <form role="form" action="{{url('/exam/create')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="row">
                     <div class="col-md-12">
                       <div class="col-md-4">
                           <div class="form-group">
                         <label for="for">Exam type  <b>*</b></label>
                         <div class="input-group">
                             <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                             <input type="text" class="form-control" autofocus required name="type" value="{{old('type')}}" placeholder="Type">
                         </div>
                     </div>
                       </div>
                       <div class="col-md-2">
                           <div class="form-group">
                         <label for="gpa">Class <b>*</b></label>
                         <div class="input-group">
                             <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                              <select name="class"  id="class" class="form-control" required>
                                @foreach($classes as $class)
                                <option value="{{$class->code}}" @if(old('class')==$class->code) selected @endif>{{$class->name}}</option>
                                @endforeach
                              </select>
                         </div>
                     </div>
                       </div>
                       <div class="col-md-2">
                         <div class="form-group">
                             <label for="grade">Section  <b>*</b></label>
                             <div class="input-group">
                                 <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                   <select id="section" name="section[]" class="form-control selectpicker" id="section" multiple data-actions-box="true" data-hide-disabled="true" data-size="5"  >
                                      <?php /*  @foreach($section as $sec)
                                          <option value="{{$sec->id}}">{{$sec->name}}</option>
                                          @endforeach*/?>
                                      <option value="">--Select Section--</option>
                                    </select>
                             </div>
                         </div>
                       </div>
                       <div class="col-md-4">
                        <button class="btn btn-primary" type="submit" style="margin-top: 30px;"><i class="glyphicon glyphicon-plus"></i>Add</button>

                       </div>

                     </div>
                   </div>
                      <br>
                        </form>
                    @endif
                    <br>
                  </div>
      <div class="box-content">
        <table id="classList" class="table table-striped table-bordered table-hover">
          <thead>
            <tr>
              <th style="width:30%">Name</th>
              <th style="width:30%">Class</th>
              <th style="width:30%">Section</th>
              <th style="width:15%">Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($exams as $exam)

            <tr>
              <td>{{$exam->type}}</td>
              <td>{{$exam->class}}</td>
              <td>{{$exam->section}}</td>

              <td>
                <a title='Edit' class='btn btn-info' href='{{url("/exam/edit")}}/{{$exam->id}}'> <i class="glyphicon glyphicon-edit icon-white"></i></a>&nbsp&nbsp<a title='Delete' class='btn btn-danger' href='{{url("/exam/delete")}}/{{$exam->id}}'> <i class="glyphicon glyphicon-trash icon-white"></i></a> </td>
              @endforeach
            </tbody>
          </table>
          <br><br>


        </div>
      </div>
    </div>
  </div>
  @stop
  @section('script')
  <script type="text/javascript">
  $( document ).ready(function() {
    $('#classList').dataTable({
        "sPaginationType": "bootstrap",
    });
  });
  </script>
  <script>

$( document ).ready(function() {
getsections();

 $('#class').on('change',function() {
    getsections();
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
      // $('#section').append($('<option>').text("--Select Section--").attr('value',""));
      var options = [];
        $.each(data, function(i, section) {
          //console.log(section);
            var opt="<option value='"+section.id+"'>"+section.name + " </option>"
          //console.log(opt);
          //$('#section').append(opt);
          options.push(opt);

        });
        $("#section").html(options).selectpicker('refresh');
        //console.log(data);
      },
      type: 'GET'
    });
};

</script>
  @stop
