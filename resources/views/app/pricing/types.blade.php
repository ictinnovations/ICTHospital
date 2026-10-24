{{--
  ICTHospital - patient types: General, panel companies, charity, online platforms.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('content')
    @include('app.partials._alerts')
    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well">
                    <h2><i class="glyphicon glyphicon-tags"></i> Patient types</h2>
                    <div class="box-icon">
                        <a href="{{ url('/pricing') }}" class="btn btn-default btn-sm">Price list per type</a>
                    </div>
                </div>
                <div class="box-content">
                    <p class="help-block">Each patient has a type. The type decides which price list their invoices use and which column of the day book their money falls in (private, panel or charity).</p>

                    @foreach ($types as $t)
                        <form id="type-save-{{ $t->id }}" action="{{ url('/patient-types/' . $t->id) }}" method="post">{{ csrf_field() }}<input type="hidden" name="_method" value="PUT"></form>
                        <form id="type-del-{{ $t->id }}" action="{{ url('/patient-types/' . $t->id) }}" method="post" onsubmit="return confirm('Delete {{ $t->name }}?')">{{ csrf_field() }}<input type="hidden" name="_method" value="DELETE"></form>
                    @endforeach

                    <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead><tr><th>Name</th><th>Kind</th><th>Company or platform</th><th>Active</th><th>Patients</th><th></th></tr></thead>
                        <tbody>
                        @foreach ($types as $t)
                            <tr>
                                <td><input form="type-save-{{ $t->id }}" name="name" class="form-control input-sm" value="{{ $t->name }}" required></td>
                                <td><select form="type-save-{{ $t->id }}" name="kind" class="form-control input-sm">
                                    @foreach ($kinds as $k => $label)<option value="{{ $k }}" {{ $t->kind === $k ? 'selected' : '' }}>{{ $label }}</option>@endforeach
                                </select></td>
                                <td><input form="type-save-{{ $t->id }}" name="company" class="form-control input-sm" value="{{ $t->company }}"></td>
                                <td><input form="type-save-{{ $t->id }}" type="checkbox" name="active" value="1" {{ $t->active ? 'checked' : '' }}></td>
                                <td>{{ $counts[$t->id] ?? 0 }}</td>
                                <td class="text-nowrap">
                                    <button form="type-save-{{ $t->id }}" class="btn btn-primary btn-xs">Save</button>
                                    <a class="btn btn-default btn-xs" href="{{ url('/pricing?type=' . $t->id) }}">Prices</a>
                                    <button form="type-del-{{ $t->id }}" class="btn btn-danger btn-xs">Delete</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>

                    <h4>Add a patient type</h4>
                    <form action="{{ url('/patient-types') }}" method="post" class="row">
                        {{ csrf_field() }}
                        <div class="col-md-3 form-group"><input name="name" class="form-control" placeholder="e.g. State Life panel" required></div>
                        <div class="col-md-3 form-group"><select name="kind" class="form-control">@foreach ($kinds as $k => $label)<option value="{{ $k }}">{{ $label }}</option>@endforeach</select></div>
                        <div class="col-md-3 form-group"><input name="company" class="form-control" placeholder="Company or platform (optional)"></div>
                        <div class="col-md-2 form-group"><label><input type="checkbox" name="active" value="1" checked> Active</label></div>
                        <div class="col-md-1 form-group"><button class="btn btn-primary">Add</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
