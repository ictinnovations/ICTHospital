{{--
  ICTHospital - radiology worklist.

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
                <div class="box-header well">
                    <h2><i class="glyphicon glyphicon-film"></i> Radiologist worklist</h2>
                    <div class="box-icon">
                        @if (\App\Support\Permissions::allows('diagnostic_report_add'))<a href="{{ url('/radiology/create') }}" class="btn btn-primary btn-sm">Order exam</a>@endif
                        <a href="{{ url('/radiology/catalogue') }}" class="btn btn-default btn-sm">Exam catalogue</a>
                    </div>
                </div>
                <div class="box-content">
                    <form method="get" class="row">
                        <div class="col-md-3 form-group"><label>Status</label><select name="status" class="form-control">
                            <option value="open" {{ $filters['status'] === 'open' ? 'selected' : '' }}>Open (to do)</option>
                            @foreach (\App\Models\RadiologyOrder::STATUSES as $k => $l)<option value="{{ $k }}" {{ $filters['status'] === $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach
                            <option value="" {{ $filters['status'] === '' ? 'selected' : '' }}>All</option>
                        </select></div>
                        <div class="col-md-3 form-group"><label>Modality</label><select name="modality" class="form-control"><option value="">All</option>@foreach (\App\Models\RadiologyExam::MODALITIES as $k => $l)<option value="{{ $k }}" {{ $filters['modality'] === $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select></div>
                        <div class="col-md-2 form-group"><label>From</label><input type="date" name="from" class="form-control" value="{{ $filters['from'] }}"></div>
                        <div class="col-md-2 form-group"><label>To</label><input type="date" name="to" class="form-control" value="{{ $filters['to'] }}"></div>
                        <div class="col-md-2 form-group"><label>&nbsp;</label><button class="btn btn-default btn-block">Search</button></div>
                    </form>
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed">
                        <thead><tr><th>#</th><th>Ordered</th><th>Patient</th><th>Exam</th><th>Modality</th><th>Referred by</th><th>Status</th><th>Billed</th><th></th></tr></thead>
                        <tbody>
                        @forelse ($orders as $o)
                            @php($p = $patients[$o->patient_id] ?? null)
                            <tr class="{{ $o->urgent && in_array($o->status, ['ordered', 'scheduled', 'done']) ? 'danger' : '' }}">
                                <td>{{ $o->id }}</td>
                                <td>{{ \Illuminate\Support\Str::of($o->created_at)->substr(0, 16) }}</td>
                                <td>{{ optional($p)->name }} <small class="text-muted">{{ optional($p)->patient_id }}</small></td>
                                <td>{{ $o->name }} @if ($o->urgent)<span class="label label-danger">Urgent</span>@endif</td>
                                <td>{{ \App\Models\RadiologyExam::MODALITIES[$o->modality] ?? $o->modality }}</td>
                                <td>{{ $doctorNames[$o->doctor_id] ?? '' }}</td>
                                <td>{{ \App\Models\RadiologyOrder::STATUSES[$o->status] ?? $o->status }}</td>
                                <td>@if ($o->payment_id)<a href="{{ url('/invoices/' . $o->payment_id) }}">#{{ $o->payment_id }}</a>@endif</td>
                                <td><a href="{{ url('/radiology/' . $o->id) }}" class="btn btn-default btn-xs">{{ $o->status === 'reported' ? 'View' : 'Open' }}</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-muted">Nothing on the worklist.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
