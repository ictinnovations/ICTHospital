{{--
  ICTHospital - printable shift close slip.

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
                    <h2><i class="glyphicon glyphicon-briefcase"></i> Shift close #{{ $close->id }}</h2>
                    <div class="box-icon">
                        <button onclick="window.print()" class="btn btn-default btn-sm">Print</button>
                        <a href="{{ url('/cash-close') }}" class="btn btn-default btn-sm">Back</a>
                    </div>
                </div>
                <div class="box-content">
                    <table class="table table-condensed" style="max-width:520px">
                        <tr><th>Cashier</th><td>{{ $cashier }}</td></tr>
                        <tr><th>From</th><td>{{ $close->opened_at }}</td></tr>
                        <tr><th>Closed</th><td>{{ $close->closed_at }}</td></tr>
                        @foreach (\App\Support\PaymentModes::labels() as $k => $label)
                            <tr><th>{{ $label }}</th><td>{{ number_format((float) $close->$k, 2) }}</td></tr>
                        @endforeach
                        <tr class="success"><th>Total collected</th><td><strong>{{ number_format((float) $close->total, 2) }}</strong></td></tr>
                        <tr><th>Cash counted</th><td>{{ $close->counted_cash !== null ? number_format((float) $close->counted_cash, 2) : 'Not counted' }}</td></tr>
                        <tr><th>Difference</th><td class="{{ (float) $close->difference < 0 ? 'text-danger' : '' }}">{{ $close->difference !== null ? number_format((float) $close->difference, 2) : '-' }}</td></tr>
                        @if ($close->comment)<tr><th>Comment</th><td>{{ $close->comment }}</td></tr>@endif
                    </table>
                    <h4>Payments in this shift</h4>
                    <table class="table table-striped table-condensed">
                        <thead><tr><th>Time</th><th>Invoice</th><th>Patient</th><th>Mode</th><th class="text-right">Amount</th></tr></thead>
                        <tbody>
                        @foreach ($payments as $p)
                            <tr><td>{{ $p->paid_at }}</td><td>#{{ $p->payment_id }}</td><td>{{ optional($p->invoice)->patient_name }}</td><td>{{ $p->method ?: 'Cash' }}</td><td class="text-right">{{ number_format((float) $p->amount, 2) }}</td></tr>
                        @endforeach
                        </tbody>
                    </table>
                    <p style="margin-top:40px">Handed over by ______________________ &nbsp; &nbsp; Received by ______________________</p>
                </div>
            </div>
        </div>
    </div>
@stop
