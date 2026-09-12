{{--
  ICTHospital - patient list.

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
                    <h2><i class="glyphicon glyphicon-user"></i> Patients</h2>
                    <div class="box-icon">
                        <a href="{{ url('/patients/create') }}" class="btn btn-primary btn-sm">
                            <i class="glyphicon glyphicon-plus"></i> Register patient
                        </a>
                    </div>
                </div>
                <div class="box-content">

                    <form role="form" action="{{ url('/patients') }}" method="get" class="form-inline">
                        <div class="form-group">
                            <input type="text" name="q" class="form-control" style="min-width:280px"
                                   value="{{ $term }}"
                                   placeholder="Name, patient ID, phone or email">
                        </div>
                        <button type="submit" class="btn btn-default">Search</button>
                        @if ($term !== '')
                            <a href="{{ url('/patients') }}" class="btn btn-link">Clear</a>
                        @endif
                    </form>

                    <br>

                    @if ($patients->total() === 0)
                        <p class="text-muted">
                            @if ($term !== '')
                                Nothing matched "{{ $term }}".
                            @else
                                No patients registered yet.
                            @endif
                        </p>
                    @else
                        <table class="table table-striped table-bordered bootstrap-datatable">
                            <thead>
                                <tr>
                                    <th>Patient ID</th>
                                    <th>Name</th>
                                    <th>Age</th>
                                    <th>Sex</th>
                                    <th>Phone</th>
                                    <th>Doctor</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($patients as $patient)
                                    <tr>
                                        <td>{{ $patient->patient_id }}</td>
                                        <td><a href="{{ url('/patients/' . $patient->id) }}">{{ $patient->name }}</a></td>
                                        <td>{{ $patient->age }}</td>
                                        <td>{{ ucfirst($patient->sex) }}</td>
                                        <td>{{ $patient->phone }}</td>
                                        <td>{{ $patient->doctor }}</td>
                                        <td>{{ $patient->add_date }}</td>
                                        <td>
                                            <a class="btn btn-default btn-sm" href="{{ url('/patients/' . $patient->id . '/edit') }}">
                                                <i class="glyphicon glyphicon-edit"></i>
                                            </a>
                                            <form action="{{ url('/patients/' . $patient->id) }}" method="post"
                                                  style="display:inline"
                                                  onsubmit="return confirm('Delete {{ $patient->name }}? This cannot be undone.');">
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

                        <p class="text-muted">{{ $patients->total() }} patient(s).</p>
                        {!! $patients->links() !!}
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
