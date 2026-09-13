{{--
  ICTHospital - hospital departments.

  Inline edit, one form per row, declared above the table and referenced by the HTML
  form attribute. A form element cannot wrap a table row, and nesting them in the cells
  makes the browser reparent the markup and post the wrong row.

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
                    <h2><i class="glyphicon glyphicon-tower"></i> Departments</h2>
                    <div class="box-icon">
                        <a href="{{ url('/doctors') }}" class="btn btn-default btn-sm">Doctors</a>
                    </div>
                </div>
                <div class="box-content">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($unlisted->isNotEmpty())
                        <div class="alert alert-warning">
                            <p>
                                <strong>{{ $unlisted->count() }} department(s) are in use by doctors but not on this
                                list:</strong> {{ $unlisted->implode(', ') }}.
                            </p>
                            <p>
                                They were typed into the doctor form before this screen existed. Adopting them puts
                                them on the list so they can be renamed properly from here.
                            </p>
                            <form action="{{ url('/departments/adopt') }}" method="post" style="display:inline">
                                {{ csrf_field() }}
                                <button type="submit" class="btn btn-primary btn-sm">Adopt all {{ $unlisted->count() }}</button>
                            </form>
                        </div>
                    @endif

                    @foreach ($departments as $department)
                        <form id="dept-save-{{ $department->id }}" action="{{ url('/departments/' . $department->id) }}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PUT">
                        </form>
                        <form id="dept-del-{{ $department->id }}" action="{{ url('/departments/' . $department->id) }}" method="post"
                              onsubmit="return confirm('Remove {{ $department->name }}?');">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="DELETE">
                        </form>
                    @endforeach

                    @if ($departments->isEmpty())
                        <p class="text-muted">
                            No departments yet. The doctor form offers this list, so filling it in keeps the
                            spelling consistent across the register.
                        </p>
                    @else
                        <p class="text-muted">
                            Renaming a department here moves every doctor in it to the new name, because the
                            doctor record stores the department as text.
                        </p>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:28%">Department</th>
                                    <th style="width:48%">Description</th>
                                    <th style="width:12%">Doctors</th>
                                    <th style="width:12%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($departments as $department)
                                    <tr>
                                        <td>
                                            <input type="text" name="name" class="form-control input-sm" required
                                                   form="dept-save-{{ $department->id }}" value="{{ $department->name }}">
                                        </td>
                                        <td>
                                            <input type="text" name="description" class="form-control input-sm"
                                                   form="dept-save-{{ $department->id }}" value="{{ $department->description }}">
                                        </td>
                                        <td>
                                            @php($n = $usage[$department->name] ?? 0)
                                            @if ($n > 0)
                                                <a href="{{ url('/doctors?q=' . urlencode($department->name)) }}">{{ $n }}</a>
                                            @else
                                                0
                                            @endif
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-primary btn-sm"
                                                    form="dept-save-{{ $department->id }}">Save</button>
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                    form="dept-del-{{ $department->id }}">
                                                <i class="glyphicon glyphicon-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <h4>Add a department</h4>
                    <form role="form" action="{{ url('/departments') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <input type="text" name="name" class="form-control" placeholder="Cardiology" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <input type="text" name="description" class="form-control" placeholder="Description">
                            </div>
                            <div class="col-md-2 form-group">
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
