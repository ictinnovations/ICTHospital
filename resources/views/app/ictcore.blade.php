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
                    <h2><i class="glyphicon glyphicon-cog"></i> Ictcore Integration</h2>

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

                        <form role="form" action="{{url('/ictcore')}}" method="post">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="type" value="{{ $type }}">
                       <div class="col-md-10" >
                                        <div class="form-group">
                                            <label for="method">Method</label>
                                            <div class="input-group">
                                                 {{--<input class="chb" data-toggle="toggle" id="myCheck" data-on="Ictcore" data-off="Telenor" data-width="100"   name="method" data-onstyle="success" data-offstyle="danger" type="checkbox" @if($ictcore_integration->method=='ictcore') Checked @endif>   --}}
                                                     <input class="chb" data-toggle="toggle" id="myCheck" data-on="Custom" data-off="Ictcore Getway" data-width="160"   name="method" data-onstyle="success" data-offstyle="danger" type="checkbox" @if($ictcore_integration->method=='ictcore' && ($ictcore_integration->type1=='' || $ictcore_integration->type1==NULL)) Checked @endif>                                            
                                            </div>

                                            </div>
                                        </div>
                                    </div>
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="col-md-10" id="ictcore">
                                        <div class="form-group">
                                            <label for="type">Url</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text" class="form-control"  name="ictcore_url" placeholder="ictcore url" value="{{$ictcore_integration->ictcore_url}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <label for="type">User Name</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text" class="form-control" required name="ictcore_user" placeholder="Ictcore User Name" value="{{$ictcore_integration->ictcore_user}}">

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
                                                <input type="password"  class="form-control" required name="ictcore_password" placeholder="Ictcore password" value="{{$ictcore_integration->ictcore_password}}">

                                            </div>
                                        </div>
                                    </div>

                                    
                                </div>
                            </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="type">Account ID</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                    <input type="text"  class="form-control" required name="ictcore_account_id" placeholder="Ictcore Account id" value="{{$ictcore_integration->ictcore_account_id}}">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {{--<div class="row" id ="tele" style="display:none">
                            <div class="col-md-12">
                            <div class="col-md-10" >
                                        <div class="form-group">
                                            <label for="type">MASK</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                <input type="text" class="form-control"  name="ictcore_urlm" placeholder="Enter Mask" value="{{$ictcore_integration->ictcore_url}}">
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            </div>--}}
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
    $("#tele").hide();
    if (checkBox.checked == true){

      //alert('hello');
       $("#tele").hide();
        $("#ictcore").show();
       
    }else{
      //alert('hello343');
      $("#ictcore").hide();
      $("#tele").show();
    }
  $(".chb").change(function()
  {
  
var checkBox = document.getElementById("myCheck");
    if (checkBox.checked == true){
      //alert('hello');
       $("#tele").hide();
        $("#ictcore").show();
        var method = 'ictcore';
    }else{
      //alert('hello343');
      $("#ictcore").hide();
      $("#tele").show();
      var method = 'telenor'
    }
        //$(".chb").prop('checked',false);
        //$(this).prop('checked',true);
  });
</script>
@stop
