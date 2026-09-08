@extends('layouts.master')
@section('style')
<style type="text/css">
  .dataTables_filter {
    float: left !important;
    text-align: left !important; 
    margin-left: 58% !important;
}
</style>
@stop
@section('content')
@if (Session::get('success'))
<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
    <strong>Process Success.</strong> {{ Session::get('success')}}

</div>
@endif
<div class="row">
<div class="box col-md-12">
        <div class="box-inner">
            <div data-original-title="" class="box-header well">
                <h2><i class="glyphicon glyphicon-user"></i> Acadamic Year</h2>

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
                <a href="{{route('year.add')}}" class="btn btn-primary pull-right" type="submit" style="margin-bottom: 0%;"><i class="glyphicon glyphicon-plus"></i>Add Acadamic Year</a>

                  </div>


                
                <div class="row">
                  <div class="col-md-12">
                      <table id="gpaList" class="table table-striped table-bordered table-hover">
                          <thead>
                            <tr>
                              <th>Year Title</th>
                              <th>Satus</th>
                              <th>Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            @if(count($years)>0)
                            @foreach($years as $year)
                            <tr>
                              <td>{{$year->title}}</td>
                              <td>@if($year->status==0)Inactive @else Default academic year @endif</td>
                              <td>
                             @if($year->status==0)
                             <a title='Active' class='btn btn-info' href='{{url("/academicYear/status")}}/{{$year->id}}?status=1' onclick="return confirm('Are you sure?')"> <i class="glyphicon glyphicon-edit icon-white"></i></a>&nbsp&nbsp
                              @endif
                              <a title='Edit' class='btn btn-info' href='{{url("/academicYear/edit")}}/{{$year->id}}'> <i class="glyphicon glyphicon-pencil"></i></a>&nbsp&nbsp
                              <a title='Delete' class='btn btn-danger' href='{{url("/academicYear/delete")}}/{{$year->id}}'> <i class="glyphicon glyphicon-trash icon-white"></i></a>
                              </td>
                            </tr>
                            @endforeach
                            @endif
                          </tbody>
                        </table>
                  </div>
                </div>
               






        </div>
    </div>
</div>
</div>
@stop
@section('script')
<script type="text/javascript">
    $( document ).ready(function() {
        $('#gpaList').dataTable();
    });
</script>

@stop
