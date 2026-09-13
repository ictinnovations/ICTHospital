{{--
  ICTHospital - invoice list.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
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
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-usd"></i> Invoices
                        <small>{{ number_format($owed, 2) }} outstanding</small>
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/invoices/create') }}" class="btn btn-primary btn-sm">
                            <i class="glyphicon glyphicon-plus"></i> Raise invoice
                        </a>
                        <a href="{{ url('/services') }}" class="btn btn-default btn-sm">
                            <i class="glyphicon glyphicon-tags"></i> Services and prices
                        </a>
                    </div>
                </div>
                <div class="box-content">

                    <ul class="nav nav-tabs">
                        <li class="{{ $show === 'outstanding' ? 'active' : '' }}">
                            <a href="{{ url('/invoices?show=outstanding') }}">Outstanding</a>
                        </li>
                        <li class="{{ $show === 'settled' ? 'active' : '' }}">
                            <a href="{{ url('/invoices?show=settled') }}">Settled</a>
                        </li>
                        <li class="{{ $show === 'all' ? 'active' : '' }}">
                            <a href="{{ url('/invoices?show=all') }}">All</a>
                        </li>
                    </ul>
                    <br>

                    <form role="form" action="{{ url('/invoices') }}" method="get" class="form-inline">
                        <input type="hidden" name="show" value="{{ $show }}">
                        <div class="form-group">
                            <label>Patient</label>
                            <select name="patient" class="form-control">
                                <option value="">Anyone</option>
                                @foreach ($patients as $p)
                                    <option value="{{ $p->id }}" {{ (string) $patientId === (string) $p->id ? 'selected' : '' }}>
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-default">Filter</button>
                    </form>

                    <br>

                    @if ($invoices->isEmpty())
                        <p class="text-muted">Nothing to show.</p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Charges</th>
                                    <th class="text-right">Total</th>
                                    <th class="text-right">Paid</th>
                                    <th class="text-right">Balance</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->date }}</td>
                                        <td>
                                            <a href="{{ url('/patients/' . $invoice->patient) }}">
                                                {{ $invoice->patient_name ?: 'Unknown' }}
                                            </a>
                                        </td>
                                        <td>
                                            @foreach ($invoice->items as $item)
                                                <span class="label label-default">{{ $item->summary() }}</span>
                                            @endforeach
                                        </td>
                                        <td class="text-right">{{ number_format((float) $invoice->gross_total, 2) }}</td>
                                        <td class="text-right">{{ number_format($invoice->paid(), 2) }}</td>
                                        <td class="text-right">{{ number_format($invoice->balance(), 2) }}</td>
                                        <td>
                                            @php($state = $invoice->settlement())
                                            <span class="label label-{{ $state === 'paid' ? 'success' : ($state === 'part paid' ? 'warning' : 'danger') }}">
                                                {{ ucfirst($state) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a class="btn btn-default btn-sm" href="{{ url('/invoices/' . $invoice->id) }}">
                                                <i class="glyphicon glyphicon-eye-open"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="text-muted">{{ $invoices->count() }} invoice(s).</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
