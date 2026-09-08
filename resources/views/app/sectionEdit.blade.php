@extends('layouts.master')
<style>
b {color:red}
</style>
@section('content')
<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-home"></i> Section Edit</h2>

            </div>
            <div class="box-content">
              @if (isset($section))
              <form role="form" action="{{url('/section/update')}}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                   <input type="hidden" name="id" value="{{$section->id }}">
                      <div class="form-group">
                        <label for="name">Name <b>*</b></label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <input type="text" class="form-control" required name="name" value="{{old('name',$section->name)}}" placeholder="Class Name">
                        </div>
                    </div>
                    
                 <div class="form-group">
                    <!--  <label for="name">Numeric Value of Class[One=1,Six=6,Ten=10 etc]</label>-->
                      <label for="name">Class <b>*</b></label>
                      <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                          <!--<input type="number" min="1" max="10" class="form-control" required name="code" placeholder="One=1,Six=6,Ten=10 etc">-->
                          
                          <select class="form-control"  name="class" required >
                          <option value="">---Select Class---</option>
                           @foreach($class as $cls)
                             <option value="{{$cls->code }}" @if($cls->code==old('class',$section->class_code)) selected @endif>{{ $cls->name}}</option>
                             @endforeach
                          </select>
                      </div>
                  </div>
                        
                     <div class="form-group">
                    <!--  <label for="name">Numeric Value of Class[One=1,Six=6,Ten=10 etc]</label>-->
                      <label for="name">Teachers <b>*</b></label>
                      <div class="input-group">
                          <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                          <!--<input type="number" min="1" max="10" class="form-control" required name="code" placeholder="One=1,Six=6,Ten=10 etc">-->
                          
                          <select class="form-control"  name="teacher_id" required >
                          <option value="">---Select Class---</option>
                           @foreach($teachers as $teacher)
                             <option value="{{$teacher->id }}" @if($teacher->id==old('teacher_id',$section->teacher_id)) selected @endif>{{ $teacher->firstName}} {{$teacher->lastName}}</option>
                             @endforeach
                          </select>
                      </div>
                  </div>
                    
                    
                    
                    
                    

                    <div class="form-group">
                        <label for="name">Description </label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <textarea type="text" class="form-control" required name="description" placeholder="Class Description">{{old('description',$section->description)}}</textarea>
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
                    <button class="btn btn-primary pull-right" type="submit"><i class="glyphicon glyphicon-check"></i>Update</button>
                    <br>
                  </div>
                </form>
                @else
                        <div class="alert alert-danger">
                            <strong>Whoops!</strong>There is no such Class!<br><br>
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
