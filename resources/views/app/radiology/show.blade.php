{{--
  ICTHospital - one radiology order: status and the report.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('content')
    @include('app.partials._alerts')
    @php($canReport = \App\Support\Permissions::allows('diagnostic_report_add'))
    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well">
                    <h2><i class="glyphicon glyphicon-film"></i> {{ $order->name }} <small>{{ optional($patient)->name }} &middot; MR {{ optional($patient)->patient_id }}</small>
                        @if ($order->urgent)<span class="label label-danger">Urgent</span>@endif</h2>
                    <div class="box-icon">
                        @if ($order->status === 'reported')<a href="{{ url('/radiology/' . $order->id . '/print') }}" target="_blank" class="btn btn-default btn-sm">Print report</a>@endif
                        <a href="{{ url('/radiology') }}" class="btn btn-default btn-sm">Worklist</a>
                    </div>
                </div>
                <div class="box-content">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-condensed">
                                <tr><th style="width:180px">Status</th><td>{{ \App\Models\RadiologyOrder::STATUSES[$order->status] ?? $order->status }}</td></tr>
                                <tr><th>Modality</th><td>{{ \App\Models\RadiologyExam::MODALITIES[$order->modality] ?? $order->modality }}</td></tr>
                                <tr><th>Referred by</th><td>{{ optional($doctor)->name ?: '-' }}</td></tr>
                                <tr><th>Clinical notes</th><td>{{ $order->clinical_notes ?: '-' }}</td></tr>
                                <tr><th>Ordered</th><td>{{ $order->created_at }}</td></tr>
                                @if ($order->scheduled_at)<tr><th>Scheduled</th><td>{{ $order->scheduled_at }}</td></tr>@endif
                                @if ($order->performed_at)<tr><th>Performed</th><td>{{ $order->performed_at }}</td></tr>@endif
                                @if ($order->reported_at)<tr><th>Reported</th><td>{{ $order->reported_at }} by {{ $reporter ? trim($reporter->firstname . ' ' . $reporter->lastname) : '' }}</td></tr>@endif
                                <tr><th>Invoice</th><td>@if ($order->payment_id)<a href="{{ url('/invoices/' . $order->payment_id) }}">#{{ $order->payment_id }}</a>@elseif (\App\Support\Permissions::allows('payment_add') && $order->status !== 'cancelled')
                                    <form method="post" action="{{ url('/radiology/' . $order->id . '/bill') }}" style="display:inline">{{ csrf_field() }}<button class="btn btn-default btn-xs">Raise invoice</button></form>@else - @endif</td></tr>
                            </table>

                            @if ($canReport && $order->status !== 'cancelled')
                                <form method="post" action="{{ url('/radiology/' . $order->id) }}">
                                    {{ csrf_field() }}<input type="hidden" name="_method" value="PUT">
                                    @if (in_array($order->status, ['ordered', 'scheduled']))
                                        <div class="form-inline" style="margin-bottom:12px">
                                            <input type="datetime-local" name="scheduled_at" class="form-control input-sm">
                                            <button name="action" value="schedule" class="btn btn-default btn-sm">Schedule</button>
                                            <button name="action" value="done" class="btn btn-default btn-sm">Mark performed</button>
                                            <button name="action" value="cancel" class="btn btn-link btn-sm text-danger" onclick="return confirm('Cancel this order?')">Cancel order</button>
                                        </div>
                                    @endif
                                    <div class="form-group"><label>Findings</label><textarea name="findings" class="form-control" rows="8">{{ old('findings', $order->findings) }}</textarea></div>
                                    <div class="form-group"><label>Impression</label><textarea name="impression" class="form-control" rows="3">{{ old('impression', $order->impression) }}</textarea></div>
                                    <button name="action" value="report" class="btn btn-primary">{{ $order->status === 'reported' ? 'Correct report' : 'Save report' }}</button>
                                </form>
                            @elseif ($order->status === 'reported')
                                <h4>Findings</h4><p style="white-space:pre-line">{{ $order->findings }}</p>
                                <h4>Impression</h4><p style="white-space:pre-line">{{ $order->impression }}</p>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <h4>Previous imaging</h4>
                            @forelse ($history as $h)
                                <p><a href="{{ url('/radiology/' . $h->id) }}">{{ \Illuminate\Support\Str::of($h->created_at)->substr(0, 10) }} {{ $h->name }}</a><br><small class="text-muted">{{ \Illuminate\Support\Str::limit($h->impression, 80) ?: (\App\Models\RadiologyOrder::STATUSES[$h->status] ?? '') }}</small></p>
                            @empty
                                <p class="text-muted">None.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
