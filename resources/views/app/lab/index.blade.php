{{--
  ICTHospital - lab request list.

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

    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-tint"></i> Laboratory</h2>
                    <div class="box-icon">
                        <a href="{{ url('/lab/catalogue') }}" class="btn btn-default btn-sm">Test catalogue</a>
                        <a href="{{ url('/lab/create') }}" class="btn btn-primary btn-sm">
                            <i class="glyphicon glyphicon-plus"></i> Raise request
                        </a>
                    </div>
                </div>
                <div class="box-content">

                    <ul class="nav nav-tabs">
                        <li class="{{ $status === 'pending' ? 'active' : '' }}">
                            <a href="{{ url('/lab?status=pending') }}">Outstanding</a>
                        </li>
                        <li class="{{ $status === 'reported' ? 'active' : '' }}">
                            <a href="{{ url('/lab?status=reported') }}">Reported</a>
                        </li>
                        <li class="{{ $status === 'all' ? 'active' : '' }}">
                            <a href="{{ url('/lab?status=all') }}">All</a>
                        </li>
                    </ul>
                    <br>

                    <form role="form" action="{{ url('/lab') }}" method="get" class="form-inline">
                        <input type="hidden" name="status" value="{{ $status }}">
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

                    @if ($requests->total() === 0)
                        <p class="text-muted">Nothing to show.</p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Tests</th>
                                    <th>Progress</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $req)
                                    @php($done = $req->tests->filter->isReported()->count())
                                    <tr>
                                        <td>{{ $req->date }}</td>
                                        <td>
                                            <a href="{{ url('/patients/' . $req->patient) }}">
                                                {{ $req->patient_name ?: 'Unknown' }}
                                            </a>
                                        </td>
                                        <td>
                                            @foreach ($req->tests as $test)
                                                <span class="label label-{{ $test->isReported() ? 'success' : 'default' }}">
                                                    {{ $test->name }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            {{ $done }} of {{ $req->tests->count() }}
                                            @if ($req->status === 'partial')
                                                <span class="label label-warning">Part reported</span>
                                            @elseif ($req->status === 'reported')
                                                <span class="label label-success">Complete</span>
                                            @else
                                                <span class="label label-info">Waiting</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-default btn-sm" href="{{ url('/lab/' . $req->id) }}">
                                                <i class="glyphicon glyphicon-eye-open"></i> Results
                                            </a>
                                            <form action="{{ url('/lab/' . $req->id) }}" method="post"
                                                  style="display:inline"
                                                  onsubmit="return confirm('Remove this request and its results?');">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="glyphicon glyphicon-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="text-muted">{{ $requests->total() }} request(s).</p>
                        {!! $requests->links() !!}
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
