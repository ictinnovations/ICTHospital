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
                    <h2><i class="glyphicon glyphicon-cog"></i> Branches Info</h2>

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
                     <div class="form-group">
                        <button type="button" class="btn btn-primary btn-block btn-sm ml-1" onclick="addbranches()">Add Another Question</button>
                    </div>
                        <form role="form" action="{{url('/branch')}}" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div id="addbranches">
                            @if($countb>0)
                            @foreach($branches as $branch)
                            <div class="row" >
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Branch Name</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text" class="form-control" required name="branchname[]" placeholder="Branch Name" value="{{$branch->branch_name}}">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Url</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text" class="form-control" required name="url[]" placeholder="Branch url" value="{{$branch->branch_url}}">

                                            </div>
                                        </div>
                                    </div>
                                    
                                  </div>
                              </div>
                                    <div class="row">
                                        <div class="col-md-12">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Username</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text"  class="form-control" required name="username[]" placeholder="Enter Username" value="{{$branch->username}}">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Password</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="password" class="form-control" required name="password[]"  value="{{$branch->password}}">

                                            </div>
                                        </div>
                                    </div>
                                    </div>

                                </div>
                                <hr>
                                @endforeach
                                @else
                                <div class="row" >
                                <div class="col-md-12">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Branch Name</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text" class="form-control" required name="branchname[]" placeholder="Branch Name" value="">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Url</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text" class="form-control" required name="url[]" placeholder="Branch url" value="">

                                            </div>
                                        </div>
                                    </div>
                                    
                                  </div>
                              </div>
                                    <div class="row">
                                        <div class="col-md-12">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Username</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text"  class="form-control" required name="username[]" placeholder="Enter Username" value="">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Password</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="password" class="form-control" required name="password[]"  value="">

                                            </div>
                                        </div>
                                    </div>
                                    </div>

                                </div>
                                @endif
                                </div>
                            
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
    <script src="{{ URL::asset('js/jquery.validate.min.js') }}"></script>

<script type="text/javascript">
var newId = 1;
    var template = jQuery.validator.format(`
        <div class="row" >
                                <div class="col-md-12">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Branch Name</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text" class="form-control" required name="branchname[]" placeholder="Branch Name" value="">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Url</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text" class="form-control" required name="url[]" placeholder="Branch url" value="">

                                            </div>
                                        </div>
                                    </div>
                                    
                                  </div>
                              </div>
                                    <div class="row">
                                        <div class="col-md-12">

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Username</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text"  class="form-control" required name="username[]" placeholder="Enter Username" value="">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Password</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="password" class="form-control" required name="password[]"  value="">

                                            </div>
                                        </div>
                                    </div>
                                    </div>

                                </div>
        <hr>
    `);

function addbranches(){
        $('#addbranches').append(template(newId));
        newId++;
    }
$(function() {
    $('#toggle-one').bootstrapToggle();
  })
    iOSCheckbox.defaults.checkedLabel='Auto';
    iOSCheckbox.defaults.uncheckedLabel='Manual';
</script>
@stop
