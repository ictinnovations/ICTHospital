{{--
  ICTHospital - invoice, payments and the balance.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
<style>
@media print {
    .sidebar-nav, .navbar, .box-icon, .btn, .take-payment { display: none !important; }
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
    @if (Session::get('error'))
        <div class="alert alert-danger">
            <button data-dismiss="alert" class="close" type="button">&times;</button>
            {{ Session::get('error') }}
        </div>
    @endif

    <div class="row">
        <div class="box col-md-9">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-usd"></i>
                        Invoice #{{ $invoice->id }} <small>{{ $invoice->date }}</small>
                    </h2>
                    <div class="box-icon">
                        <a href="javascript:window.print()" class="btn btn-default btn-sm">
                            <i class="glyphicon glyphicon-print"></i> Print
                        </a>
                        <a href="{{ url('/invoices') }}" class="btn btn-default btn-sm">Back to invoices</a>
                    </div>
                </div>
                <div class="box-content">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Patient:</strong>
                                @if ($patient)
                                    <a href="{{ url('/patients/' . $patient->id) }}">{{ $patient->name }}</a>
                                    ({{ $patient->patient_id }})
                                @else
                                    {{ $invoice->patient_name ?: 'Unknown' }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Doctor:</strong> {{ $doctor ? $doctor->name : ($invoice->doctor_name ?: '-') }}</p>
                            <p><strong>Status:</strong> {{ ucfirst($invoice->settlement()) }}</p>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Charge</th>
                                <th class="text-right">Quantity</th>
                                <th class="text-right">Unit</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice->items as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td class="text-right">{{ $item->quantity }}</td>
                                    <td class="text-right">{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="text-right">{{ number_format((float) $item->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Subtotal</th>
                                <th class="text-right">{{ number_format((float) $invoice->amount, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Discount</th>
                                <th class="text-right">{{ number_format((float) $invoice->discount, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Tax</th>
                                <th class="text-right">{{ number_format((float) $invoice->vat, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Total</th>
                                <th class="text-right">{{ number_format((float) $invoice->gross_total, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Paid</th>
                                <th class="text-right">{{ number_format($invoice->paid(), 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Balance</th>
                                <th class="text-right">{{ number_format($invoice->balance(), 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>

                    @if ($invoice->payments->isNotEmpty())
                        <h4>Payments</h4>
                        <table class="table table-striped">
                            <thead>
                                <tr><th>When</th><th>Method</th><th>Reference</th><th class="text-right">Amount</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($invoice->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->paid_at }}</td>
                                        <td>{{ $payment->method ?: '-' }}</td>
                                        <td>{{ $payment->reference ?: '-' }}</td>
                                        <td class="text-right">{{ number_format((float) $payment->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    @if ($invoice->balance() > 0.004)
                        <div class="take-payment">
                            <h4>Take a payment</h4>
                            <form role="form" method="post" action="{{ url('/invoices/' . $invoice->id . '/pay') }}"
                                  class="form-inline">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control"
                                           value="{{ number_format($invoice->balance(), 2, '.', '') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Method</label>
                                    <input type="text" name="method" class="form-control" placeholder="Cash, card">
                                </div>
                                <div class="form-group">
                                    <label>Reference</label>
                                    <input type="text" name="reference" class="form-control">
                                </div>
                                <button type="submit" class="btn btn-primary">Record payment</button>
                            </form>
                            <p class="help-block">
                                More than the outstanding balance is refused, rather than accepted and left as a
                                negative for somebody to puzzle over later.
                            </p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
