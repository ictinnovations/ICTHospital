{{--
  ICTHospital - price list per patient type, outpatient and inpatient.

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
                    <h2><i class="glyphicon glyphicon-usd"></i> Pricing{{ $type ? ': ' . $type->name : '' }}</h2>
                    <div class="box-icon">
                        <a href="{{ url('/patient-types') }}" class="btn btn-default btn-sm">Patient types</a>
                        <a href="{{ url('/pricing/export?type=' . $typeId) }}" class="btn btn-default btn-sm">Download price list</a>
                    </div>
                </div>
                <div class="box-content">
                    <form method="get" action="{{ url('/pricing') }}" class="row">
                        <div class="col-md-3 form-group">
                            <label>Patient type</label>
                            <select name="type" class="form-control" onchange="this.form.submit()">
                                @foreach ($types as $t)<option value="{{ $t->id }}" {{ $t->id === $typeId ? 'selected' : '' }}>{{ $t->name }}{{ $t->active ? '' : ' (off)' }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Department</label>
                            <select name="department" class="form-control">
                                <option value="">All</option>
                                @foreach ($departments as $d)<option value="{{ $d }}" {{ $department === $d ? 'selected' : '' }}>{{ $d }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Service name</label>
                            <input name="q" class="form-control" value="{{ $term }}">
                        </div>
                        <div class="col-md-2 form-group"><label>&nbsp;</label><button class="btn btn-default btn-block">Search</button></div>
                    </form>

                    <form method="post" action="{{ url('/pricing') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="type" value="{{ $typeId }}">
                        <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead><tr><th>Service</th><th>Department</th><th>List price</th><th>OPD price</th><th>IPD price</th></tr></thead>
                            <tbody>
                            @forelse ($services as $s)
                                @php($p = $prices->get($s->id))
                                <tr>
                                    <td>{{ $s->category }}</td>
                                    <td>{{ $s->department }}</td>
                                    <td>{{ number_format((float) $s->c_price, 2) }}</td>
                                    <td><input type="number" step="0.01" min="0" name="opd[{{ $s->id }}]" class="form-control input-sm" placeholder="{{ number_format((float) $s->c_price, 2, '.', '') }}" value="{{ $p && $p->opd_price !== null ? $p->opd_price : '' }}"></td>
                                    <td><input type="number" step="0.01" min="0" name="ipd[{{ $s->id }}]" class="form-control input-sm" placeholder="{{ number_format((float) $s->c_price, 2, '.', '') }}" value="{{ $p && $p->ipd_price !== null ? $p->ipd_price : '' }}"></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-muted">No services match. Add services under <a href="{{ url('/services') }}">Services and Prices</a>.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                        </div>
                        <p class="help-block">A blank box means this type pays the list price for that service.</p>
                        <button class="btn btn-primary">Save price list</button>
                    </form>

                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Put prices up or down</h4>
                            <form method="post" action="{{ url('/pricing/adjust') }}" class="form-inline">
                                {{ csrf_field() }}
                                <input type="hidden" name="type" value="{{ $typeId }}">
                                <input type="number" step="0.01" name="percent" class="form-control" placeholder="%" style="width:90px" required>
                                <select name="setting" class="form-control"><option value="both">OPD and IPD</option><option value="opd">OPD only</option><option value="ipd">IPD only</option></select>
                                <button class="btn btn-default" onclick="return confirm('Change every price for this type?')">Update pricing list</button>
                            </form>
                            <p class="help-block">Use a minus number to lower prices. Services without a price for this type start from the list price.</p>
                        </div>
                        <div class="col-md-6">
                            <h4>Copy pricing list</h4>
                            <form method="post" action="{{ url('/pricing/copy') }}" class="form-inline">
                                {{ csrf_field() }}
                                <input type="hidden" name="to" value="{{ $typeId }}">
                                From <select name="from" class="form-control">@foreach ($types as $t)@if ($t->id !== $typeId)<option value="{{ $t->id }}">{{ $t->name }}</option>@endif @endforeach</select>
                                <button class="btn btn-default" onclick="return confirm('Replace this list with a copy?')">Copy into {{ optional($type)->name }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
