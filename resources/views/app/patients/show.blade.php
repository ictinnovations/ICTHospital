{{--
  ICTHospital - single patient record.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    @if (Session::get('success'))
        <div class="alert alert-success">
            <button data-dismiss="alert" class="close" type="button">&times;</button>
            {{ Session::get('success') }}
        </div>
    @endif

    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-user"></i> {{ $patient->name }}
                        <small>{{ $patient->patient_id }}</small>
                        @if ($patient->mlc)<span class="label label-danger">MLC</span>@endif
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/patients/' . $patient->id . '/edit') }}" class="btn btn-default btn-sm">
                            <i class="glyphicon glyphicon-edit"></i> Edit
                        </a>
                        <a href="{{ url('/patients/' . $patient->id . '/card') }}" target="_blank" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-credit-card"></i> Health card</a>
                        <a href="{{ url('/patients/' . $patient->id . '/wristband') }}" target="_blank" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-tag"></i> Wristband</a>
                        @if (\App\Support\Permissions::allows('payment_add'))<a href="{{ url('/settlement?patient=' . $patient->id) }}" class="btn btn-default btn-sm"><i class="glyphicon glyphicon-check"></i> Final settlement</a>@endif
                        <a href="{{ url('/patients') }}" class="btn btn-default btn-sm">Back to list</a>
                    </div>
                </div>
                <div class="box-content">

                    <div class="row">
                        @if ($patient->img_url)
                            <div class="col-md-3">
                                <img src="{{ asset('storage/' . $patient->img_url) }}"
                                     alt="{{ $patient->name }}" class="img-thumbnail" style="max-width:100%">
                            </div>
                        @endif

                        <div class="col-md-{{ $patient->img_url ? 9 : 12 }}">
                            <table class="table table-striped">
                                <tbody>
                                    <tr><th style="width:220px">Patient ID</th><td>{{ $patient->patient_id }}</td></tr>
                                    <tr><th>Patient type</th><td>{{ optional(\App\Models\PatientType::find($patient->patient_type_id))->name ?: 'General' }}</td></tr>
                                    <tr><th>CNIC</th><td>{{ $patient->cnic ?: '-' }}</td></tr>
                                    <tr><th>Father or guardian</th><td>{{ $patient->father_name ?: '-' }}{{ $patient->guardian_relation ? ' (' . $patient->guardian_relation . ')' : '' }}{{ $patient->guardian_mrn ? ', MR ' . $patient->guardian_mrn : '' }}</td></tr>
                                    <tr><th>Date of birth</th><td>{{ $patient->birthdate ?: '-' }}</td></tr>
                                    <tr><th>Age</th><td>{{ $patient->age !== '' ? $patient->age : '-' }}</td></tr>
                                    <tr><th>Sex</th><td>{{ $patient->sex ? ucfirst($patient->sex) : '-' }}</td></tr>
                                    <tr><th>Blood group</th><td>{{ $patient->bloodgroup ?: '-' }}</td></tr>
                                    <tr><th>Phone</th><td>{{ $patient->phone ?: '-' }}</td></tr>
                                    <tr><th>Email</th><td>{{ $patient->email ?: '-' }}</td></tr>
                                    <tr><th>Address</th><td>{{ $patient->address ?: '-' }}{{ $patient->city ? ', ' . $patient->city : '' }}</td></tr>
                                    @if ($patient->alt_phone)<tr><th>Alternate contact</th><td>{{ $patient->alt_phone }}</td></tr>@endif
                                    @if ($patient->old_mrn)<tr><th>Old MR number</th><td>{{ $patient->old_mrn }}</td></tr>@endif
                                    @if ($patient->employee_no)<tr><th>Employee or student no.</th><td>{{ $patient->employee_no }}</td></tr>@endif
                                    <tr><th>Assigned doctor</th><td>{{ $patient->doctor ?: 'Not assigned' }}</td></tr>
                                    <tr><th>Registered</th><td>{{ $patient->registration_time ?: $patient->add_date }}</td></tr>
                                    <tr><th>Registered via</th><td>{{ $patient->how_added ?: '-' }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @include('app.patients._chart')

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
