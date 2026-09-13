{{--
  ICTHospital - doctor register.

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
                    <h2><i class="glyphicon glyphicon-briefcase"></i> Doctors</h2>
                    <div class="box-icon">
                        <a href="{{ url('/doctors/create') }}" class="btn btn-primary btn-sm">
                            <i class="glyphicon glyphicon-plus"></i> Add doctor
                        </a>
                    </div>
                </div>
                <div class="box-content">

                    <form role="form" action="{{ url('/doctors') }}" method="get" class="form-inline">
                        <div class="form-group">
                            <input type="text" name="q" class="form-control" style="min-width:280px"
                                   value="{{ $term }}" placeholder="Name, department, phone or email">
                        </div>
                        <button type="submit" class="btn btn-default">Search</button>
                        @if ($term !== '')
                            <a href="{{ url('/doctors') }}" class="btn btn-link">Clear</a>
                        @endif
                    </form>

                    <br>

                    @if ($doctors->total() === 0)
                        <p class="text-muted">
                            @if ($term !== '')
                                Nothing matched "{{ $term }}".
                            @else
                                No doctors on the register yet. Appointments, prescriptions, lab requests and
                                invoices all pick a doctor from this list, so add at least one before using them.
                            @endif
                        </p>
                    @else
                        <table class="table table-striped table-bordered bootstrap-datatable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Upcoming appointments</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($doctors as $doctor)
                                    <tr>
                                        <td><a href="{{ url('/doctors/' . $doctor->id) }}">{{ $doctor->name }}</a></td>
                                        <td>{{ $doctor->department }}</td>
                                        <td>{{ $doctor->phone }}</td>
                                        <td>{{ $doctor->email }}</td>
                                        <td>{{ $upcoming[$doctor->id] ?? 0 }}</td>
                                        <td>
                                            <a class="btn btn-default btn-sm" href="{{ url('/doctors/' . $doctor->id . '/edit') }}">
                                                <i class="glyphicon glyphicon-edit"></i>
                                            </a>
                                            <form action="{{ url('/doctors/' . $doctor->id) }}" method="post"
                                                  style="display:inline"
                                                  onsubmit="return confirm('Remove {{ $doctor->name }} from the register?');">
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

                        <p class="text-muted">{{ $doctors->total() }} doctor(s).</p>
                        {!! $doctors->links() !!}
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
