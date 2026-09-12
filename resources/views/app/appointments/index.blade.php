{{--
  ICTHospital - appointment day view.

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
                    <h2><i class="glyphicon glyphicon-calendar"></i> Appointments</h2>
                    <div class="box-icon">
                        <a href="{{ url('/appointments/create?date=' . $date) }}" class="btn btn-primary btn-sm">
                            <i class="glyphicon glyphicon-plus"></i> Book appointment
                        </a>
                    </div>
                </div>
                <div class="box-content">

                    <form role="form" action="{{ url('/appointments') }}" method="get" class="form-inline">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" value="{{ $date }}">
                        </div>
                        <div class="form-group">
                            <label>Doctor</label>
                            <select name="doctor" class="form-control">
                                <option value="">All doctors</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}"
                                        {{ (string) $doctorId === (string) $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">Any</option>
                                @foreach ($statuses as $value)
                                    <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ ucfirst($value) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-default">Show</button>
                    </form>

                    <br>

                    @if ($appointments->isEmpty())
                        <p class="text-muted">Nothing booked for {{ $date }}.</p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($appointments as $appointment)
                                    <tr>
                                        <td>{{ $appointment->time_slot }}</td>
                                        <td>
                                            <a href="{{ url('/patients/' . $appointment->patient) }}">
                                                {{ $patients[$appointment->patient] ?? 'Unknown patient' }}
                                            </a>
                                        </td>
                                        <td>{{ optional($doctors->firstWhere('id', $appointment->doctor))->name ?? '-' }}</td>
                                        <td>
                                            @php($label = ['cancelled' => 'danger', 'completed' => 'success', 'no show' => 'warning'][$appointment->status] ?? 'info')
                                            <span class="label label-{{ $label }}">{{ ucfirst($appointment->status) }}</span>
                                        </td>
                                        <td>{{ $appointment->remarks }}</td>
                                        <td>
                                            <a class="btn btn-default btn-sm" href="{{ url('/appointments/' . $appointment->id) }}">
                                                <i class="glyphicon glyphicon-eye-open"></i>
                                            </a>
                                            <a class="btn btn-default btn-sm" href="{{ url('/appointments/' . $appointment->id . '/edit') }}">
                                                <i class="glyphicon glyphicon-edit"></i>
                                            </a>
                                            <form action="{{ url('/appointments/' . $appointment->id) }}" method="post"
                                                  style="display:inline"
                                                  onsubmit="return confirm('Remove this appointment?');">
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
                        <p class="text-muted">{{ $appointments->count() }} appointment(s).</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
