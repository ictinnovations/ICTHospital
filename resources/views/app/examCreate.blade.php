@extends('layouts.master')
@section('style')
<style>
b {color:red}
</style>
@stop
@section('content')
@if (Session::get('success'))

<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
    <strong>Process Success.</strong> {{ Session::get('success')}}<br><a href="/exam/list">View List</a><br>

</div>
@endif
<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-home"></i> Exam Create</h2>

            </div>
            <div class="box-content">
              <form role="form" action="{{url('/exam/create')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <div class="form-group">
                        <label for="name">Exam Type  <b>*</b></label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <input type="text" class="form-control" autofocus required name="type" value="{{old('type')}}" placeholder="Type">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name">Class <b>*</b></label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                           <select name="class"  id="class" class="form-control" required>
                      @foreach($classes as $class)
                      <option value="{{$class->code}}" @if(old('class')==$class->code) selected @endif>{{$class->name}}</option>
                      @endforeach

                    </select>
                        </div>
                    </div>

                     <div class="form-group">
                        <label for="name">Section  <b>*</b></label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                           <select id="section" name="section[]" class="form-control selectpicker" id="section" multiple data-actions-box="true" data-hide-disabled="true" data-size="5"  required="true">
                  <?php /*  @foreach($section as $sec)
                      <option value="{{$sec->id}}">{{$sec->name}}</option>
                      @endforeach*/?>
                      <option value="">--Select Section--</option>
                    </select>
                        </div>
                    </div>
                    <div class="clearfix"></div>
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
                                <div class="form-group">
                    <button class="btn btn-primary pull-right" type="submit"><i class="glyphicon glyphicon-plus"></i>Add</button>
                    <br>
                  </div>
                </form>

        </div>
    </div>
</div>
</div>
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
