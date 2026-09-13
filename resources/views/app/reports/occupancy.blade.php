{{--
  ICTHospital - bed occupancy over a date range.

  The per day bar is drawn with a plain div rather than a charting library,
  because adding a JavaScript dependency for one horizontal bar is not worth the
  page weight or the supply chain.

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
                    <h2><i class="glyphicon glyphicon-th"></i> Bed occupancy
                        <small>{{ $from->toDateString() }} to {{ $to->toDateString() }}</small>
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/reports') }}" class="btn btn-default btn-sm">All reports</a>
                    </div>
                </div>
                <div class="box-content">

                    <form role="form" action="{{ url('/reports/occupancy') }}" method="get" class="form-inline">
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

                    <table class="table table-bordered" style="max-width:640px">
                        <tbody>
                            <tr><th style="width:45%">Beds on the ward board</th><td>{{ $totalBeds }}</td></tr>
                            <tr><th>Occupied right now</th><td>{{ $currentlyOccupied }}</td></tr>
                            <tr><th>Busiest day in range</th><td>{{ $peak }}</td></tr>
                            <tr><th>Discharges in range</th><td>{{ $discharges }}</td></tr>
                            <tr>
                                <th>Average length of stay</th>
                                <td>
                                    @if ($averageStay === null)
                                        <span class="text-muted">No completed stays in range</span>
                                    @else
                                        {{ $averageStay }} day(s)
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    @if ($totalBeds === 0)
                        <p class="text-muted">
                            No beds are set up yet, so occupancy has nothing to measure against.
                            Add them on the <a href="{{ url('/beds') }}">ward board</a>.
                        </p>
                    @endif

                    @if ($byWard->isNotEmpty())
                        <h4>Admissions by ward</h4>
                        <table class="table table-bordered" style="max-width:420px">
                            <tbody>
                                @foreach ($byWard as $ward => $count)
                                    <tr><td>{{ $ward }}</td><td style="width:80px">{{ $count }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <h4>Day by day</h4>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width:16%">Date</th>
                                <th style="width:12%">Occupied</th>
                                <th>Of {{ $totalBeds }} bed(s)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($days as $day)
                                <tr>
                                    <td>{{ $day['date'] }}</td>
                                    <td>{{ $day['occupied'] }}</td>
                                    <td>
                                        <div style="background:#eef2f6;border-radius:3px;height:16px;width:100%">
                                            <div style="background:{{ $day['percent'] >= 90 ? '#b4443c' : '#2f6fb5' }};
                                                        height:16px;border-radius:3px;
                                                        width:{{ min(100, $day['percent']) }}%"></div>
                                        </div>
                                        <span class="text-muted">{{ $day['percent'] }}%</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
