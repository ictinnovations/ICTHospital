{{--
  ICTHospital - pharmacy receipt.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
<style>
@media print {
    .sidebar-nav, .navbar, .box-icon, .btn { display: none !important; }
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

    <div class="row">
        <div class="box col-md-8">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-shopping-cart"></i>
                        Receipt <small>{{ $sale->date }}</small>
                    </h2>
                    <div class="box-icon">
                        <a href="javascript:window.print()" class="btn btn-default btn-sm">
                            <i class="glyphicon glyphicon-print"></i> Print
                        </a>
                        <a href="{{ url('/pharmacy') }}" class="btn btn-default btn-sm">Back to sales</a>
                    </div>
                </div>
                <div class="box-content">

                    <p>
                        <strong>Patient:</strong>
                        @if ($patient)
                            <a href="{{ url('/patients/' . $patient->id) }}">{{ $patient->name }}</a>
                            ({{ $patient->patient_id }})
                        @else
                            Counter sale
                        @endif
                    </p>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Medicine</th>
                                <th class="text-right">Quantity</th>
                                <th class="text-right">Unit</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->items as $item)
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
                                <th class="text-right">{{ number_format((float) $sale->amount, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Discount</th>
                                <th class="text-right">{{ number_format((float) $sale->discount, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Tax</th>
                                <th class="text-right">{{ number_format((float) $sale->vat, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Total</th>
                                <th class="text-right">{{ number_format((float) $sale->gross_total, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Received</th>
                                <th class="text-right">{{ number_format((float) $sale->amount_received, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
