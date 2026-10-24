{{--
  ICTHospital - OPD history: tokens, times and payment per visit.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('content')
    @include('app.partials._alerts')
    @php($mins = fn ($a, $b) => ($a && $b) ? max(0, \Carbon\Carbon::parse($a)->diffInMinutes(\Carbon\Carbon::parse($b))) : null)
    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well"><h2><i class="glyphicon glyphicon-time"></i> OPD history</h2></div>
                <div class="box-content">
                    <form method="get" class="row">
                        <div class="col-md-2 form-group"><label>From</label><input type="date" name="from" class="form-control" value="{{ $filters['from'] }}"></div>
                        <div class="col-md-2 form-group"><label>To</label><input type="date" name="to" class="form-control" value="{{ $filters['to'] }}"></div>
                        <div class="col-md-3 form-group"><label>Doctor</label><select name="doctor" class="form-control"><option value="">All</option>@foreach ($doctors as $d)<option value="{{ $d->id }}" {{ $filters['doctorId'] === (string) $d->id ? 'selected' : '' }}>{{ $d->name }}</option>@endforeach</select></div>
                        <div class="col-md-3 form-group"><label>Status</label><select name="status" class="form-control"><option value="">All</option><option value="active" {{ $filters['status'] === 'active' ? 'selected' : '' }}>Active</option><option value="cancelled" {{ $filters['status'] === 'cancelled' ? 'selected' : '' }}>Cancelled</option></select></div>
                        <div class="col-md-2 form-group"><label>&nbsp;</label><button class="btn btn-default btn-block">Search</button></div>
                    </form>
                    @php($waits = $visits->map(fn ($v) => $mins($v->checked_in_at, $v->consult_started_at))->filter(fn ($x) => $x !== null))
                    @if ($waits->isNotEmpty())<p><strong>Average wait:</strong> {{ round($waits->avg()) }} min over {{ $waits->count() }} visit(s).</p>@endif
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed">
                        <thead><tr><th>Token</th><th>Date and time</th><th>Invoice</th><th>Patient / MR / phone</th><th>Consultant</th><th>Check in / out</th><th>Waiting / treatment</th><th>Status</th><th class="text-right">Payment</th></tr></thead>
                        <tbody>
                        @forelse ($visits as $v)
                            @php($p = $patients[$v->patient] ?? null)
                            @php($inv = $v->payment_id ? ($invoices[$v->payment_id] ?? null) : null)
                            <tr>
                                <td>{{ $v->token_no ?: '-' }}</td>
                                <td>{{ $v->s_time ? \Carbon\Carbon::parse($v->s_time)->format('d M, H:i') : $v->date }}</td>
                                <td>@if ($inv)<a href="{{ url('/invoices/' . $inv->id) }}">#{{ $inv->id }}</a>@endif</td>
                                <td>{{ optional($p)->name }} / {{ optional($p)->patient_id }} / {{ optional($p)->phone }}</td>
                                <td>{{ $doctorNames[$v->doctor] ?? '' }}</td>
                                <td>{{ $v->checked_in_at ? \Carbon\Carbon::parse($v->checked_in_at)->format('H:i') : '-' }} / {{ $v->checked_out_at ? \Carbon\Carbon::parse($v->checked_out_at)->format('H:i') : '-' }}</td>
                                <td>{{ $mins($v->checked_in_at, $v->consult_started_at) ?? '-' }} / {{ $mins($v->consult_started_at, $v->checked_out_at) ?? '-' }} min</td>
                                <td>{{ ucfirst($v->status) }}</td>
                                <td class="text-right">{{ $inv ? number_format((float) $inv->gross_total, 2) . ' (' . $inv->settlement() . ')' : '' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-muted">No visits in this range.</td></tr>
                        @endforelse
                        </tbody>
                        @if ($invoices->isNotEmpty())<tfoot><tr><th colspan="8" class="text-right">Total</th><th class="text-right">{{ number_format($invoices->sum('gross_total'), 2) }}</th></tr></tfoot>@endif
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
