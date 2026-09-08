@extends('layouts.master')
@section('content')
<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-home"></i> Message Edit</h2>

            </div>
            @if (Session::get('success'))

<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
    <strong>Process Success.</strong> {{ Session::get('success')}}<br><br>

</div>
@endif
            <div class="box-content">
              @if (isset($message))
              <form role="form" action="{{url('/message/update')}}" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                   <input type="hidden" name="id" value="{{$message->id }}">
                   <input type="hidden" name="recording" value="{{$message->recording}}">
                      @if($message->name!='mark_notification')
                      <div class="form-group">
                        <label for="name">Name</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <input type="text" class="form-control" required name="title" value="{{$message->name}}" placeholder="Class Name">
                        </div>
                    </div>
                    @else
                    <input type="hidden" class="form-control"  name="title" value="{{$message->name}}" placeholder="Class Name">

                    @endif

                    <div class="form-group">
                        <label for="name"> write your message,  you can use  [name] and [amount]  variables</label>
                         @if($message->name=='mark_notification')
                       <span><?php echo "[student_name],[subjects],[marks],[outoff],[exam]";?></span>
                        @else
                        <span><?php //echo "[name],[amount]";?></span>
                        @endif
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <textarea type="text" class="form-control" required name="description" placeholder="Class Description">{{$message->description}}</textarea>
                        </div>
                    </div>

                
                    {{--<div class="form-group">
                        <label for="name">Message</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                            <input type="file" class="form-control"  name="message" placeholder="">
                        </div>
                    </div>--}}


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
                            <strong>Whoops!</strong>There is no such Level!<br><br>
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
