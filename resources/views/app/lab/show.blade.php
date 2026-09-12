{{--
  ICTHospital - lab request and result entry.

  Results are entered on the same screen that displays them, because a technician
  filling one test at a time should not have to move between a view and an edit
  form for every line.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
<style>
@media print {
    .sidebar-nav, .navbar, .box-icon, .btn, .result-input { display: none !important; }
}
</style>
@stop
@section('content')

    @if (Session::get('success'))
        <div class="alert alert-success">
            <button data-dismiss="alert" class="close" type="button">&times;</button>
            {{ Session::get('success') }}
        </div>
    @endif

    <div class="row">
        <div class="box col-md-10">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-tint"></i>
                        {{ $patient ? $patient->name : 'Unknown patient' }}
                        <small>{{ $request->date }}</small>
                    </h2>
                    <div class="box-icon">
                        <a href="javascript:window.print()" class="btn btn-default btn-sm">
                            <i class="glyphicon glyphicon-print"></i> Print
                        </a>
                        <a href="{{ url('/lab') }}" class="btn btn-default btn-sm">Back to laboratory</a>
                    </div>
                </div>
                <div class="box-content">

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Patient:</strong>
                                @if ($patient)
                                    <a href="{{ url('/patients/' . $patient->id) }}">{{ $patient->name }}</a>
                                    ({{ $patient->patient_id }})
                                @else
                                    {{ $request->patient_name ?: 'Unknown' }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Requested by:</strong> {{ $doctor ? $doctor->name : ($request->doctor_name ?: '-') }}</p>
                            <p><strong>Status:</strong> {{ ucfirst($request->status) }}</p>
                        </div>
                    </div>

                    <form role="form" method="post" action="{{ url('/lab/' . $request->id . '/results') }}">
                        {{ csrf_field() }}

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:30%">Test</th>
                                    <th style="width:30%">Reference range</th>
                                    <th>Result</th>
                                    <th style="width:18%">Reported</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($request->tests as $test)
                                    <tr>
                                        <td>{{ $test->name }}</td>
                                        <td>{{ $test->reference_value ?: '-' }}</td>
                                        <td>
                                            <span class="hidden-print">
                                                <input type="text" class="form-control result-input"
                                                       name="results[{{ $test->id }}]"
                                                       value="{{ $test->result }}" maxlength="1000">
                                            </span>
                                            <span class="visible-print-inline">{{ $test->result ?: 'pending' }}</span>
                                        </td>
                                        <td>{{ $test->reported_at ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <p class="help-block">
                            Leaving a box blank does not clear a result that is already recorded, so submitting
                            from a stale page cannot wipe someone else's entry.
                        </p>

                        <button type="submit" class="btn btn-primary">Save results</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
