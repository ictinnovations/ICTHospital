{{--
  ICTHospital - single appointment.

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
                    <h2><i class="glyphicon glyphicon-calendar"></i>
                        {{ $patient ? $patient->name : 'Unknown patient' }}
                        <small>{{ $appointment->date }} {{ $appointment->time_slot }}</small>
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/appointments/' . $appointment->id . '/edit') }}" class="btn btn-default btn-sm">
                            <i class="glyphicon glyphicon-edit"></i> Edit
                        </a>
                        <a href="{{ url('/appointments?date=' . $appointment->date) }}" class="btn btn-default btn-sm">
                            Back to day view
                        </a>
                    </div>
                </div>
                <div class="box-content">

                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th style="width:220px">Patient</th>
                                <td>
                                    @if ($patient)
                                        <a href="{{ url('/patients/' . $patient->id) }}">{{ $patient->name }}</a>
                                        ({{ $patient->patient_id }})
                                    @else
                                        Unknown
                                    @endif
                                </td>
                            </tr>
                            <tr><th>Doctor</th><td>{{ $doctor ? $doctor->name : 'Unknown' }}</td></tr>
                            <tr><th>Date</th><td>{{ $appointment->date }}</td></tr>
                            <tr><th>Time</th><td>{{ $appointment->time_slot }}</td></tr>
                            <tr><th>Status</th><td>{{ ucfirst($appointment->status) }}</td></tr>
                            <tr><th>Remarks</th><td>{{ $appointment->remarks ?: '-' }}</td></tr>
                            <tr><th>Blood pressure</th><td>{{ $appointment->b_p ?: '-' }}</td></tr>
                            <tr><th>Pulse</th><td>{{ $appointment->pulse ?: '-' }}</td></tr>
                            <tr><th>Temperature</th><td>{{ $appointment->temprature ?: '-' }}</td></tr>
                            <tr><th>Weight</th><td>{{ $appointment->weight ?: '-' }}</td></tr>
                            <tr><th>Booked</th><td>{{ $appointment->registration_time ?: $appointment->add_date }}</td></tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
