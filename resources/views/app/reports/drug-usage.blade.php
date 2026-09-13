{{--
  ICTHospital - prescribed against dispensed.

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
                    <h2><i class="glyphicon glyphicon-plus-sign"></i> Drug usage
                        <small>{{ $from->toDateString() }} to {{ $to->toDateString() }}</small>
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/reports') }}" class="btn btn-default btn-sm">All reports</a>
                    </div>
                </div>
                <div class="box-content">

                    <form role="form" action="{{ url('/reports/drug-usage') }}" method="get" class="form-inline">
                        <div class="form-group">
                            <label>From</label>
                            <input type="date" name="from" class="form-control" value="{{ $from->toDateString() }}">
                        </div>
                        <div class="form-group">
                            <label>To</label>
                            <input type="date" name="to" class="form-control" value="{{ $to->toDateString() }}">
                        </div>
                        <button type="submit" class="btn btn-default">Show</button>
                    </form>

                    <br>

                    @if ($rows->isEmpty())
                        <p class="text-muted">Nothing was prescribed or dispensed in that range.</p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Medicine</th>
                                    <th style="width:15%">Times prescribed</th>
                                    <th style="width:15%">Units dispensed</th>
                                    <th style="width:15%">Value dispensed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr class="{{ $row['prescribed'] > 0 && $row['dispensed'] === 0 ? 'warning' : '' }}">
                                        <td>{{ $row['name'] }}</td>
                                        <td>{{ $row['prescribed'] }}</td>
                                        <td>{{ $row['dispensed'] }}</td>
                                        <td>{{ number_format($row['value'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total dispensed value</th>
                                    <th></th>
                                    <th></th>
                                    <th>{{ number_format($totalValue, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>

                        <p class="text-muted">
                            Rows highlighted in amber were prescribed in this period and never dispensed here.
                            That is normally stock, or a patient filling the script somewhere else, and it is
                            worth checking before reordering.
                        </p>
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
