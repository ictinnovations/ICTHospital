{{--
  ICTHospital - outstanding lab work.

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
                    <h2><i class="glyphicon glyphicon-tint"></i> Outstanding lab work
                        <small>{{ $rows->count() }} test(s) waiting, {{ $overdue }} over three days</small>
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/reports') }}" class="btn btn-default btn-sm">All reports</a>
                    </div>
                </div>
                <div class="box-content">

                    @if ($rows->isEmpty())
                        <p class="text-muted">Nothing is waiting. Every requested test has a result against it.</p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Waiting</th>
                                    <th>Test</th>
                                    <th>Patient</th>
                                    <th>Requested</th>
                                    <th>Request</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr class="{{ ($row['days'] ?? 0) >= 3 ? 'danger' : '' }}">
                                        <td>
                                            @if ($row['days'] === null)
                                                <span class="text-muted">No date</span>
                                            @else
                                                {{ $row['days'] }} day{{ $row['days'] === 1 ? '' : 's' }}
                                            @endif
                                        </td>
                                        <td>{{ $row['test']->name }}</td>
                                        <td>
                                            @if ($row['patient'])
                                                <a href="{{ url('/patients/' . $row['patient']->id) }}">{{ $row['patient']->name }}</a>
                                            @else
                                                <span class="text-muted">Not recorded</span>
                                            @endif
                                        </td>
                                        <td>{{ $row['lab']->date ?? '' }}</td>
                                        <td>
                                            @if ($row['lab'])
                                                <a href="{{ url('/lab/' . $row['lab']->id) }}">Open request</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <h4>By test</h4>
                        <table class="table table-bordered" style="max-width:420px">
                            <tbody>
                                @foreach ($byTest as $name => $count)
                                    <tr>
                                        <td>{{ $name }}</td>
                                        <td style="width:80px">{{ $count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="text-muted">
                            A test near the top of that list and near the top of the waiting list as well is
                            usually a capacity problem rather than a one off.
                        </p>
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
