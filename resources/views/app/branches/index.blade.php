{{--
  ICTHospital - hospital branches and who works at each.

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
                <div class="box-header well"><h2><i class="glyphicon glyphicon-map-marker"></i> Branches</h2></div>
                <div class="box-content">
                    <p class="help-block">Invoices, OPD visits and shift closes are stamped with the branch they happen at. A user tied to a branch sees that branch only; a user with no branch picks one from the top bar, or sees all. Records from before branches were set up show in every branch.</p>
                    @forelse ($branches as $b)
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <form method="post" action="{{ url('/hospital-branches/' . $b->id) }}" class="form-inline">
                                    {{ csrf_field() }}<input type="hidden" name="_method" value="PUT">
                                    <input name="name" class="form-control input-sm" value="{{ $b->name }}" required style="font-weight:bold">
                                    <input name="address" class="form-control input-sm" value="{{ $b->address }}" placeholder="Address" style="width:280px">
                                    <input name="phone" class="form-control input-sm" value="{{ $b->phone }}" placeholder="Phone">
                                    <label><input type="checkbox" name="active" value="1" {{ $b->active ? 'checked' : '' }}> Active</label>
                                    <button class="btn btn-default btn-sm">Save</button>
                                </form>
                            </div>
                            <div class="panel-body">
                                <form method="post" action="{{ url('/hospital-branches/' . $b->id . '/users') }}">
                                    {{ csrf_field() }}
                                    <label>Users who work at {{ $b->name }}</label>
                                    <div class="row">
                                        @foreach ($users as $u)
                                            <div class="col-md-3 checkbox" style="margin-top:0"><label><input type="checkbox" name="users[]" value="{{ $u->id }}" {{ (int) $u->branch_id === $b->id ? 'checked' : '' }}>
                                                {{ trim($u->firstname . ' ' . $u->lastname) ?: $u->login }} <small class="text-muted">{{ $u->group }}</small></label></div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm">Save users</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No branches. With none set up, the hospital runs as a single site.</p>
                    @endforelse
                    <h4>Add a branch</h4>
                    <form method="post" action="{{ url('/hospital-branches') }}" class="form-inline">
                        {{ csrf_field() }}
                        <input name="name" class="form-control" placeholder="e.g. Gulberg branch" required>
                        <input name="address" class="form-control" placeholder="Address">
                        <input name="phone" class="form-control" placeholder="Phone">
                        <button class="btn btn-primary">Add branch</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
