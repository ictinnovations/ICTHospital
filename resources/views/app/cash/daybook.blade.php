{{--
  ICTHospital - day book: billing by department and patient group, doctor and
  hospital share per line, and collections by payment mode.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('content')
    @include('app.partials._alerts')
    @php($buckets = ['Private', 'Panel', 'Charity'])
    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well">
                    <h2><i class="glyphicon glyphicon-book"></i> Day book</h2>
                    <div class="box-icon">
                        <a href="{{ url('/day-book?' . http_build_query(array_merge(request()->query(), ['export' => 'csv']))) }}" class="btn btn-default btn-sm">Export CSV</a>
                    </div>
                </div>
                <div class="box-content">
                    <form method="get" class="row">
                        <div class="col-md-2 form-group"><label>From</label><input type="date" name="from" class="form-control" value="{{ $filters['from'] }}"></div>
                        <div class="col-md-2 form-group"><label>To</label><input type="date" name="to" class="form-control" value="{{ $filters['to'] }}"></div>
                        <div class="col-md-3 form-group"><label>User</label><select name="user" class="form-control"><option value="">All</option>@foreach ($users as $u)<option value="{{ $u->id }}" {{ $filters['userId'] === (string) $u->id ? 'selected' : '' }}>{{ trim($u->firstname . ' ' . $u->lastname) ?: $u->login }}</option>@endforeach</select></div>
                        <div class="col-md-3 form-group"><label>Patient group</label><select name="kind" class="form-control"><option value="">All</option>@foreach ($buckets as $b)<option value="{{ strtolower($b) }}" {{ $filters['kind'] === strtolower($b) ? 'selected' : '' }}>{{ $b }}</option>@endforeach</select></div>
                        <div class="col-md-2 form-group"><label>&nbsp;</label><button class="btn btn-default btn-block">Search</button></div>
                    </form>

                    <div class="row">
                        <div class="col-md-7">
                            <h4>Department wise summary <small>net billed</small></h4>
                            <table class="table table-bordered table-condensed">
                                <thead><tr><th>Department</th>@foreach ($buckets as $b)<th class="text-right">{{ $b }}</th>@endforeach<th class="text-right">Grand total</th></tr></thead>
                                <tbody>
                                @forelse ($byDept as $dept => $row)
                                    <tr><td>{{ $dept }}</td>@foreach ($buckets as $b)<td class="text-right">{{ number_format($row[$b] ?? 0, 2) }}</td>@endforeach<td class="text-right"><strong>{{ number_format(array_sum($row), 2) }}</strong></td></tr>
                                @empty
                                    <tr><td colspan="5" class="text-muted">Nothing billed in this range.</td></tr>
                                @endforelse
                                </tbody>
                                @if ($byDept)
                                <tfoot><tr><th>Grand total</th>@foreach ($buckets as $b)<th class="text-right">{{ number_format(collect($byDept)->sum(fn ($r) => $r[$b] ?? 0), 2) }}</th>@endforeach<th class="text-right">{{ number_format(collect($byDept)->sum(fn ($r) => array_sum($r)), 2) }}</th></tr></tfoot>
                                @endif
                            </table>
                        </div>
                        <div class="col-md-5">
                            <h4>Collected <small>by payment mode, same days</small></h4>
                            <table class="table table-bordered table-condensed">
                                @foreach (\App\Support\PaymentModes::labels() as $k => $label)
                                    <tr><th>{{ $label }}</th><td class="text-right">{{ number_format($collections[$k], 2) }}</td></tr>
                                @endforeach
                                <tr class="success"><th>Subtotal</th><td class="text-right"><strong>{{ number_format($collections['total'], 2) }}</strong></td></tr>
                                @if ($collections['write_off'] > 0)<tr><th class="text-muted">Written off</th><td class="text-right text-muted">{{ number_format($collections['write_off'], 2) }}</td></tr>@endif
                            </table>
                        </div>
                    </div>

                    @if (! empty($kind_summary))
                        <h4>By kind of service</h4>
                        <p>@foreach ($kind_summary as $k => $v)<span class="label label-default" style="margin-right:6px;font-size:12px">{{ \App\Support\ServiceKinds::label($k) }}: {{ number_format($v, 2) }}</span>@endforeach</p>
                    @endif
                    <h4>Services consultation wise</h4>
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed">
                        <thead><tr><th>Date</th><th>Invoice</th><th>Patient</th><th>Type</th><th>Service</th><th>Department</th><th class="text-right">Total sale</th><th class="text-right">Discount</th><th class="text-right">Net</th><th class="text-right">Doctor share</th><th class="text-right">Hospital share</th></tr></thead>
                        <tbody>
                        @foreach ($lines as $l)
                            <tr><td>{{ $l['date'] }}</td><td><a href="{{ url('/invoices/' . $l['invoice']) }}">#{{ $l['invoice'] }}</a></td><td>{{ $l['patient'] }}</td><td>{{ $l['type'] }}</td><td>{{ $l['service'] }}</td><td>{{ $l['department'] }}</td>
                                <td class="text-right">{{ number_format($l['gross'], 2) }}</td><td class="text-right">{{ number_format($l['discount'], 2) }}</td><td class="text-right">{{ number_format($l['net'], 2) }}</td><td class="text-right">{{ number_format($l['doctor'], 2) }}</td><td class="text-right">{{ number_format($l['hospital'], 2) }}</td></tr>
                        @endforeach
                        </tbody>
                        @if ($lines)
                        <tfoot><tr><th colspan="6" class="text-right">Total</th>@foreach (['gross', 'discount', 'net', 'doctor', 'hospital'] as $k)<th class="text-right">{{ number_format(collect($lines)->sum($k), 2) }}</th>@endforeach</tr></tfoot>
                        @endif
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
