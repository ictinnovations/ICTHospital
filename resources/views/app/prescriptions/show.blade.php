{{--
  ICTHospital - one prescription, laid out the way it is handed to a patient.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
<style>
@media print {
    .sidebar-nav, .navbar, .box-icon, .btn { display: none !important; }
    .box-content { border: 0 !important; }
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
                    <h2><i class="glyphicon glyphicon-list-alt"></i>
                        Prescription <small>{{ $prescription->date }}</small>
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/prescriptions/' . $prescription->id . '/edit') }}" class="btn btn-default btn-sm">
                            <i class="glyphicon glyphicon-edit"></i> Edit
                        </a>
                        <a href="javascript:window.print()" class="btn btn-default btn-sm">
                            <i class="glyphicon glyphicon-print"></i> Print
                        </a>
                        <a href="{{ url('/prescriptions') }}" class="btn btn-default btn-sm">Back to list</a>
                    </div>
                </div>
                <div class="box-content">

                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                <strong>Patient:</strong>
                                @if ($patient)
                                    <a href="{{ url('/patients/' . $patient->id) }}">{{ $patient->name }}</a>
                                    ({{ $patient->patient_id }})
                                @else
                                    Unknown
                                @endif
                            </p>
                            <p><strong>Symptoms:</strong> {{ $prescription->symptom ?: '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Doctor:</strong> {{ $doctor ? $doctor->name : '-' }}</p>
                            <p><strong>Valid until:</strong> {{ $prescription->validity ?: '-' }}</p>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr><th>Medicine</th><th>Dosage</th><th>Duration</th><th>Instructions</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($prescription->items as $item)
                                <tr>
                                    <td>
                                        {{ $item->name }}
                                        @if (! $item->medicine_id)
                                            <small class="text-muted">(not in the catalogue)</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->dosage ?: '-' }}</td>
                                    <td>{{ $item->duration ?: '-' }}</td>
                                    <td>{{ $item->instructions ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted">No medicines recorded.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if ($prescription->advice)
                        <p><strong>Advice:</strong> {{ $prescription->advice }}</p>
                    @endif
                    @if ($prescription->note)
                        <p><strong>Notes:</strong> {{ $prescription->note }}</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
