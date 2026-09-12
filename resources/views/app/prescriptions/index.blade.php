{{--
  ICTHospital - prescription list.

  The drug filter is the query the legacy free text column made impossible, so it
  is on the screen rather than buried.

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
                    <h2><i class="glyphicon glyphicon-list-alt"></i> Prescriptions</h2>
                    <div class="box-icon">
                        <a href="{{ url('/prescriptions/create') }}" class="btn btn-primary btn-sm">
                            <i class="glyphicon glyphicon-plus"></i> Write prescription
                        </a>
                    </div>
                </div>
                <div class="box-content">

                    <form role="form" action="{{ url('/prescriptions') }}" method="get" class="form-inline">
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
                        <div class="form-group">
                            <label>Doctor</label>
                            <select name="doctor" class="form-control">
                                <option value="">Anyone</option>
                                @foreach ($doctors as $d)
                                    <option value="{{ $d->id }}" {{ (string) $doctorId === (string) $d->id ? 'selected' : '' }}>
                                        {{ $d->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Contains drug</label>
                            <input type="text" name="drug" class="form-control" value="{{ $drug }}"
                                   placeholder="e.g. amoxicillin">
                        </div>
                        <button type="submit" class="btn btn-default">Search</button>
                    </form>

                    <br>

                    @if ($prescriptions->total() === 0)
                        <p class="text-muted">Nothing matches.</p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Medicines</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prescriptions as $prescription)
                                    <tr>
                                        <td>{{ $prescription->date }}</td>
                                        <td>
                                            <a href="{{ url('/patients/' . $prescription->patient) }}">
                                                {{ optional($patients->firstWhere('id', (int) $prescription->patient))->name ?? 'Unknown' }}
                                            </a>
                                        </td>
                                        <td>{{ optional($doctors->firstWhere('id', (int) $prescription->doctor))->name ?? '-' }}</td>
                                        <td>
                                            @foreach ($prescription->items as $item)
                                                <span class="label label-default">{{ $item->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a class="btn btn-default btn-sm" href="{{ url('/prescriptions/' . $prescription->id) }}">
                                                <i class="glyphicon glyphicon-eye-open"></i>
                                            </a>
                                            <a class="btn btn-default btn-sm" href="{{ url('/prescriptions/' . $prescription->id . '/edit') }}">
                                                <i class="glyphicon glyphicon-edit"></i>
                                            </a>
                                            <form action="{{ url('/prescriptions/' . $prescription->id) }}" method="post"
                                                  style="display:inline"
                                                  onsubmit="return confirm('Remove this prescription?');">
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
                        <p class="text-muted">{{ $prescriptions->total() }} prescription(s).</p>
                        {!! $prescriptions->links() !!}
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
