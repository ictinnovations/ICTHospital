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
    <strong>Process Success.</strong> {{ Session::get('success')}}<br><a href="/subject/list">View List</a><br>

</div>
@endif
<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-book"></i> Subject Create</h2>

            </div>
            <div class="box-content">
                                    <div class="row">
                                <div class="col-md-12">
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
                                    </div>
                                  </div>

                            <form role="form" action="{{url('/subject/create')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="row">
                      <div class="col-md-12">
                          <h3 class="text-info"> Subject Details</h3>
                          <hr>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="col-md-4">
                          <div class="form-group">
                              <label for="name">Code <b>*</b></label>
                              <div class="input-group">
                                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                  <input type="text" class="form-control" value="{{old('code')}}" autofocus required name="code" placeholder="Subject Code">
                              </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                              <label for="name">Name <b>*</b></label>
                              <div class="input-group">
                                  <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                  <input type="text" class="form-control" value="{{old('name')}}" required name="name" placeholder="Subject Name">
                              </div>
                          </div>
                        </div>
                        <div class="col-md-4" style="display:none">
                          <div class="form-group">
                          <label class="control-label" for="type">Type <b>*</b></label>

                          <div class="input-group">
                              <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign  blue"></i></span>
                              <select name="type" class="form-control" required>
                              <option value="Core" @if(old('type')=='Core') selected @else selected @endif>Core</option>
                                <option value="Comprehensive"  @if(old('type')=='Comprehensive') selected @endif>Comprehensive</option>
                                <option value="Electives"  @if(old('type')=='Electives') selected @endif>Electives</option>
                              </select>
                          </div>
                      </div>
                        </div>

                                    </div>
                    </div>

                    <div class="row">
                      <div class="col-md-12">
                          <div class="col-md-2" style="display:none">
                              <div class="form-group">
                                  <label class="control-label" for="stdgroup">Subject Group <b>*</b></label>
                                  <div class="input-group">
                                      <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign  blue"></i></span>
                                      <select name="subgroup" class="form-control" required >
                                          <option value="N/A" selected>N/A</option>
                                          <option value="Bangla"  @if(old('subgroup')=='Bangla') selected @endif>Urdu</option>
                                          <option value="English"  @if(old('subgroup')=='English') selected @endif>English</option>


                                      </select>
                                  </div>
                              </div>
                          </div>
                    <div class="col-md-3">
                      <div class="form-group">
                            <label class="control-label" for="stdgroup">Student Group <b>*</b></label>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign  blue"></i></span>
                                <select name="stdgroup" class="form-control" required >
                                  <option value="N/A" >N/A</option>
                                  <option value="Science" @if(old('stdgroup')=='Science') selected @endif>Science</option>
                                  <option value="Arts" @if(old('stdgroup')=='Arts') selected @endif>Arts</option>
                                  <option value="Commerce" @if(old('stdgroup')=='Commerce') selected @endif>Commerce</option>

                                </select>
                            </div>
                        </div>
                    </div>
                      <div class="col-md-4">
                            <div class="form-group">
                                            <label class="control-label" for="class">Class <b>*</b></label>

                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-home blue"></i></span>
                                                <select name="class[]" class="form-control selectpicker" multiple data-hide-disabled="true" data-size="5"required >
                                                  @foreach($classes as $class)
                                                    <option value="{{$class->code}}"  @if(old('class.*')==$class->code) selected @endif >{{$class->name}}</option>
                                                  @endforeach

                                                </select>
                                            </div>
                                        </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group">
                        <label for="for">Grade System <b>*</b></label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <select name="gradeSystem" required class="form-control">
                              @if($gpa)
                             @foreach($gpa as $gp)
                              <option  value="{{$gp->for}}"> @if($gp->for=="1") 100 Marks @elseif($gp->for=="3") 75 Marks  @elseif($gp->for=="2") 50 Marks  @elseif($gp->for=="4") 30 Marks  @elseif($gp->for=="5") 25 Marks  @elseif($gp->for=="6") 20 Marks @elseif($gp->for=="7") 15 Marks @elseif($gp->for=="8") 10 Marks @endif </option>
                              <!--<option value="2">50 Marks </option>-->
                            @endforeach
                            @endif
                            </select>
                        </div>
                    </div>
                      </div>
                  </div>
                </div>
               <div style="display:none">
                <div class="row">
                  <div class="col-md-12">
                      <h3 class="text-info">Exam Details</h3>
                      <hr>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12">
                      <div class="col-md-3"></div>
                <div class="col-md-3">
                  <label>Full Marks</label>
                </div>
                  <div class="col-md-1"></div>
                <div class="col-md-3">
                <label>Pass Marks</label>
                </div>
                  <div class="col-md-2"></div>
                </div>
               </div>
                 <div class="row">
                 <div class="col-md-12">
  <div class="col-md-2"></div>
               <div class="col-md-4">
                        <div class="form-group">
                      <label for="totalfull" class="col-md-3 control-label">Total: </label>
                        <div class="col-md-3">
                            <input type="text" class="form-control" value="0" name="totalfull"  placeholder="0">
                            </div>
                            </div>
               </div>
               <div class="col-md-4">
                 <div class="form-group">
               <label for="totalpass" class="col-md-3 control-label">Total: </label>
                 <div class="col-md-3">
                     <input type="text" class="form-control" required value="0" name="totalpass"  placeholder="0">
                     </div>
                     </div>
               </div>
  <div class="col-md-2"></div>
               </div>
              </div>
              <div class="row">
                <div class="col-md-12">
 <div class="col-md-2"></div>
              <div class="col-md-4">
                       <div class="form-group">
                     <label for="wfull" class="col-md-3 control-label">Written: &nbsp;</label>
                       <div class="col-md-3">
                           <input type="text" class="form-control" name="wfull" value="0" required="true"  placeholder="0">
                           </div>
                           </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
              <label for="wpass" class="col-md-3 control-label">Written: &nbsp;</label>
                <div class="col-md-3">
                    <input type="text" class="form-control" required name="wpass" value="0"  placeholder="0">
                    </div>
                    </div>
              </div>
 <div class="col-md-2"></div>
              </div>
             </div>
             <div class="row">
               <div class="col-md-12">
