{{--
  ICTHospital - one admission.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    @if (Session::get('error'))
        <div class="alert alert-danger">
            <button data-dismiss="alert" class="close" type="button">&times;</button>
            {{ Session::get('error') }}
        </div>
    @endif

    <div class="row">
        <div class="box col-md-10">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-log-in"></i>
                        {{ $patient ? $patient->name : 'Unknown patient' }}
                        <small>{{ $admission->d_time ? 'discharged' : 'on the ward' }}</small>
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/admissions') }}" class="btn btn-default btn-sm">Back to admissions</a>
                    </div>
                </div>
                <div class="box-content">

                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th style="width:220px">Patient</th>
                                <td>
                                    @if ($patient)
                                        <a href="{{ url('/patients/' . $patient->id) }}">{{ $patient->name }}</a>
                                        ({{ $patient->patient_id }})
                                    @else
                                        Unknown
                                    @endif
                                </td>
                            </tr>
                            <tr><th>Ward</th><td>{{ $categories[$admission->category] ?? '-' }}</td></tr>
                            <tr><th>Bed</th><td>{{ $bed ? $bed->number : $admission->number }}</td></tr>
                            <tr><th>Admitted</th><td>{{ $admission->a_time }}</td></tr>
                            <tr>
                                <th>Discharged</th>
                                <td>{{ $admission->d_time ?: 'Still on the ward' }}</td>
                            </tr>
                            <tr>
                                <th>Length of stay</th>
                                <td>
                                    @php($from = $admission->a_time ? \Carbon\Carbon::parse($admission->a_time) : null)
                                    @php($to = $admission->d_time ? \Carbon\Carbon::parse($admission->d_time) : now())
                                    {{ $from ? $from->diffForHumans($to, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) : '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    @unless ($admission->d_time)
                        <form role="form" method="post"
                              action="{{ url('/admissions/' . $admission->id . '/discharge') }}" class="form-inline">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Discharge at</label>
                                <input type="datetime-local" name="d_time" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-primary">Discharge</button>
                            <span class="help-block">Leave the time blank to discharge now.</span>
                        </form>
                    @endunless

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
