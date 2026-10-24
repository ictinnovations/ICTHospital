{{--
  ICTHospital - end-of-shift cash close.

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
                    <h2><i class="glyphicon glyphicon-briefcase"></i> Sale close: {{ $userNames[$userId] ?? 'me' }}</h2>
                    <div class="box-icon"><a href="{{ url('/day-book') }}" class="btn btn-default btn-sm">Day book</a></div>
                </div>
                <div class="box-content">
                    @if ($users->isNotEmpty())
                        <form method="get" class="form-inline" style="margin-bottom:12px">
                            <label>Cashier</label>
                            <select name="user_id" class="form-control" onchange="this.form.submit()">
                                @foreach ($users as $u)<option value="{{ $u->id }}" {{ (int) $userId === $u->id ? 'selected' : '' }}>{{ trim($u->firstname . ' ' . $u->lastname) ?: $u->login }}</option>@endforeach
                            </select>
                        </form>
                    @endif

                    <div class="row">
                        @foreach (\App\Support\PaymentModes::labels() as $k => $label)
                            <div class="col-md-2 col-xs-6"><div class="well well-sm text-center"><small>{{ $label }}</small><br><strong style="font-size:18px">{{ number_format($totals[$k], 2) }}</strong></div></div>
                        @endforeach
                        <div class="col-md-2 col-xs-6"><div class="well well-sm text-center" style="background:#dff0d8"><small>Total collected</small><br><strong style="font-size:18px">{{ number_format($totals['total'], 2) }}</strong></div></div>
                    </div>
                    @if ($totals['write_off'] > 0)<p class="text-muted">Write-offs this shift: {{ number_format($totals['write_off'], 2) }} (not money, not in the totals).</p>@endif

                    <h4>Current sale <small>{{ $totals['count'] }} payment(s) since the last close</small></h4>
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed">
                        <thead><tr><th>Time</th><th>Invoice</th><th>Patient</th><th>Mode</th><th>Reference</th><th class="text-right">Amount</th></tr></thead>
                        <tbody>
                        @forelse ($payments as $p)
                            <tr>
                                <td>{{ \Illuminate\Support\Str::of($p->paid_at)->substr(0, 16) }}</td>
                                <td><a href="{{ url('/invoices/' . $p->payment_id) }}">#{{ $p->payment_id }}</a></td>
                                <td>{{ optional($p->invoice)->patient_name }}</td>
                                <td>{{ $p->method ?: 'Cash' }}</td>
                                <td>{{ $p->reference }}</td>
                                <td class="text-right">{{ number_format((float) $p->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-muted">No payments since the last close.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    </div>

                    @if ($payments->isNotEmpty())
                        <form method="post" action="{{ url('/cash-close') }}" class="row">
                            {{ csrf_field() }}
                            <input type="hidden" name="user_id" value="{{ $userId }}">
                            <div class="col-md-3 form-group"><label>Cash counted in the drawer</label><input type="number" step="0.01" min="0" name="counted_cash" class="form-control" placeholder="{{ number_format($totals['cash'], 2, '.', '') }}"></div>
                            <div class="col-md-6 form-group"><label>Comment</label><input name="comment" class="form-control" maxlength="500" placeholder="Handed to, shortages, anything unusual"></div>
                            <div class="col-md-3 form-group"><label>&nbsp;</label><button class="btn btn-primary btn-block" onclick="return confirm('Close this shift?')">Sale close</button></div>
                        </form>
                        <p class="help-block">Closing locks these payments into the shift. Any difference between the counted cash and the cash taken is recorded on the slip.</p>
                    @endif

                    <h4>Sale history</h4>
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed">
                        <thead><tr><th>Closed</th><th>Cashier</th><th class="text-right">Payments</th><th class="text-right">Cash</th><th class="text-right">Total</th><th class="text-right">Counted</th><th class="text-right">Difference</th><th></th></tr></thead>
                        <tbody>
                        @forelse ($history as $h)
                            <tr>
                                <td>{{ \Illuminate\Support\Str::of($h->closed_at)->substr(0, 16) }}</td>
                                <td>{{ $userNames[$h->user_id] ?? $h->user_id }}</td>
                                <td class="text-right">{{ $h->payments_count }}</td>
                                <td class="text-right">{{ number_format((float) $h->cash, 2) }}</td>
                                <td class="text-right">{{ number_format((float) $h->total, 2) }}</td>
                                <td class="text-right">{{ $h->counted_cash !== null ? number_format((float) $h->counted_cash, 2) : '-' }}</td>
                                <td class="text-right {{ (float) $h->difference < 0 ? 'text-danger' : '' }}">{{ $h->difference !== null ? number_format((float) $h->difference, 2) : '-' }}</td>
                                <td><a href="{{ url('/cash-close/' . $h->id) }}" class="btn btn-default btn-xs">Slip</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-muted">No closed shifts yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
