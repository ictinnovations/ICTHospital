@extends('layouts.master')
@section('style')
<link href="{{url('/css/bootstrap-datepicker.css')}}" rel="stylesheet">

@stop
@section('content')
@if (Session::get('success'))
<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">×</button>
  <strong>Process Success.</strong><br>{{ Session::get('success')}}<br>
</div>

@endif
@if (Session::get('error'))
<div class="alert alert-warning">
  <button data-dismiss="alert" class="close" type="button">×</button>
  <strong> {{ Session::get('error')}}</strong>
</div>
@endif
<div class="row">
  <div class="box col-md-12">
    <div class="box-inner">
      <div data-original-title="" class="box-header well">
        <h2><i class="glyphicon glyphicon-book"></i> Student Fee Detail</h2>
      </div>
      <div class="box-content">
        <div class="row">
          <div class="col-md-12">

          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <table id="studentList" class="table table-striped table-bordered table-hover">
              <thead>
                <tr>
                  <th>Month</th>
                  <th>Status</th>
                  <th>Fee</th>
                  <th>Late Fee</th>
                  <th>total</th>
                </tr>
              </thead>
              <tbody>
                @foreach($fee_data as $fee)
                <tr>
                  <td>{{$fee['month']}}</td>
                  <td >@if($fee['status']=='unpaid')  <span class="role unpaid">{{$fee['status']}}</span>@endif</td>
                  <td>{{$fee['fee']}}</td>
                  <td>{{$fee['lateFee']}}</td>
                  <td>{{$fee['total']}}</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <br><br>
      </div>
    </div>
  </div>
</div>
@stop
@section('script')

@stop
