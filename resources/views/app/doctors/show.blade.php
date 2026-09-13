{{--
  ICTHospital - a single doctor, with their recent appointment list.

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
        <div class="box col-md-5">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-briefcase"></i> {{ $doctor->name }}</h2>
                    <div class="box-icon">
                        <a href="{{ url('/doctors/' . $doctor->id . '/edit') }}" class="btn btn-default btn-sm">Edit</a>
                    </div>
                </div>
                <div class="box-content">
                    @if ($doctor->img_url)
                        <p><img src="{{ asset('storage/' . $doctor->img_url) }}" alt="{{ $doctor->name }}"
                                style="max-width:160px;border-radius:4px"></p>
                    @endif
                    <table class="table table-bordered">
                        <tr><th style="width:40%">Department</th><td>{{ $doctor->department ?: 'Not recorded' }}</td></tr>
                        <tr><th>Qualification</th><td>{{ $doctor->profile ?: 'Not recorded' }}</td></tr>
                        <tr><th>Phone</th><td>{{ $doctor->phone ?: 'Not recorded' }}</td></tr>
                        <tr><th>Email</th><td>{{ $doctor->email ?: 'Not recorded' }}</td></tr>
                        <tr><th>Address</th><td>{{ $doctor->address ?: 'Not recorded' }}</td></tr>
                    </table>
                    <a href="{{ url('/appointments/create?doctor=' . $doctor->id) }}" class="btn btn-primary btn-sm">
                        <i class="glyphicon glyphicon-calendar"></i> Book an appointment
                    </a>
                    <a href="{{ url('/doctors') }}" class="btn btn-default btn-sm">Back to list</a>
                </div>
            </div>
        </div>

        <div class="box col-md-7">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-calendar"></i> Recent appointments</h2>
                </div>
                <div class="box-content">
                    @if ($appointments->isEmpty())
                        <p class="text-muted">Nothing booked with this doctor yet.</p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Patient</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($appointments as $appointment)
                                    @php($patient = $patients[$appointment->patient] ?? null)
                                    <tr>
                                        <td>{{ $appointment->date }}</td>
                                        <td>{{ $appointment->time_slot }}</td>
                                        <td>
                                            @if ($patient)
                                                <a href="{{ url('/patients/' . $patient->id) }}">{{ $patient->name }}</a>
                                            @else
                                                <span class="text-muted">Removed</span>
                                            @endif
                                        </td>
                                        <td>{{ ucfirst($appointment->status) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="text-muted">Showing the {{ $appointments->count() }} most recent.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
