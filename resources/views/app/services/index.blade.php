{{--
  ICTHospital - billable services and price list.

  Editing is inline: each row is its own form, so a price can be corrected
  without a round trip to a separate screen. A price list gets adjusted far more
  often than it gets added to.

  A form element cannot wrap a table row, so the forms are declared above the
  table and each control points at one with the HTML form attribute. Nesting them
  inside the cells instead produces markup browsers silently reparent, which is
  how inline table editing usually ends up posting the wrong row.

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
                    <h2><i class="glyphicon glyphicon-tags"></i> Services and prices</h2>
                    <div class="box-icon">
                        <a href="{{ url('/invoices') }}" class="btn btn-default btn-sm">Back to invoices</a>
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

                    @foreach ($services as $service)
                        <form id="svc-save-{{ $service->id }}" action="{{ url('/services/' . $service->id) }}" method="post">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="PUT">
                        </form>
                        <form id="svc-del-{{ $service->id }}" action="{{ url('/services/' . $service->id) }}" method="post"
                              onsubmit="return confirm('Remove {{ $service->category }} from the price list?');">
                            {{ csrf_field() }}
                            <input type="hidden" name="_method" value="DELETE">
                        </form>
                    @endforeach

                    @if ($services->isEmpty())
                        <p class="text-muted">
                            Nothing on the price list yet. Invoice lines are raised against this list, so add
                            the consultations, procedures and bed charges you bill for before raising an invoice.
                        </p>
                    @else
                        <p class="text-muted">
                            A price is copied onto an invoice line when the charge is raised, so changing one
                            here never rewrites what a patient was already billed.
                        </p>
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:19%">Service</th>
                                    <th style="width:21%">Description</th>
                                    <th style="width:13%">Group</th>
                                    <th style="width:12%">Price</th>
                                    <th style="width:9%">Doctor %</th>
                                    <th style="width:9%">Hospital %</th>
                                    <th style="width:7%">Invoiced</th>
                                    <th style="width:10%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($services as $service)
                                    <tr>
                                        <td>
                                            <input type="text" name="category" class="form-control input-sm" required
                                                   form="svc-save-{{ $service->id }}" value="{{ $service->category }}">
                                        </td>
                                        <td>
                                            <input type="text" name="description" class="form-control input-sm"
                                                   form="svc-save-{{ $service->id }}" value="{{ $service->description }}">
                                        </td>
                                        <td>
                                            <input type="text" name="type" class="form-control input-sm" list="typelist"
                                                   form="svc-save-{{ $service->id }}" value="{{ $service->type }}">
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" min="0" name="c_price" class="form-control input-sm"
                                                   form="svc-save-{{ $service->id }}" value="{{ $service->c_price }}">
                                        </td>
                                        <td>
                                            <input type="number" min="0" max="100" name="d_commission" class="form-control input-sm"
                                                   form="svc-save-{{ $service->id }}" value="{{ $service->d_commission }}">
                                        </td>
                                        <td>
                                            <input type="number" min="0" max="100" name="h_commission" class="form-control input-sm"
                                                   form="svc-save-{{ $service->id }}" value="{{ $service->h_commission }}">
                                        </td>
                                        <td>{{ $usage[$service->id] ?? 0 }}</td>
                                        <td>
                                            <button type="submit" class="btn btn-primary btn-sm"
                                                    form="svc-save-{{ $service->id }}">Save</button>
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                    form="svc-del-{{ $service->id }}">
                                                <i class="glyphicon glyphicon-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <datalist id="typelist">
                        @foreach ($types as $type)
                            <option value="{{ $type }}"></option>
                        @endforeach
                        <option value="Consultation"></option>
                        <option value="Procedure"></option>
                        <option value="Ward"></option>
                        <option value="Diagnostics"></option>
                    </datalist>

                    <h4>Add a service</h4>
                    <form role="form" action="{{ url('/services') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <input type="text" name="category" class="form-control"
                                       placeholder="Consultation, general" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <input type="text" name="description" class="form-control" placeholder="Description">
                            </div>
                            <div class="col-md-2 form-group">
                                <input type="text" name="type" class="form-control" list="typelist" placeholder="Group">
                            </div>
                            <div class="col-md-2 form-group">
                                <input type="number" step="0.01" min="0" name="c_price" class="form-control"
                                       placeholder="Price">
                            </div>
                            <div class="col-md-2 form-group">
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </div>
                        <p class="help-block">
                            The doctor and hospital percentages are recorded against a service for reference.
                            Nothing in this release applies them to an invoice.
                        </p>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
