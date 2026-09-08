@extends('layouts.master')
@section('style')
<link href="/css/bootstrap-datepicker.css" rel="stylesheet">
@stop
@section('content')



<?php 
if($student->family_id==''){

  //$student->family_id =   hexdec(substr(uniqid(rand(), true), 5, 5));
}
?>
<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-user"></i> Student New Admission</h2>

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
              @if (isset($student))
              <form role="form" action="{{url('/family/update')}}" method="post" enctype="multipart/form-data">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="id" value="{{ $student->id }}">
                  <input type="hidden" name="oldphoto" value="{{ $student->photo }}">
                  <input type="hidden" name="family_id" value="{{ $family_id }}">
                
                    
                    <div class="row">
                      <div class="col-md-12">
                                <div class="col-md-12">
                              <div class="form-group">
                                  <label for="presentAddress">Family Id </label>
                                  <div class="input-group">
                                      <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker blue"></i></span>
                                      <input type="text" class="form-control" required name="adfamily_id" placeholder="" value="{{$student->family_id}}">
                                  </div>
                              </div>
                              </div>
                    </div>
                  </div>
                  <div class="row">
                      <div class="col-md-12">
                                <div class="col-md-12">
                              <div class="form-group">
                                  <label for="presentAddress">Father Name</label>
                                  <div class="input-group">
                                      <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker blue"></i></span>
                                      <input type="text" class="form-control" required name="f-name" placeholder="" value="{{$student->fatherName}}">
                                  </div>
                              </div>
                              </div>
                    </div>
                  </div>
                  <div class="row">
                      <div class="col-md-12">
                                <div class="col-md-12">
                              <div class="form-group">
                                  <label for="presentAddress">Phone Number </label>
                                  <div class="input-group">
                                      <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker blue"></i></span>
                                      <input type="text" class="form-control" required name="cell_phone" placeholder="" value="{{$student->fatherCellNo}}">
                                  </div>
                              </div>
                              </div>
                    </div>
                  </div>


                  <div class="row">
                    <div class="col-md-12">
                              <div class="col-md-12">
                            <div class="form-group">
                                <label for="presentAddress">About Family Behavior </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker blue"></i></span>
                                    <textarea type="text" class="form-control" required name="familb" placeholder="About Family Behavior ">{{$student->about_family}}</textarea>
                                </div>
                            </div>
                            </div>
                            
                  </div>
                </div>


                    <div class="clearfix"></div>

                                <div class="form-group">
                    <button class="btn btn-primary pull-right" type="submit"><i class="glyphicon glyphicon-check"></i>Update</button>
                    <br>
                  </div>
                </form>
              @else
                      <div class="alert alert-danger">
                          <strong>Whoops!</strong>There is no such Student!<br><br>
                          <ul>
                              @foreach ($errors->all() as $error)
                                  <li>{{ $error }}</li>
                              @endforeach
                          </ul>
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

    $( document ).ready(function() {
         $('.b_form').mask('00000-0000000-0');
      $('.datepicker').datepicker({autoclose:true});
      $(".datepicker2").datepicker( {
    format: " yyyy", // Notice the Extra space at the beginning
    viewMode: "years",
    minViewMode: "years",
    autoclose:true
});
//getsections();
 
  $('#class').on('change',function() {
    getsections();
  });

    });


function getsections()
{
    var aclass = $('#class').val();
    //alert(aclass);
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

