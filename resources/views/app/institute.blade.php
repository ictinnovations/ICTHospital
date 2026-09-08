@extends('layouts.master')
@section('style')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@stop
@section('content')
    @if (Session::get('success'))
        <div class="alert alert-success">
            <button data-dismiss="alert" class="close" type="button">×</button>
            <strong>Process Success.</strong> {{ Session::get('success')}}

        </div>
    @endif
    @if (Session::get('error'))
        <div class="alert alert-danger">
          <strong>Alert!!!</strong> {{ Session::get('error')}}

        </div>
    @endif
    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-cog"></i> Institute Info</h2>

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

                        <form role="form" action="{{url('/institute')}}" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="row">
                                <div class="col-md-12">

                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label for="type">Name</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text" class="form-control" required name="name" placeholder="Institute Name" value="{{$institute->name}}">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="type">Establish</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text" class="form-control" required name="establish" placeholder="1990" value="{{$institute->establish}}">

                                            </div>
                                        </div>
                                    </div>
                                  </div>
                              </div>
                                    <div class="row">
                                        <div class="col-md-12">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Web Stie</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text"  class="form-control" required name="web" placeholder="www.ictinnovations.com" value="{{$institute->web}}">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Email</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="email" class="form-control" required name="email" placeholder="admin@shanixlab.com" value="{{$institute->email}}">

                                            </div>
                                        </div>
                                    </div>
                                    </div>

                                </div>
                            <div class="row">

                                <div class="col-md-12">
                                  <div class="col-md-5">
                                      <div class="form-group">
                                          <label for="type">Phone/Mobile No</label>
                                          <div class="input-group">
                                              <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                              <input type="text" class="form-control" required name="phoneNo" placeholder="+8801554322707" value="{{$institute->phoneNo}}">

                                          </div>
                                      </div>
                                  </div>
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <label for="type">Address</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <textarea type="text" class="form-control" required name="address" placeholder="Address">{{$institute->address}}</textarea>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Logo</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="file" class="form-control"  name="logo">

                                            </div>
                                        </div>
                                    </div>
                            </div>


                          @if(Auth::user()->login=='ictkashif')
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="type">Grade System</label>
<!--                                                <input data-no-uniform="true" name="grade_system"  @if($gradsystem=='' || $gradsystem=='auto') checked @endif type="checkbox" class="iphone-toggle">
 -->                                        
                                                <div class="input-group">
                                                <input class="chb form-control" data-toggle="toggle" id="grade_system" data-on="Auto" data-off="Manual" data-width="100"   name="grade_system" data-onstyle="danger" data-offstyle="success" type="checkbox" @if($gradsystem=='' || $gradsystem=='auto') checked @endif  >                                            
                                            </div>
                                    </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="type">Family</label>
                                                <input   id="toggle-one" @if($family=='on') checked @endif name="family" type="checkbox">                                        
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="type">Accounting</label>
                                              <!-- <input data-no-uniform="true" name="accounting"  @if($accounting=='' || $accounting=='yes') checked @endif type="checkbox" class="iphone-toggle">-->
                                            <div class="input-group">
                                                <input class="chb form-control" data-toggle="toggle" id="accounting" data-on="No" data-off="Yes" data-width="100"   name="accounting" data-onstyle="danger" data-offstyle="success" type="checkbox" @if($accounting=='' || $accounting=='no') checked @endif  >                                            
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                            </div>
                                
                            @endif
                            <button class="btn btn-primary pull-right" type="submit"><i class="glyphicon glyphicon-check"></i> Save</button>
                            <br>
                            <br>
                        </form>

                </div>








            </div>
        </div>
    </div>
@stop
@section('script')
<script type="text/javascript">
$(function() {
    $('#toggle-one').bootstrapToggle();
  })
    iOSCheckbox.defaults.checkedLabel='Auto';
    iOSCheckbox.defaults.uncheckedLabel='Manual';
</script>
@stop
