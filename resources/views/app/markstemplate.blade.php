@extends('layouts.master')
@section('content')
@if (Session::get('success'))

<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
    <strong>Process Success.</strong> {{ Session::get('success')}}<br><a href="/template/list">View List</a><br>

</div>
@endif
<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-th"></i>Tempalate Message</h2>

            </div>
             <div class="box-content">
                
                       <!-- <form role="form" action="/ictcore/attendance" method="post"  enctype="multipart/form-data">-->
                       <form role="form" action="{{url('/template/create')}}" method="post"  enctype="multipart/form-data">
                      
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <br >
                                {{--<div class="form-group col-md-12 row">
                                    <label for="name"  class="col-sm-2 col-form-label">Title</label>
                                    <div class="input-group col-md-6">
                                       <input type="text" name="title" class="form-control" required>
                                    </div>
                                </div>--}}
                              <input type="hidden" name="title" value="mark_notification" class="form-control" required>
                                <div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Description</label>
                                    <div class="input-group col-md-6">
                                       <span><?php echo "[student_name],[subjects],[marks],[outoff],[exam]";?></span>
                                        <textarea type="text" class="form-control"  name="description" placeholder="Class Description"></textarea>
                                    </div>
                                </div>


                                 {{--<div class="form-group row">
                                    <label for="name" class="col-sm-2 col-form-label">Upload Message File</label>
                                    <div class="input-group col-md-6">

                                        <input type="file" class="form-control" required name="message" placeholder=""></textarea>
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
                                        <button class="btn btn-primary pull-right" type="submit"><i class="glyphicon glyphicon-plus"></i>Add</button>
                                        <br>
                                    </div>
                         </form>
                    
           
        </div>

@stop
