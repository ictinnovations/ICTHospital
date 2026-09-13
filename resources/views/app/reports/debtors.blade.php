{{--
  ICTHospital - who owes money, and for how long.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-usd"></i> Debtors
                        <small>{{ number_format($total, 2) }} outstanding across {{ $rows->count() }} patient(s)</small>
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/reports') }}" class="btn btn-default btn-sm">All reports</a>
                    </div>
                </div>
                <div class="box-content">

                    @if ($rows->isEmpty())
                        <p class="text-muted">Nothing outstanding. Every invoice is settled in full.</p>
                    @else
                        <table class="table table-bordered" style="max-width:520px">
                            <thead>
                                <tr><th>Age</th><th>Amount</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($buckets as $label => $amount)
                                    <tr>
                                        <td>{{ $label }}</td>
                                        <td>{{ number_format($amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <p class="text-muted">
                            Age is taken from the oldest unsettled invoice for that patient, not from the
                            most recent one, so a long standing balance does not look fresh because
                            something new was added to it.
                        </p>

                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Phone</th>
                                    <th style="width:12%">Invoices</th>
                                    <th style="width:15%">Owed</th>
                                    <th style="width:15%">Oldest</th>
                                    <th style="width:10%">Days</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr class="{{ ($row['days'] ?? 0) > 90 ? 'danger' : '' }}">
                                        <td>
                                            @if ($row['patient'])
                                                <a href="{{ url('/patients/' . $row['patient']->id) }}">{{ $row['patient']->name }}</a>
                                                <span class="text-muted">{{ $row['patient']->patient_id }}</span>
                                            @else
                                                <span class="text-muted">Patient removed</span>
                                            @endif
                                        </td>
                                        <td>{{ $row['patient']->phone ?? '' }}</td>
                                        <td>{{ $row['invoices'] }}</td>
                                        <td>{{ number_format($row['owed'], 2) }}</td>
                                        <td>{{ $row['oldest'] }}</td>
                                        <td>{{ $row['days'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