<div class="col-md-2"></div>
             <div class="col-md-4">
                      <div class="form-group">
                    <label for="mfull" class="col-md-3 control-label">MCQ: </label>
                      <div class="col-md-3">
                          <input type="text" class="form-control" name="mfull" value="0" required="true" placeholder="0">
                          </div>
                          </div>
             </div>
             <div class="col-md-4">
               <div class="form-group">
             <label for="mpass" class="col-md-3 control-label">MCQ: </label>
               <div class="col-md-3">
                   <input type="text" class="form-control" required name="mpass" value="0"  placeholder="0">
                   </div>
                   </div>
             </div>
<div class="col-md-2"></div>
             </div>
            </div>
            <div class="row">
              <div class="col-md-12">
<div class="col-md-2"></div>
            <div class="col-md-4">
                     <div class="form-group">
                   <label for="sfull" class="col-md-3 control-label">SBA: </label>
                     <div class="col-md-3">
                         <input type="text" class="form-control" name="sfull" value="0" required="true" placeholder="0">
                         </div>
                         </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
            <label for="spass" class="col-md-3 control-label">SBA: </label>
              <div class="col-md-3">
                  <input type="text" class="form-control" required name="spass"  value="0" placeholder="0">
                  </div>
                  </div>
            </div>
<div class="col-md-2"></div>
            </div>
           </div>
           <div class="row">
             <div class="col-md-12">
           <div class="col-md-2"></div>
           <div class="col-md-4">
                    <div class="form-group">
                  <label for="pfull" class="col-md-3 control-label">Practical:&nbsp; </label>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="pfull" value="0" required="true"  placeholder="0">
                        </div>
                        </div>
           </div>
           <div class="col-md-4">
             <div class="form-group">
           <label for="ppass" class="col-md-3 control-label">Practical:&nbsp;</label>
             <div class="col-md-3">
                 <input type="text" class="form-control" name="ppass"  value="0" placeholder="0">
                 </div>
                 </div>
           </div>
           </div>
           <div class="col-md-2"></div>
           </div>
           </div>

                <div class="row">
                <div class="col-md-12">

                    <button class="btn btn-primary pull-right" type="submit"><i class="glyphicon glyphicon-plus"></i>Add</button>

                  </div>
                </div>
                </form>






        </div>
    </div>
</div>
</div>
@stop
