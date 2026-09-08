@extends('layouts.master')
@section('style')
<link href="{{url('/css/bootstrap-datepicker.css')}}" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
b {color:red}
#errmsg
{
color: red;
}
</style>
@stop
@section('content')
@if (Session::get('success'))
<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
  <strong>Process Success.</strong> {{ Session::get('success')}}<br><a href="/student/list">View List</a><br>
</div>
@endif
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
        @if(family_check()=='on')
        <div class="btn-group ">
          
                <form class="navbar-search" name="navbar_search" action="{{url('/student/list')}}" id="navbar_search" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="search" value="yes">
                    <input placeholder="Search Family by Phone Number" class="search-query form-control col-md-10" name="student_name" id="family_name" 
                    type="hidden" autocomplete="off">
                    <div id="familyListd">
                    </div>
                </form>
        </div>
        @endif
        <br>
        <br>
        <form role="form" action="{{url('/student/create')}}" method="post" enctype="multipart/form-data">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
         
             <div class="row">
          <div class="col-md-12">
            <h3 class="text-info">School Information</h3>
            <hr>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
          
            <div class="col-md-6">
              <div class="form-group">
                <label for="fatherName">Family Id </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control"  name="family_id" value="{{old('family_id',$family_id)}}" id="family_id" @if($family_id!='') readonly @endif>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="fatherCellNo">Refer by Family </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                <input type="text" class="form-control typeahead"   name="refer_by" value="{{old('refer_by')}}" id="refer_by"   placeholder="enter referal name or Id">

                    {{--<select class="form-control" id="refer_by"   name="refer_by">
                      <option value="">--- Select Refer By Family---</option>
                      @if($families)
                        @foreach($families as $family)
                          <option value="{{$family->family_id}}">{{ $family->fatherName }} ({{$family->family_id}})</option>
                        @endforeach
                      @endif
                    </select>--}}
                </div>
              </div>
            </div>
            
            <div class="col-md-8">
              <div class="form-group">
                <label for="presentAddress">About Family Behavior </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <textarea type="text" class="form-control"  name="familyc" placeholder="">{{old('presentAddress')}}</textarea>
                </div>
              </div>
            </div>



          </div>
        </div>


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
                <label for="fatherCellNo">Father's Mobile No <b>*</b></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control"  required name="fatherCellNo" value="{{old('fatherCellNo')}}" id="f_phone" autocomplete="off"  placeholder="03000000000">
                </div>
                &nbsp;<span id="errmsg"></span>
                
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="fatherName">Father's Name <b>*</b></label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control" required value="{{old('fatherName')}}"  name="fatherName" placeholder="Name" id="f_name">
                </div>
              </div>
            </div>
            
            
           <!--  <div class="col-md-4">
              <div class="form-group">
                <label for="presentAddress">Family Id</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control"  name="family_id" id="family_id" readonly>
                </div>
              </div>
            </div> -->
            <div class="col-md-4">
              <div class="form-group">
                <label for="localGuardianCell">local Guardian Mobile No </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control"  name="localGuardianCell" value="{{old('localGuardianCell')}}" placeholder="03000000000" id="g_phone">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <!-- <div class="col-md-4">
              <div class="form-group">
                <label for="motherName">Mother's Name </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control"   name="motherName" value="{{old('motherName')}}" placeholder="Name" id="m_name">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="motherCellNo">Mother's Mobile No </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control"  name="motherCellNo" value="{{old('motherCellNo')}}" placeholder="+8801xxxxxxxxx" id="m_phone">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="localGuardian">Local Guardian Name </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control"  name="localGuardian" value="{{old('localGuardian')}}" placeholder="Name" id="g_name">
                </div>
              </div>
            </div> -->
            

            {{--<div class="col-md-8">
              <div class="form-group">
                <label for="presentAddress">About Family Behavior </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <textarea type="text" class="form-control"  name="familyc" placeholder="">{{old('presentAddress')}}</textarea>
                </div>
              </div>
            </div>--}}

          </div>
          <input type="hidden" name="check" id="check">
        </div>
        



          
       


          <div class="row">
            <div class="col-md-12">
              <h3 class="text-info"> Acdemic Details</h3>
              <hr>
            </div>
          </div>
          <div class="row">
           
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label" for="class">Class <b>*</b></label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-home blue"></i></span>
                    <select name="class" id="class" class="form-control" required>
                      @foreach($classes as $class)
                      <option value="{{$class->code}}" @if(old('class')==$class->code)   selected @endif>{{$class->name}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
               <?php /* <div class="form-group">
                  <label class="control-label" for="section">Section</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="section" id="section" required="true" class="form-control" >
                     <option value="A">A</option>
                      <option value="B">B</option>
                      <option value="C">C</option>
                      <option value="D">D</option>
                      <option value="E">E</option>
                      <option value="F">F</option>
                      <option value="G">G</option>
                      <option value="H">H</option>
                      <option value="I">I</option>
                      <option value="J">J</option>
                    </select>
                  </div>
                </div>*/?>
                <div class="form-group">
                  <label class="control-label" for="student">Section <b>*</b></label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-book blue"></i></span>
                    <select id="section" name="section" class="form-control" required="true">
                  <?/*  @foreach($section as $sec)
                      <option value="{{$sec->id}}">{{$sec->name}}</option>
                      @endforeach*/?>
                      <option value=""></option>
                    </select>
                  </div>
                </div>
              </div>
              {{--<div class="col-md-4">
                <div class="form-group ">
                  <label for="session">session</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                    <input type="text" id="session" value="{{date('Y')}}" class="form-control datepicker2" name="session" required  data-date-format="yyyy">
                  </div>
                </div>
              </div>--}}
              <input type="hidden" id="session" value="{{get_current_session()->id}}" class="form-control datepicker2" name="session" required  data-date-format="yyyy">
            </div>
          
            <div class="row">
            <div class="col-md-12">
              <div class="col-md-4">
                <div class="form-group ">
                  <label for="session">Monthly Fee Discount</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <input type="text" id="discount_id" value="{{old('discount_id')}}" class="form-control" name="discount_id">
                  </div>
                </div>
              </div>
          
              <div class="col-md-4">
                <div class="form-group">
                  <label for="regiNo">Registration No  <b>*</b></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <input type="text" id="regiNo" readOnly class="form-control" required name="regiNo" value="{{old('regiNo')}}" placeholder="">
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="rollNo">Card/Roll No  <b>*</b></label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <input type="text" id="rollNo" class="form-control" required name="rollNo" value="{{old('rollNo')}}" placeholder="Class roll no">
                  </div>
                </div>
              </div>
              </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="group">Group</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="group" class="form-control" >
                      <option value="N/A"  >N/A</option>
                      <option value="Science" @if(old('group')=="Science")   selected @endif>Science</option>
                      <option value="Arts" @if(old('group')=="Arts")   selected @endif>Arts</option>
                      <option value="Commerce" @if(old('group')=="Commerce")   selected @endif>Commerce</option>


                    </select>


                  </div>
                </div>
              </div>

            
         <!-- <div class="row">
            <div class="col-md-12">
              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="shift">Shift</label>

                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="shift" required="true" class="form-control" >
                      <option value="Day">Day</option>
                      <option value="Morning">Morning</option>
                    </select>

                  </div>
                </div>
              </div>
            </div>
          </div>-->

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
                    <input type="text" class="form-control" required name="fname" value="{{old('fname')}}" placeholder="First Name">
                  </div>
                </div>
              </div>
              <!-- <div class="col-md-4">

                <div class="form-group">
                  <label for="mname">Midle Name</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <input type="text" class="form-control"  name="mname" value="{{old('mname')}}" placeholder="Midle Name">
                  </div>
                </div>
              </div> -->
                 <input type="hidden" class="form-control"  name="mname" value="{{old('mname')}}" placeholder="Midle Name">

              <div class="col-md-6">
                <div class="form-group">
                  <label for="lname">Last Name</label>
                  <div class="input-group">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <input type="text" class="form-control"  name="lname" value="{{old('lname')}}" placeholder="Last Name">
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
                    <select name="gender" class="form-control" required >

                      <option value="Male"   @if(old('gender')=="Male")   selected @endif>Male</option>
                      <option value="Female" @if(old('gender')=="Female") selected @endif>Female</option>
                      <option value="Other"  @if(old('gender')=="Other")  selected @endif>Other</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
              <div class="form-group ">
                <label for="dob">Date Of Birth </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i> </span>
                  <input type="date"   class="form-control db" name="dob" value="{{old('dob')}}"   data-date-format="dd/mm/yyyy">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="remarks"> B-form/Cnic </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control b_form" value="{{old('b_form')}}"  name="b_form" placeholder="B-form/Cnic">
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
                  <textarea type="text" class="form-control" required name="presentAddress" placeholder="Address">{{old('presentAddress')}}</textarea>
                </div>
              </div>
            </div>

          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            
            <div class="col-md-4">
              <div class="form-group ">
                <label for="photo">Admission Fee</label>
                <input id="adfee" name="adfee" class="form-control" value="{{old('adfee',0)}}"  type="text">
              </div>
            </div>
          </div>
        </div>
        <!-- <div class="row">
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
                  <textarea type="text" class="form-control"  name="parmanentAddress" placeholder="Address">{{old('parmanentAddress')}}</textarea>
                </div>
              </div>
            </div>
          </div>
        </div> -->

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
                    <select name="religion" class="form-control"  >
                      <option value="Islam" selected>Islam</option>
                      <option value="Hindu">Hindu</option>
                      <option value="Cristian">Cristian</option>
                      <option value="Buddhist">Buddhist</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-group">
                  <label class="control-label" for="bloodgroup">Bloodgroup</label>
                  <div class="input-group" >
                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                    <select name="bloodgroup" class="form-control"  >
                      <option value="A+"  @if(old('bloodgroup')=="A+") selected @endif>A+</option>
                      <option value="A-"  @if(old('bloodgroup')=="A-") selected @endif>A-</option>
                      <option value="B+"  @if(old('bloodgroup')=="B+") selected @endif>B+</option>
                      <option value="B-"  @if(old('bloodgroup')=="B-") selected @endif>B-</option>
                      <option value="AB+" @if(old('bloodgroup')=="AB+") selected @endif>AB+</option>
                      <option value="AB-" @if(old('bloodgroup')=="AB-") selected @endif>AB-</option>
                      <option value="O+"  @if(old('bloodgroup')=="O+") selected @endif>O+</option>
                      <option value="O-"  @if(old('bloodgroup')=="O-") selected @endif>O-</option>
                    </select>
                  </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-group">
                <label for="nationality">Nationality</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control" value="Pakistani"   name="nationality" placeholder="Nationality">
                </div>
              </div>
            </div>


            <div class="col-md-4">
              <div class="form-group">
                <label for="extraActivity">Extra Curicular Activity </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control"  name="extraActivity" value="{{old('extraActivity')}}" placeholder="Sport,Writing,etc">
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="remarks">Remarks </label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                  <input type="text" class="form-control"  name="remarks" value="{{old('remarks')}}" placeholder="Remarks">
                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="clearfix"></div>

        <div class="form-group">
          <button class="btn btn-primary pull-right" type="submit"><i class="glyphicon glyphicon-plus"></i>Add</button>
          <br>
        </div>
      </form>
    </div>
  </div>
</div>
</div>

@stop
@section('script')
<script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>
--}}
<script src="{{url('/js/bootstrap-datepicker.js')}}"></script>
<script type="text/javascript">
//alert({{get_current_session()->id}});
 //$( function() {
  
  /*$("#refer_by").keydown(function (event) {

      var refrl = $("#refer_by").val();
    $.ajax({
     url: "{{url('/get/refral')}}"+'/'+refrl,
     data: {
       //format: 'json'
     },
     error: function(error) {
      
     },
    // dataType: 'json',
     success: function(data) {

      //alert(data);
      var availableTags = [data];
      // $('#regiNo').val(data[0]);
      // $('#rollNo').val(data[1]);
      $( "#refer_by" ).autocomplete({
        source: availableTags
      });
     },
     type: 'GET'
   });





    var availableTags = [
      "ActionScript",
      "AppleScript",
      "Asp",
      "BASIC",
      "C",
      "C++",
      "Clojure",
      "COBOL",
      "ColdFusion",
      "Erlang",
      "Fortran",
      "Groovy",
      "Haskell",
      "Java",
      "JavaScript",
      "Lisp",
      "Perl",
      "PHP",
      "Python",
      "Ruby",
      "Scala",
      "Scheme"
    ];
    
  } );*/

      
     /*var refrl = $("#refer_by").val();
      var path  = "{{url('/get/refral')}}";
    $('input.typeahead').typeahead({
      //alert(refrl);

     
        source:  function (query, process) {

        return $.get(path+'/'+query, function (data) {
          console.log(9898);

                return process(data);

            });

        }

    });*/
