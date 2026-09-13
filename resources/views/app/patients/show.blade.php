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
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/patients/' . $patient->id . '/edit') }}" class="btn btn-default btn-sm">
                            <i class="glyphicon glyphicon-edit"></i> Edit
                        </a>
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
                                    <tr><th>Father or guardian</th><td>{{ $patient->father_name ?: '-' }}</td></tr>
                                    <tr><th>Date of birth</th><td>{{ $patient->birthdate ?: '-' }}</td></tr>
                                    <tr><th>Age</th><td>{{ $patient->age !== '' ? $patient->age : '-' }}</td></tr>
                                    <tr><th>Sex</th><td>{{ $patient->sex ? ucfirst($patient->sex) : '-' }}</td></tr>
                                    <tr><th>Blood group</th><td>{{ $patient->bloodgroup ?: '-' }}</td></tr>
                                    <tr><th>Phone</th><td>{{ $patient->phone ?: '-' }}</td></tr>
                                    <tr><th>Email</th><td>{{ $patient->email ?: '-' }}</td></tr>
                                    <tr><th>Address</th><td>{{ $patient->address ?: '-' }}</td></tr>
                                    <tr><th>Assigned doctor</th><td>{{ $patient->doctor ?: 'Not assigned' }}</td></tr>
                                    <tr><th>Registered</th><td>{{ $patient->registration_time ?: $patient->add_date }}</td></tr>
                                    <tr><th>Registered via</th><td>{{ $patient->how_added ?: '-' }}</td></tr>
                                </tbody>
                            </table>
                    <a href="{{ url('/history?patient=' . $patient->id) }}" class="btn btn-default btn-sm">
                        <i class="glyphicon glyphicon-folder-open"></i> Medical history
                    </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
