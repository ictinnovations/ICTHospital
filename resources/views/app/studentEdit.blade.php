@extends('layouts.master')
@section('style')
<link href="/css/bootstrap-datepicker.css" rel="stylesheet">
<style>
b {color:red}
</style>
@stop
@section('content')

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
              <form role="form" action="{{url('/student/update')}}" method="post" enctype="multipart/form-data">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="id" value="{{ $student->id }}">
                  <input type="hidden" name="oldphoto" value="{{ $student->photo }}">
                <div class="row">
                        <div class="col-md-12">
                            <h3 class="text-info"> Guardian's Detail</h3>
                            <hr>
                        </div>
                      </div>
                        <div class="row">
                          <div class="col-md-12">
                          <div class="col-md-4">
                            <div class="form-group">
                                <label for="fatherCellNo">Father's Mobile No  <b>*</b></label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                    <input type="text" class="form-control" value="{{$student->fatherCellNo}}" required name="fatherCellNo" placeholder="03000000000" readonly>
                                </div>
                            </div>
                            </div>
                        <div class="col-md-4">
                          <div class="form-group">
                              <label for="fatherName">Father's Name <b>*</b></label>
                              <div class="input-group">
                                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                  <input type="text" class="form-control" value="{{$student->fatherName}}"  required  name="fatherName" placeholder="Name" readonly>
                              </div>
                          </div>
                          </div>

                          <div class="col-md-4">
                            <div class="form-group">
                                <label for="localGuardianCell">local Guardian Mobile No </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                    <input type="text" class="form-control"  value="{{$student->localGuardianCell}}"  name="localGuardianCell" placeholder="03000000000">
                                </div>
                            </div>
                            </div>
                          
                            
                  </div>
                </div>

                


                <div class="row">
                  <div class="col-md-12">
                      <h3 class="text-info"> Acdemic Details</h3>
                      <hr>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-4">
                        <div class="form-group">
                      <label for="regiNo">Registration No <b>*</b></label>
                      <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                          <input type="text" class="form-control" readyonly="true" required name="regiNo" value="{{$student->regiNo}}" placeholder="" readonly>
                      </div>
                  </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                      <label for="rollNo">Card/Roll No <b>*</b></label>
                      <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                          <input type="text" class="form-control" required name="rollNo" value="{{$student->rollNo}}" placeholder="Class roll no" readonly>
                      </div>
                  </div>
                    </div>
                    <div class="col-md-4">
                      {{--<div class="form-group ">
                                       <label for="session">session <b>*</b></label>
                                           <div class="input-group">

                                            <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                                              <input type="text" value="{{$student->session}}"  class="form-control datepicker2" name="session" required  data-date-format="yyyy">
                                          </div>
                                   </div>--}}
                      <input type="hidden" value="{{get_current_session()->id}}"  class="form-control" name="session" required  data-date-format="yyyy">

                      </div>
                      </div>

                </div>

                <div class="row">
                  <div class="col-md-12">
                <div class="col-md-4">
                  <div class="form-group">
                  <label class="control-label" for="class">Class <b>*</b></label>

                  <div class="input-group">
                      <span class="input-group-addon"><i class="glyphicon glyphicon-home blue"></i></span>
                         {{ Form::select('class',$classes,$student->class,['class'=>'form-control','id'=>'class','required'=>'true'])}}

                  </div>
                </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                  <label class="control-label" for="group">Group <b>*</b></label>

                  <div class="input-group">
                      <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                        {{ Form::select('group',['All'=>'N/A','Science'=>'Science','Arts'=>'Arts','Commerce'=>'Commerce'],$student->group,['class'=>'form-control','required'=>'true'])}}


                  </div>
                </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                    <label class="control-label" for="section">Section <b>*</b></label>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                        <?php  $data=[
                          'A'=>'A',
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
                      <select id="section" name="section" class="form-control" required="true">
                    @foreach($sections as $sec)
                      <option value="{{$sec->id}}" @if($sec->id==$student->section)  selected @endif>{{$sec->name}}</option>
                      @endforeach
                     
                    </select>


                         <?php /* {{ Form::select('section',$sections,$student->section,['class'=>'form-control','id'=>'section','required'=>'true'])}}*/ ?>


              
            
             <?php /*  <select name="section" id="section" required="true" class="form-control" >
                @if ($sections->count())
               @foreach($sections as $section)
            <option value="{{ $section->name }}"  <?php if ($student->section == $section->name){ echo "selected"; } ?> >{{ $section->name }}</option>    
           @endforeach
           @endif
          </select> */ ?>

                    </div>
                  </div>
                    </div>
              </div>
            </div>

             <div class="row">
             
             	<div class="col-md-4">
                <div class="form-group ">
                  <label for="session">Monthly Fee Discount</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i> </span>
                    <input type="text" id="discount_id" value="{{$student->discount_id}}" class="form-control" name="discount_id">
                  </div>
                </div>
              </div>

              <!-- <div class="col-md-12">
                <div class="col-md-4">
                  <div class="form-group">
                  <label class="control-label" for="shift">Shift</label>

                  <div class="input-group">
                      <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                      <?php /* $data=[
                        'Day'=>'Day',
                        'Morning'=>'Morning'
                        ];*/?>
                        {{ Form::select('shift',$data,$student->shift,['class'=>'form-control','required'=>'true'])}}


                  </div>
                </div>
                  </div>
                </div>
              </div>
-->
          <input type="hidden" value="Morning" name="shift" >

            <div class="row">
              <div class="col-md-12">
                  <h3 class="text-info"> Student's Detail</h3>
                  <hr>
              </div>
            </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="col-md-6">
                      <div class="form-group">
                          <label for="fname">First Name <b>*</b></label>
                          <div class="input-group">
                              <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                              <input type="text" class="form-control" value="{{$student->firstName}}" required name="fname" placeholder="First Name">
                          </div>
                      </div>
                    </div>
                   <!--  <div class="col-md-4">

                        <div class="form-group">
                            <label for="mname">Midle Name</label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                <input type="text" class="form-control" value="{{$student->middleName}}"  name="mname" placeholder="Midle Name">
                            </div>
                        </div>
                    </div> -->
                    <input type="hidden" class="form-control" value="{{$student->middleName}}"  name="mname" placeholder="Midle Name">

                    <div class="col-md-6">
                      <div class="form-group">
                          <label for="lname">Last Name</label>
                          <div class="input-group">
                              <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                              <input type="text" class="form-control" value="{{$student->lastName}}"  name="lname" placeholder="Last Name">
                          </div>
                      </div>
                      </div>

                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="col-md-4">
                        <div class="form-group">
                        <label class="control-label" for="gender">Gender</label>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <?php  $data=[
                              'Male'    =>'Male',
                              'Female'  =>'Female',
                              'Other'   =>'Other'

                              ];?>
                              {{ Form::select('gender',$data,$student->gender,['class'=>'form-control','required'=>'true'])}}

                        </div>
                      </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group ">
                                             <label for="dob">Date Of Birth </label>
                                                 <div class="input-group">

                                                  <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                                                    <input type="text" value="{{$student->dob}}"  class="form-control datepicker" name="dob"   data-date-format="dd/mm/yyyy">
                                                </div>


                                         </div>
                            </div>

                            <div class="col-md-4">
                              <div class="form-group">
                                <label for="remarks"> B-form/Cnic </label>
                                <div class="input-group">
                                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                  <input type="text" class="form-control b_form" value="{{$student->b_form}}"    name="b_form" placeholder="B-form/Cnic">
                                </div>
                              </div>
                           </div>
                        

                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        

                          

                            <div class="col-md-4">
                              <div class="form-group ">
                              <label for="photo">Photo</label>
                              <input id="photo" name="photo"  type="file">
                              </div>
                            </div>
                            <div class="col-md-6">
                        <div class="form-group">
                            <label for="presentAddress">Present Address <b>*</b></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker blue"></i></span>
                                <textarea type="text" class="form-control" required name="presentAddress" placeholder="Address">{{$student->presentAddress}}</textarea>
                            </div>
                        </div>
                        </div>

                    </div>
                  </div>
                  

                  <div class="row">
                    <div class="col-md-12">
                      <h3 class="text-info">Other Details</h3>
                      <hr>
                    </div>
                  </div>

              <div class="row">
                <div class="col-md-12">
                <div class="col-md-4">
                          <div class="form-group">
                          <label class="control-label" for="religion">Religion</label>

                          <div class="input-group">
                              <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                              <?php  $data=[
                                'Islam'=>'Islam',
                                'Hindu'=>'Hindu',
                                'Cristian'=>'Cristian',
                                'Buddhist'=>'Buddhist',
                                  'Other'=>'Other'

                                ];?>
                                {{ Form::select('religion',$data,$student->religion,['class'=>'form-control'])}}

                          </div>
                        </div>
                          </div>
                      <div class="col-md-4">
                        <div class="form-group">
                        <label class="control-label" for="bloodgroup">Bloodgroup</label>

                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <?php  $data=[
                              'A+'=>'A+',
                              'A-'=>'A-',
                              'B+'=>'B+',
                              'B+'=>'B+',
                              'AB+'=>'AB+',
                              'AB-'=>'AB-',
                              'O+'=>'O+',
                              'O-'=>'O-',

                              ];?>
                              {{ Form::select('bloodgroup',$data,$student->bloodgroup,['class'=>'form-control'])}}

                        </div>
                      </div>
                        </div>

                        <div class="col-md-4">
                          <div class="form-group">
                              <label for="nationality">Nationality</label>
                              <div class="input-group">
                                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                  <input type="text" class="form-control" value="{{$student->nationality}}"   name="nationality" placeholder="Nationality">
                              </div>
                          </div>
                        </div>

                         <div class="col-md-4">
                        <div class="form-group">
                            <label for="extraActivity">Extra Curicular Activity </label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                <input type="text" class="form-control" value="{{$student->extraActivity}}"  name="extraActivity" placeholder="Sport,Writing,etc">
                            </div>
                        </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                              <label for="remarks">Remarks </label>
                              <div class="input-group">
                                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                  <input type="text" class="form-control"  value="{{$student->remarks}}"   name="remarks" placeholder="Remarks">
                              </div>
                          </div>
                          </div>
                </div>
              </div> 
                
              {{--<div class="row">
                <div class="col-md-12">
                    <h3 class="text-info"> Address Detail</h3>
                    <hr>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                          
                        <div class="col-md-6">
                          <div class="form-group">
                              <label for="parmanentAddress">Parmanent Address</label>
                              <div class="input-group">
                                  <span class="input-group-addon"><i class="glyphicon glyphicon-map-marker blue"></i></span>
                                  <textarea type="text" class="form-control"  name="parmanentAddress" placeholder="Address">{{$student->parmanentAddress}}</textarea>
                              </div>
                          </div>
                          </div>
              </div>
            </div>--}}


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

