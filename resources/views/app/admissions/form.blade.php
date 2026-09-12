{{--
  ICTHospital - admit a patient.

  Only free beds are offered, so a clash is hard to reach from the form. The
  controller still checks, because two clerks can have this page open at once.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    <div class="row">
        <div class="box col-md-8">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-log-in"></i> Admit patient</h2>
                    <div class="box-icon">
                        <a href="{{ url('/admissions') }}" class="btn btn-default btn-sm">Back to admissions</a>
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

                    <form role="form" method="post" action="{{ url('/admissions') }}">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>Patient <span class="text-danger">*</span></label>
                            <select name="patient" class="form-control" required>
                                <option value="">Choose a patient</option>
                                @foreach ($patients as $p)
                                    <option value="{{ $p->id }}"
                                        {{ (string) old('patient', $selectedPatient) === (string) $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ $p->patient_id }})
                                    </option>
                                @endforeach
                            </select>
                            @if ($patients->isEmpty())
                                <span class="help-block">
                                    No patients yet. <a href="{{ url('/patients/create') }}">Register one first.</a>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Bed <span class="text-danger">*</span></label>
                            <select name="bed_id" class="form-control" required>
                                <option value="">Choose a free bed</option>
                                @foreach ($freeBeds as $bed)
                                    <option value="{{ $bed->id }}"
                                        {{ (string) old('bed_id') === (string) $bed->id ? 'selected' : '' }}>
                                        {{ $categories[$bed->category] ?? 'Ward' }} - bed {{ $bed->number }}
                                        @if ($bed->description) ({{ $bed->description }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            @if ($freeBeds->isEmpty())
                                <span class="help-block">
                                    Every bed is occupied. Discharge someone, or
                                    <a href="{{ url('/beds/create') }}">add a bed</a>.
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Admitted at</label>
                            <input type="datetime-local" name="a_time" class="form-control" value="{{ old('a_time') }}">
                            <span class="help-block">Leave blank for now.</span>
                        </div>

                        <button type="submit" class="btn btn-primary" {{ $freeBeds->isEmpty() ? 'disabled' : '' }}>
                            Admit
                        </button>
                        <a href="{{ url('/admissions') }}" class="btn btn-default">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
