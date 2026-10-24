{{--
  ICTHospital - consultations on a day.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('content')
    @include('app.partials._alerts')
    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well"><h2><i class="glyphicon glyphicon-heart"></i> Consultations</h2>
                    <div class="box-icon"><a href="{{ url('/opd/queue') }}" class="btn btn-default btn-sm">Token queue</a></div></div>
                <div class="box-content">
                    <form method="get" class="row">
                        <div class="col-md-3 form-group"><label>Date</label><input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()"></div>
                        <div class="col-md-4 form-group"><label>Doctor</label><select name="doctor" class="form-control" onchange="this.form.submit()"><option value="">All</option>@foreach ($doctors as $d)<option value="{{ $d->id }}" {{ $doctorId === (string) $d->id ? 'selected' : '' }}>{{ $d->name }}</option>@endforeach</select></div>
                    </form>
                    <table class="table table-striped table-bordered table-condensed">
                        <thead><tr><th>Patient</th><th>Doctor</th><th>Complaint</th><th>Diagnosis</th><th>Follow-up</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        @forelse ($rows as $r)
                            <tr>
                                <td>{{ optional($patients[$r->patient_id] ?? null)->name }}</td>
                                <td>{{ $doctorNames[$r->doctor_id] ?? '' }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($r->presenting_complaint, 50) }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($r->diagnosis ?: $r->provisional_diagnosis, 50) }}</td>
                                <td>{{ $r->follow_up_date }}</td>
                                <td>{{ ucfirst($r->status) }}</td>
                                <td class="text-nowrap">@if ($r->appointment_id)<a href="{{ url('/consultations/visit/' . $r->appointment_id) }}" class="btn btn-default btn-xs">Open</a>@endif <a href="{{ url('/consultations/' . $r->id . '/print') }}" target="_blank" class="btn btn-default btn-xs">Print</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-muted">No consultations on this day. Open one from the token queue with Consult.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
