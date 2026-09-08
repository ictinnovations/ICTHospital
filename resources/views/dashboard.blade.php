{{--
  ICTHospital — dashboard
  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section("style")
<link href="{{ URL::asset('/css/custom.min.css')}}" rel='stylesheet'>
<link href="{{ URL::asset('/font-awesome/css/font-awesome.min.css')}}" rel='stylesheet'>
<link href="{{ URL::asset('/css/theme1.css')}}" rel="stylesheet" media="all">
<style>
  .ich-card{background:#fff;border:1px solid #e6e9ef;border-radius:6px;padding:18px;margin-bottom:20px;}
  .ich-card .ich-num{font-size:28px;font-weight:700;line-height:1.1;color:#1b2a4a;}
  .ich-card .ich-label{color:#7b8499;font-size:13px;text-transform:uppercase;letter-spacing:.4px;}
  .ich-card .ich-icon{float:right;font-size:26px;opacity:.25;}
  .ich-sec{font-size:16px;font-weight:600;margin:6px 0 14px;color:#1b2a4a;}
  .ich-bar{height:8px;background:#eef1f6;border-radius:4px;overflow:hidden;}
  .ich-bar span{display:block;height:8px;background:#1abb9c;}
  .ich-money{font-size:22px;font-weight:700;}
  .ich-pos{color:#1abb9c;} .ich-neg{color:#e74c3c;}
  .ich-empty{color:#9aa2b1;font-style:italic;padding:14px 0;}
</style>
@endsection

@section('content')
<div class="row" style="margin-top:18px;">
  <div class="col-md-12">
    @if($error)<div class="alert alert-danger">{{ $error }}</div>@endif
    @if($success)<div class="alert alert-success">{{ $success }}</div>@endif
  </div>
</div>

{{-- headline counts --}}
<div class="row">
  <div class="col-md-3 col-sm-6">
    <div class="ich-card">
      <i class="fa fa-user-md ich-icon"></i>
      <div class="ich-num">{{ number_format($totalPatients) }}</div>
      <div class="ich-label">Patients</div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="ich-card">
      <i class="fa fa-calendar-check-o ich-icon"></i>
      <div class="ich-num">{{ number_format($totalAppointments) }}</div>
      <div class="ich-label">Appointments</div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="ich-card">
      <i class="fa fa-stethoscope ich-icon"></i>
      <div class="ich-num">{{ number_format($totalDoctors) }}</div>
      <div class="ich-label">Doctors</div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6">
    <div class="ich-card">
      <i class="fa fa-users ich-icon"></i>
      <div class="ich-num">{{ number_format($totalStaff) }}</div>
      <div class="ich-label">Staff</div>
    </div>
  </div>
</div>

{{-- beds + money --}}
<div class="row">
  <div class="col-md-6">
    <div class="ich-card">
      <div class="ich-sec">Bed occupancy</div>
      <div class="ich-num">{{ $occupiedBeds }} <small style="font-size:15px;color:#7b8499;">of {{ $totalBeds }} occupied</small></div>
      <div class="ich-bar" style="margin:12px 0;"><span style="width:{{ $occupancy }}%"></span></div>
      <div class="ich-label">{{ $occupancy }}% occupied &middot; {{ $freeBeds }} free</div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="ich-card">
      <div class="ich-sec">Finance</div>
      <div class="row">
        <div class="col-xs-4">
          <div class="ich-money ich-pos">{{ number_format($income, 2) }}</div>
          <div class="ich-label">Received</div>
        </div>
        <div class="col-xs-4">
          <div class="ich-money ich-neg">{{ number_format($expenses, 2) }}</div>
          <div class="ich-label">Expenses</div>
        </div>
        <div class="col-xs-4">
          <div class="ich-money {{ $balance >= 0 ? 'ich-pos' : 'ich-neg' }}">{{ number_format($balance, 2) }}</div>
          <div class="ich-label">Balance</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- clinical activity --}}
<div class="row">
  <div class="col-md-3 col-sm-6"><div class="ich-card"><div class="ich-num">{{ number_format($totalPrescriptions) }}</div><div class="ich-label">Prescriptions</div></div></div>
  <div class="col-md-3 col-sm-6"><div class="ich-card"><div class="ich-num">{{ number_format($totalLabTests) }}</div><div class="ich-label">Lab tests</div></div></div>
  <div class="col-md-3 col-sm-6"><div class="ich-card"><div class="ich-num">{{ number_format($totalReports) }}</div><div class="ich-label">Diagnostic reports</div></div></div>
  <div class="col-md-3 col-sm-6"><div class="ich-card"><div class="ich-num">{{ number_format($totalMedicines) }}</div><div class="ich-label">Medicines</div></div></div>
</div>

{{-- recent activity --}}
<div class="row">
  <div class="col-md-6">
    <div class="ich-card">
      <div class="ich-sec">Recent patients</div>
      @if($recentPatients->count())
      <table class="table table-striped" style="margin-bottom:0;">
        <thead><tr><th>Name</th><th>Phone</th><th>Sex</th><th>Age</th></tr></thead>
        <tbody>
        @foreach($recentPatients as $p)
          <tr>
            <td>{{ $p->name ?? '-' }}</td>
            <td>{{ $p->phone ?? '-' }}</td>
            <td>{{ $p->sex ?? '-' }}</td>
            <td>{{ $p->age ?? '-' }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
      @else
        <div class="ich-empty">No patients registered yet.</div>
      @endif
    </div>
  </div>
  <div class="col-md-6">
    <div class="ich-card">
      <div class="ich-sec">Recent appointments</div>
      @if($recentAppointments->count())
      <table class="table table-striped" style="margin-bottom:0;">
        <thead><tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Status</th></tr></thead>
        <tbody>
        @foreach($recentAppointments as $a)
          <tr>
            <td>{{ $a->patient ?? '-' }}</td>
            <td>{{ $a->doctor ?? '-' }}</td>
            <td>{{ $a->date ?? '-' }}</td>
            <td>{{ $a->status ?? '-' }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
      @else
        <div class="ich-empty">No appointments booked yet.</div>
      @endif
    </div>
  </div>
</div>
@endsection
