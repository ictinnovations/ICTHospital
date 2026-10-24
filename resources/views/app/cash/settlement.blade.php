{{--
  ICTHospital - final settlement for one patient across all their invoices.

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
                    <h2><i class="glyphicon glyphicon-check"></i> Final settlement{{ $patient ? ': ' . $patient->name . ' (' . $patient->patient_id . ')' : '' }}</h2>
                </div>
                <div class="box-content">
                    <form method="get" class="form-inline" style="margin-bottom:14px">
                        <label>Patient</label>
                        <select name="patient" class="form-control" onchange="this.form.submit()">
                            <option value="">Choose a patient</option>
                            @foreach ($patients as $p)<option value="{{ $p->id }}" {{ $patient && $patient->id === $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->patient_id }})</option>@endforeach
                        </select>
                    </form>

                    @if ($patient)
                        <div class="row">
                            @foreach (['Base price' => $base, 'Discount' => $discount, 'Net amount' => $net, 'Paid' => $paid, 'Written off' => $writtenOff, 'Receivable' => $receivable] as $label => $value)
                                <div class="col-md-2 col-xs-6"><div class="well well-sm text-center" style="{{ $label === 'Receivable' && $value > 0 ? 'background:#fcf8e3' : '' }}"><small>{{ $label }}</small><br><strong style="font-size:17px">{{ number_format($value, 2) }}</strong></div></div>
                            @endforeach
                        </div>

                        <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed">
                            <thead><tr><th>Invoice</th><th>Date</th><th>Setting</th><th>Charges</th><th class="text-right">Base</th><th class="text-right">Discount</th><th class="text-right">Net</th><th class="text-right">Paid</th><th class="text-right">Balance</th><th>Status</th></tr></thead>
                            <tbody>
                            @forelse ($invoices as $inv)
                                <tr>
                                    <td><a href="{{ url('/invoices/' . $inv->id) }}">#{{ $inv->id }}</a></td><td>{{ $inv->date }}</td><td>{{ strtoupper($inv->care_setting ?: 'opd') }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($inv->items->map->summary()->implode(', '), 60) }}</td>
                                    <td class="text-right">{{ number_format((float) $inv->amount, 2) }}</td><td class="text-right">{{ number_format((float) $inv->discount, 2) }}</td>
                                    <td class="text-right">{{ number_format((float) $inv->gross_total, 2) }}</td><td class="text-right">{{ number_format($inv->paid(), 2) }}</td>
                                    <td class="text-right">{{ number_format($inv->balance(), 2) }}</td><td>{{ ucfirst($inv->settlement()) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-muted">No invoices for this patient.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                        </div>

                        @if ($receivable > 0)
                            <h4>Settle</h4>
                            <form method="post" action="{{ url('/settlement/' . $patient->id) }}" class="row">
                                {{ csrf_field() }}
                                <div class="col-md-2 form-group"><label>Amount received</label><input type="number" step="0.01" min="0" name="amount" class="form-control" value="{{ number_format($receivable, 2, '.', '') }}"></div>
                                <div class="col-md-2 form-group"><label>Mode</label><select name="method" class="form-control">@foreach (\App\Support\PaymentModes::MODES as $m)<option>{{ $m }}</option>@endforeach</select></div>
                                <div class="col-md-2 form-group"><label>Reference</label><input name="reference" class="form-control"></div>
                                @if (\App\Support\Permissions::allows('payment_update'))
                                    <div class="col-md-2 form-group"><label>Write off</label><input type="number" step="0.01" min="0" name="write_off" class="form-control" placeholder="0.00"></div>
                                    <div class="col-md-2 form-group"><label>Write-off reason</label><input name="write_off_reason" class="form-control" maxlength="120"></div>
                                @endif
                                <div class="col-md-2 form-group"><label>&nbsp;</label><button class="btn btn-primary btn-block">Final settlement</button></div>
                            </form>
                            <p class="help-block">The amount is applied to the oldest invoices first. A write-off settles what is left without counting as money collected.</p>
                        @else
                            <p class="text-success"><strong>Nothing outstanding.</strong></p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