$(document).ready(function() {


$(".db").datepicker( {
              //format: "yyyy/m", // Notice the Extra space at the beginning
             // viewMode: "years",
             // minViewMode: "years",
              autoclose:true

            })



  @if($family_id!='')

    getfamilydata({{$family_id}});
  @endif
 $( "#refer_by" ).autocomplete({
        //var refrl = $("#refer_by").val();
        source: function(request, response) {

            $.ajax({
            url: "{{url('/get/refral')}}"+'/'+request.term,
            data: {
                   // term : request.term
             },
            dataType: "json",
            success: function(data){
               var resp = $.map(data,function(obj){
                    console.log(obj.fatherName);
                    return obj.fatherName+'('+obj.family_id+')';
               }); 
 
               response(resp);
            }
        });
    },
    minLength: 1
 });


 $( "#family_id" ).autocomplete({
        //var refrl = $("#refer_by").val();
        source: function(request, response) {

            $.ajax({
            url: "{{url('/get/family_id/list')}}"+'/'+request.term,
            data: {
                   // term : request.term
             },
            dataType: "json",
            success: function(data){
               var resp = $.map(data,function(obj){
                    //console.log(obj.fatherName);

                   
                    return obj.fatherName+'('+obj.family_id+')';
               }); 
                 //$('#family_id').keyup();
                 //getfamilydata();
               response(resp);
            }
        });
    },
    minLength: 1,
     select: function(event, ui) {
        getfamilydata(ui.item.value);
           // alert(ui.item.value);
             }
 });
});

 var getStdRegiRollNo = function(){

 







   var aclass = $('#class').val();
  // var session = $('#session').val().trim();
   var session = {{get_current_session()->id}};
   var section=$('#section').val().trim();

     //  var section = $("#section option:selected").val();
    // alert(section);
   $.ajax({
     url: "{{url('/student/getRegi')}}"+'/'+aclass+'/'+session+'/'+section,
     data: {
       format: 'json'
     },
     error: function(error) {
      if(aclass=='' && session==''){
       alert(error);
      }
     },
     dataType: 'json',
     success: function(data) {
       $('#regiNo').val(data[0]);
       $('#rollNo').val(data[1]);
     },
     type: 'GET'
   });
 };

 function getsections()
{
    var aclass = $('#class').val();
   // alert(aclass);
    $.ajax({
      url: "{{url('/section/getList/')}}"+'/'+aclass,
      data: {
        format: 'json'
      },
      error: function(error) {
        alert("Please fill all inputs correctly!");
      },
      dataType: 'json',
      success: function(data) {
       $('#section').empty();
       $('#section').append($('<option>').text("--Select Section--").attr('value',""));
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

$( document ).ready(function() {
   $('.b_form').mask('00000-0000000-0');
  getStdRegiRollNo();
getsections();
 
  $('#class').on('change',function() {
    getsections();
  });
 
  $('.datepicker').datepicker({autoclose:true});
  $(".datepicker2").datepicker( {
    format: " yyyy", // Notice the Extra space at the beginning
    viewMode: "years",
    minViewMode: "years",
    autoclose:true
  }).on('changeDate', function (ev) {
    getStdRegiRollNo();
  });
  $('#class').on('change',function() {
    getStdRegiRollNo();
  });
  $('#section').on('change',function() {
    getStdRegiRollNo();
  });


});

$(document).ready(function(){

$("#f_phone").keydown(function (event) {

  if (!((event.keyCode == 46 || 
            event.keyCode == 8  || 
            event.keyCode == 37 || 
            event.keyCode == 39 || 
            event.keyCode == 9) || 
            $(this).val().length < 11 &&
            ((event.keyCode >= 48 && event.keyCode <= 57) ||
            (event.keyCode >= 96 && event.keyCode <= 105)))) {
            // Stop the event
            $("#errmsg").html("Digits Only and digits not more than 11").show().delay(5000).fadeOut("slow");
            event.preventDefault();
            return false;
        }
     //if the letter is not digit then display error and don't type anything
   // alert(e.length)
     /*if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) &&  $(this).val().length <=12) {
        //display error message
        $("#errmsg").html("Digits Only and digits not more than 12").show().fadeOut("slow");
               return false;
    }*/
    // var value = document.getElementById('f_phone').value;
     /*if (value.length <=12 ) {
        $("#errmsg").html("formate not correct please enter this formate 030000000").show().fadeOut("slow");
     }*/
   });
 $('#family_name').keyup(function(){ 
        var query = $('#family_name').val();
        if(query != '')
        {
         var _token = $('input[name="_token"]').val();
         $.ajax({
          url:"{{ url('family/search') }}",
          method:"POST",
          data:{query:query, _token:_token},
          success:function(data){
            //alert(32);
            $('#familyListd').fadeIn();  
            $('#familyListd').html(data);
          
          }
         });
        }
    });

     
     
    $('#familyListd').on('click', 'li', function() { 
       
         $('#family_name').val($(this).text());
         //alert($('#family_name').val($(this).text()));
         var father_name       = $(this).attr('data-father');
         var phone             = $(this).attr('data-phone');
         var mother_name       = $(this).attr('data-mother_name');
         var mother_phone      = $(this).attr('data-mother_phone');
         var localGuardian     = $(this).attr('data-localGuardian');
         var localGuardianCell = $(this).attr('data-localGuardianCell');
         var family_id         = $(this).attr('data-familyid');
         var check             = $(this).attr('data-check');
         $('#f_name').val(father_name);
         $('#f_phone').val(phone);
         $('#m_name').val(mother_name);
         $('#m_phone').val(mother_phone);
         $('#g_name').val(localGuardian);
         $('#g_phone').val(localGuardianCell);
         $('#check').val(check);
         $('#family_id').val(family_id);
         //alert($(this).attr('data-father'));  
          $('#familyListd').fadeOut(); 
         //$( "#navbar_search" ).submit(); 
    });


      $('#family_id').keyup(function(){ 
        getfamilydata($('#family_id').val());
      });
        //);

});
function getfamilydata(id){
     // $('#family_id').bind('click keyup', function() { 
        var query =id ;
        //alert(query);
        console.log(query);
        if(query != '')
        {
         var _token = $('input[name="_token"]').val();
         $.ajax({
          //url:"{{ url('get/family_id') }}",
          url:"{{ url('get/family/data') }}",
          method:"POST",
          data:{query:query, _token:_token},
          success:function(data){
            //alert(JSON.stringify(data));
           //$('#familyListd').fadeIn();  
             if(data.unique_code!='' && typeof(data.unique_code)!='undefined'){
               // $('#f_phone').val(data.unique_code);
                $('#f_phone').val(data.fatherphone);
                 $('#f_phone').attr("readonly", true);
             }else{
              $('#f_phone').val('');
               $('#f_phone').attr("readonly", false);
             }
             
            
             if(data.referalname!='' && typeof(data.referalname)!='undefined'){
               //alert(data.referalname);
              //alert(323);
              output = [];
              //output.push('<option value="'+ data.referalid +'" selected>'+ data.referalname +'</option>');
              $('#refer_by').val(data.referalname);
              $('#refer_by').attr("disabled", 'disabled');
             }else{
               $('#refer_by').attr("disabled", false);
             }
              if(data.fathername!='' && typeof(data.fathername)!='undefined'){
                  $('#f_name').val(data.fathername);
              }else{
                $('#f_name').val('');
              }
              if(data.fatherphone!='' && typeof(data.fatherphone)!='undefined'){

              }
              if(data.localg!='' && typeof(data.localg)!='undefined'){
                    $('g_phone').val(data.localg);
              }

         //  $('#familyListd').html(data);
          }
         });
        }
    }


</script>
@stop

