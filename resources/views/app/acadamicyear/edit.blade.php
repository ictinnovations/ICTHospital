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

                        <form role="form" action="{{route('year.update')}}" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="id" value="{{ $year->id }}">

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                <label for="type">Title <b> Seperate Each word by -</b></label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                    <input type="text" class="form-control" id="from" required="true"  name="title" value="{{ $year->title }}" placeholder="Enter Title">

                                                </div>
                                            </div>
                                        </div>
                                        {{--<div class="col-md-6">
                                            <div class="form-group">
                                                <label for="type">To</label>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="glyphicon glyphicon-info-sign blue"></i></span>
                                                    <input type="text" class="form-control datepicker2" id="session1" required="true"  name="to" placeholder="" value="">

                                                </div>
                                            </div>
                                        </div>--}}
                                    </div>

                                    {{--<div class="col-md-10">
                                        <div class="form-group">
                                            <label for="type">Mark as default academic year</label>
                                            <div class="input-group">
                                               
                                                <input type="checkbox" class="form-control" required name="default" @if($year->status==1) checked @endif>
                                                
                                            </div>
                                        </div>
                                    </div>--}}
                                    <br>
                                    <br>
                                    <br>
                                    
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
<script src="{{url('/js/bootstrap-datepicker.js')}}"></script>

<script type="text/javascript">
    iOSCheckbox.defaults.checkedLabel='Auto';
    iOSCheckbox.defaults.uncheckedLabel='Manual';

    $(".datepicker2").datepicker( {
    format: " yyyy", // Notice the Extra space at the beginning
    viewMode: "years",
    minViewMode: "years",
    autoclose:true

  });
</script>
@stop
