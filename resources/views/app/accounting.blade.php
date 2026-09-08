@extends('layouts.master')
@section('style')
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
                    <h2><i class="glyphicon glyphicon-cog"></i> Accounting Integration</h2>

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

                        <form role="form" action="{{url('/accounting')}}" method="post">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            
                      
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="col-md-10" id="ictcore">
                                        <div class="form-group">
                                            <label for="type">Company Id</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text" class="form-control"  name="company_id" placeholder="Company Id" value="{{$accounting->company_id}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label for="type">Api Link</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text" class="form-control" required name="api_link" placeholder="Api Link" value="{{$accounting->api_link}}">

                                            </div>
                                        </div>
                                    </div>
                                  </div>
                              </div>
                                       <div class="row">
                                                <div class="col-md-12">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="type">User Name</label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                        <input type="text"  class="form-control" required name="username" placeholder="User Name" value="{{$accounting->username}}">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type">Password</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="password"  class="form-control" required name="password" placeholder="Ictcore password" value="{{$accounting->password}}">

                                            </div>
                                        </div>
                                    </div>
                                </div>
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
<script type="text/javascript">

 var checkBox = document.getElementById("myCheck");
    // document.getElementById("myForm").reset();
    if (checkBox.checked == true){
      //alert('hello');
        $("#ictcore").show();
    }else{
      //alert('hello343');
      $("#ictcore").hide();
    }
  $(".chb").change(function()
  {
  
var checkBox = document.getElementById("myCheck");
    if (checkBox.checked == true){
      //alert('hello');
        $("#ictcore").show();
        var method = 'ictcore';
    }else{
      //alert('hello343');
      $("#ictcore").hide();
      var method = 'telenor'
    }
        //$(".chb").prop('checked',false);
        //$(this).prop('checked',true);
  });
</script>
@stop
