{{--
  ICTHospital - raise a lab request.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    <div class="row">
        <div class="box col-md-10">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-tint"></i> Raise lab request</h2>
                    <div class="box-icon">
                        <a href="{{ url('/lab') }}" class="btn btn-default btn-sm">Back to laboratory</a>
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

                    <form role="form" method="post" action="{{ url('/lab') }}">
                        {{ csrf_field() }}

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Patient <span class="text-danger">*</span></label>
                                <select name="patient" class="form-control" required>
                                    <option value="">Choose a patient</option>
                                    @foreach ($patients as $p)
                                        <option value="{{ $p->id }}"
                                            {{ (string) old('patient', $request->patient) === (string) $p->id ? 'selected' : '' }}>
                                            {{ $p->name }} ({{ $p->patient_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Requested by</label>
                                <select name="doctor" class="form-control">
                                    <option value="">Not recorded</option>
                                    @foreach ($doctors as $d)
                                        <option value="{{ $d->id }}"
                                            {{ (string) old('doctor', $request->doctor) === (string) $d->id ? 'selected' : '' }}>
                                            {{ $d->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" required
                                       value="{{ old('date', $request->date ?: now()->toDateString()) }}">
                            </div>
                        </div>

                        <h4>Tests <span class="text-danger">*</span></h4>
                        @if ($catalogue->isEmpty())
                            <p class="text-muted">
                                The test catalogue is empty.
                                <a href="{{ url('/lab/catalogue') }}">Add a test first.</a>
                            </p>
                        @else
                            <div class="row">
                                @foreach ($catalogue as $test)
                                    <div class="col-md-4">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="tests[]" value="{{ $test->id }}"
                                                    {{ in_array($test->id, (array) old('tests', [])) ? 'checked' : '' }}>
                                                {{ $test->category }}
                                                @if ($test->reference_value)
                                                    <small class="text-muted">({{ $test->reference_value }})</small>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary" {{ $catalogue->isEmpty() ? 'disabled' : '' }}>
                            Raise request
                        </button>
                        <a href="{{ url('/lab') }}" class="btn btn-default">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
