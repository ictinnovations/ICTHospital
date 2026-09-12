{{--
  ICTHospital - book or edit an appointment.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    @php($editing = (bool) $appointment->id)
    @php($startValue = old('start', $appointment->s_time ? \Carbon\Carbon::parse($appointment->s_time)->format('H:i') : ''))
    @php($minutesValue = old('minutes', $appointment->s_time && $appointment->e_time
        ? \Carbon\Carbon::parse($appointment->s_time)->diffInMinutes(\Carbon\Carbon::parse($appointment->e_time))
        : 15))

    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-calendar"></i>
                        {{ $editing ? 'Edit appointment' : 'Book appointment' }}
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/appointments') }}" class="btn btn-default btn-sm">Back to day view</a>
                    </div>
                </div>
                <div class="box-content">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Check the form.</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form role="form" method="post"
                          action="{{ $editing ? url('/appointments/' . $appointment->id) : url('/appointments') }}">
                        {{ csrf_field() }}
                        @if ($editing)
                            <input type="hidden" name="_method" value="PUT">
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Patient <span class="text-danger">*</span></label>
                                    <select name="patient" class="form-control" required>
                                        <option value="">Choose a patient</option>
                                        @foreach ($patientList as $p)
                                            <option value="{{ $p->id }}"
                                                {{ (string) old('patient', $appointment->patient) === (string) $p->id ? 'selected' : '' }}>
                                                {{ $p->name }} ({{ $p->patient_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @if ($patientList->isEmpty())
                                        <span class="help-block">
                                            No patients yet. <a href="{{ url('/patients/create') }}">Register one first.</a>
                                        </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label>Doctor <span class="text-danger">*</span></label>
                                    <select name="doctor" class="form-control" required>
                                        <option value="">Choose a doctor</option>
                                        @foreach ($doctors as $doctor)
                                            <option value="{{ $doctor->id }}"
                                                {{ (string) old('doctor', $appointment->doctor) === (string) $doctor->id ? 'selected' : '' }}>
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        @foreach ($statuses as $value)
                                            <option value="{{ $value }}"
                                                {{ old('status', $appointment->status ?: 'scheduled') === $value ? 'selected' : '' }}>
                                                {{ ucfirst($value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" required
                                           value="{{ old('date', $appointment->date ?: now()->toDateString()) }}">
                                </div>
                                <div class="form-group">
                                    <label>Start time <span class="text-danger">*</span></label>
                                    <input type="time" name="start" class="form-control" required value="{{ $startValue }}">
                                </div>
                                <div class="form-group">
                                    <label>Length</label>
                                    <input type="number" name="minutes" class="form-control" min="5" max="480"
                                           value="{{ $minutesValue }}">
                                    <span class="help-block">
                                        Minutes. The end time is worked out from this, so the slot and the times cannot disagree.
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Remarks</label>
                            <textarea name="remarks" class="form-control" rows="2"
                                      maxlength="500">{{ old('remarks', $appointment->remarks) }}</textarea>
                        </div>

                        <h4>Vitals <small>recorded at the visit, optional</small></h4>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label>Blood pressure</label>
                                <input type="text" name="b_p" class="form-control" value="{{ old('b_p', $appointment->b_p) }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Pulse</label>
                                <input type="text" name="pulse" class="form-control" value="{{ old('pulse', $appointment->pulse) }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Temperature</label>
                                <input type="text" name="temprature" class="form-control" value="{{ old('temprature', $appointment->temprature) }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Weight</label>
                                <input type="text" name="weight" class="form-control" value="{{ old('weight', $appointment->weight) }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ $editing ? 'Save changes' : 'Book appointment' }}
                        </button>
                        <a href="{{ url('/appointments') }}" class="btn btn-default">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
