{{--
  ICTHospital - admissions list.

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
                    <h2><i class="glyphicon glyphicon-log-in"></i> Admissions</h2>
                    <div class="box-icon">
                        <a href="{{ url('/admissions/create') }}" class="btn btn-primary btn-sm">
                            <i class="glyphicon glyphicon-plus"></i> Admit patient
                        </a>
                    </div>
                </div>
                <div class="box-content">

                    <ul class="nav nav-tabs">
                        <li class="{{ $show === 'current' ? 'active' : '' }}">
                            <a href="{{ url('/admissions?show=current') }}">On the ward</a>
                        </li>
                        <li class="{{ $show === 'discharged' ? 'active' : '' }}">
                            <a href="{{ url('/admissions?show=discharged') }}">Discharged</a>
                        </li>
                        <li class="{{ $show === 'all' ? 'active' : '' }}">
                            <a href="{{ url('/admissions?show=all') }}">All</a>
                        </li>
                    </ul>
                    <br>

                    @if ($admissions->isEmpty())
                        <p class="text-muted">Nothing to show.</p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Ward</th>
                                    <th>Bed</th>
                                    <th>Admitted</th>
                                    <th>Discharged</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($admissions as $admission)
                                    <tr>
                                        <td>
                                            <a href="{{ url('/patients/' . $admission->patient) }}">
                                                {{ $patients[$admission->patient] ?? 'Unknown patient' }}
                                            </a>
                                        </td>
                                        <td>{{ $categories[$admission->category] ?? '-' }}</td>
                                        <td>{{ optional($beds->get($admission->bed_id))->number ?? $admission->number }}</td>
                                        <td>{{ $admission->a_time }}</td>
                                        <td>
                                            @if ($admission->d_time)
                                                {{ $admission->d_time }}
                                            @else
                                                <span class="label label-warning">On the ward</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-default btn-sm" href="{{ url('/admissions/' . $admission->id) }}">
                                                <i class="glyphicon glyphicon-eye-open"></i>
                                            </a>
                                            @unless ($admission->d_time)
                                                <form action="{{ url('/admissions/' . $admission->id . '/discharge') }}"
                                                      method="post" style="display:inline"
                                                      onsubmit="return confirm('Discharge this patient now?');">
                                                    {{ csrf_field() }}
                                                    <button type="submit" class="btn btn-primary btn-sm">Discharge</button>
                                                </form>
                                            @endunless
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="text-muted">{{ $admissions->count() }} record(s).</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
