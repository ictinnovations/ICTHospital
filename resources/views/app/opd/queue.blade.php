{{--
  ICTHospital - OPD token queue for a day: check in, call in, done, bill the visit.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('content')
    @include('app.partials._alerts')
    @php($mins = fn ($a, $b) => ($a && $b) ? max(0, (int) floor(\Carbon\Carbon::parse($a)->diffInMinutes(\Carbon\Carbon::parse($b)))) : null)
    @php($canUpdate = \App\Support\Permissions::allows('appointment_update'))
    @php($canBill = \App\Support\Permissions::allows('payment_add'))
    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well">
                    <h2><i class="glyphicon glyphicon-sort-by-order"></i> OPD token queue</h2>
                    <div class="box-icon"><a href="{{ url('/opd/history') }}" class="btn btn-default btn-sm">OPD history</a></div>
                </div>
                <div class="box-content">
                    <form method="get" class="row">
                        <div class="col-md-3 form-group"><label>Date</label><input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()"></div>
                        <div class="col-md-4 form-group"><label>Doctor</label><select name="doctor" class="form-control" onchange="this.form.submit()"><option value="">All doctors</option>@foreach ($doctors as $d)<option value="{{ $d->id }}" {{ $doctorId === (string) $d->id ? 'selected' : '' }}>{{ $d->name }}</option>@endforeach</select></div>
                    </form>

                    <div class="row">
                        @foreach (['Total patients' => $counts['total'], 'Waiting' => $counts['waiting'], 'With doctor' => $counts['in'], 'Checked out' => $counts['done']] as $label => $n)
                            <div class="col-md-3 col-xs-6"><div class="well well-sm text-center"><small>{{ $label }}</small><br><strong style="font-size:22px">{{ $n }}</strong></div></div>
                        @endforeach
                    </div>

                    @if (\App\Support\Permissions::allows('appointment_add') && $date === now()->toDateString())
                        <form method="post" action="{{ url('/opd/walk-in') }}" class="row well well-sm" style="margin:0 0 15px">
                            {{ csrf_field() }}
                            <div class="col-md-4 form-group"><label>Walk-in patient</label><select name="patient" class="form-control" required><option value="">Choose</option>@foreach ($patientList as $p)<option value="{{ $p->id }}">{{ $p->name }} ({{ $p->patient_id }})</option>@endforeach</select></div>
                            <div class="col-md-3 form-group"><label>Doctor</label><select name="doctor" class="form-control" required>@foreach ($doctors as $d)<option value="{{ $d->id }}" {{ $doctorId === (string) $d->id ? 'selected' : '' }}>{{ $d->name }}</option>@endforeach</select></div>
                            <div class="col-md-2 form-group"><label>Visit</label><select name="visit_type" class="form-control">@foreach ($visitTypes as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                            <div class="col-md-1 form-group"><label>Bill fee</label><br><input type="checkbox" name="bill" value="1" {{ $canBill ? 'checked' : 'disabled' }}></div>
                            <div class="col-md-2 form-group"><label>&nbsp;</label><button class="btn btn-primary btn-block">Issue token</button></div>
                        </form>
                    @endif

                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed">
                        <thead><tr><th>Token</th><th>Patient</th><th>Doctor</th><th>Booked</th><th>Checked in</th><th>Waiting</th><th>With doctor</th><th>Status</th><th>Fee</th><th></th></tr></thead>
                        <tbody>
                        @forelse ($visits as $v)
                            @php($p = $patients[$v->patient] ?? null)
                            <tr class="{{ $v->status === 'in consultation' ? 'info' : ($v->status === 'completed' ? 'success' : '') }}">
                                <td><strong style="font-size:16px">{{ $v->token_no ?: '-' }}</strong></td>
                                <td>@if ($p)<a href="{{ url('/patients/' . $p->id) }}">{{ $p->name }}</a><br><small class="text-muted">{{ $p->patient_id }}</small>@endif</td>
                                <td>{{ $doctorNames[$v->doctor] ?? '' }}</td>
                                <td>{{ $v->s_time ? \Carbon\Carbon::parse($v->s_time)->format('H:i') : '' }}{{ $v->remarks === 'Walk-in' ? ' (walk-in)' : '' }}</td>
                                <td>{{ $v->checked_in_at ? \Carbon\Carbon::parse($v->checked_in_at)->format('H:i') : '' }}</td>
                                <td>{{ ($w = $mins($v->checked_in_at, $v->consult_started_at ?: ($v->status === 'arrived' ? now() : null))) !== null ? $w . ' min' : '' }}</td>
                                <td>{{ ($t = $mins($v->consult_started_at, $v->checked_out_at ?: ($v->status === 'in consultation' ? now() : null))) !== null ? $t . ' min' : '' }}</td>
                                <td>{{ ucfirst($v->status) }}</td>
                                <td>@if ($v->payment_id)<a href="{{ url('/invoices/' . $v->payment_id) }}">#{{ $v->payment_id }}</a>@endif</td>
                                <td style="min-width:170px">
                                    @if ($canUpdate)
                                        @if (in_array($v->status, ['scheduled', 'no show'], true))
                                            <form method="post" action="{{ url('/opd/' . $v->id . '/check-in') }}" style="display:inline">{{ csrf_field() }}<button class="btn btn-default btn-xs">Check in</button></form>
                                        @elseif ($v->status === 'arrived')
                                            <form method="post" action="{{ url('/opd/' . $v->id . '/call-in') }}" style="display:inline">{{ csrf_field() }}<button class="btn btn-primary btn-xs">Call in</button></form>
                                        @elseif ($v->status === 'in consultation')
                                            <form method="post" action="{{ url('/opd/' . $v->id . '/done') }}" style="display:inline">{{ csrf_field() }}<button class="btn btn-success btn-xs">Done</button></form>
                                        @endif
                                    @endif
                                    @if (\App\Support\Permissions::allows('consultation_add') && in_array($v->status, ['arrived', 'in consultation', 'completed'], true))
                                        <a href="{{ url('/consultations/visit/' . $v->id) }}" class="btn btn-info btn-xs">Consult</a>
                                    @endif
                                    @if ($canBill && ! $v->payment_id && $v->status !== 'cancelled')
                                        <form method="post" action="{{ url('/opd/' . $v->id . '/bill') }}" style="display:inline">{{ csrf_field() }}
                                            <select name="visit_type" class="input-sm" style="height:22px;padding:0">@foreach ($visitTypes as $k => $l)<option value="{{ $k }}" {{ ($v->visit_type ?: 'first') === $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select>
                                            <button class="btn btn-default btn-xs">Bill fee</button></form>
                                    @endif
                                    @if ($v->token_no)<a href="{{ url('/opd/' . $v->id . '/token') }}" target="_blank" class="btn btn-default btn-xs">Token slip</a>@endif
                                    @if ($v->mode === 'video' && $v->video_room)<a href="{{ \App\Support\Telemedicine::url($v) }}" target="_blank" rel="noopener" class="btn btn-success btn-xs"><i class="glyphicon glyphicon-facetime-video"></i> Video</a>@endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-muted">No visits for this day yet. Book appointments, or issue a walk-in token above.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    </div>
                    <p class="help-block">Check in gives the next token for that doctor. Waiting time runs from check in to call in; time with the doctor runs from call in to done.</p>
                </div>
            </div>
        </div>
    </div>
@stop
