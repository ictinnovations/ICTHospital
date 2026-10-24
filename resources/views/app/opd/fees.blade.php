{{--
  ICTHospital - OPD visit fees per doctor, visit type and shift.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('content')
    @include('app.partials._alerts')
    @php($canEdit = \App\Support\Permissions::allows('service_update'))
    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well"><h2><i class="glyphicon glyphicon-usd"></i> Doctor visit fee</h2></div>
                <div class="box-content">
                    <p class="help-block">The fee billed when a visit is checked in. A doctor's own fee beats an all-doctors fee, and a fee for the shift beats an any-time fee. Leave the fee blank to charge the service's price for the patient's type.</p>
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed">
                        <thead><tr><th>Doctor</th><th>Visit type</th><th>Shift</th><th>Service on invoice</th><th class="text-right">Visit fee</th><th></th></tr></thead>
                        <tbody>
                        @forelse ($fees as $f)
                            <tr>
                                <td>{{ $f->doctor_id ? ($doctorNames[$f->doctor_id] ?? $f->doctor_id) : 'All doctors' }}</td>
                                <td>{{ \App\Models\VisitFee::TYPES[$f->visit_type] ?? $f->visit_type }}</td>
                                <td>{{ \App\Models\VisitFee::SHIFTS[$f->shift] ?? $f->shift }}</td>
                                <td>{{ $serviceNames[$f->payment_category_id] ?? '' }}</td>
                                <td class="text-right">{{ $f->fee !== null ? number_format((float) $f->fee, 2) : 'Price list' }}</td>
                                <td>@if ($canEdit)<form method="post" action="{{ url('/opd/visit-fees/' . $f->id) }}">{{ csrf_field() }}<input type="hidden" name="_method" value="DELETE"><button class="btn btn-danger btn-xs">Remove</button></form>@endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-muted">No visit fees yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    </div>
                    @if ($canEdit)
                        <h4>Add visit configuration</h4>
                        <form method="post" action="{{ url('/opd/visit-fees') }}" class="row">
                            {{ csrf_field() }}
                            <div class="col-md-3 form-group"><select name="doctor_id" class="form-control"><option value="">All doctors</option>@foreach ($doctors as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div>
                            <div class="col-md-2 form-group"><select name="visit_type" class="form-control">@foreach (\App\Models\VisitFee::TYPES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                            <div class="col-md-2 form-group"><select name="shift" class="form-control">@foreach (\App\Models\VisitFee::SHIFTS as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                            <div class="col-md-2 form-group"><select name="payment_category_id" class="form-control" required><option value="">Service</option>@foreach ($services as $s)<option value="{{ $s->id }}">{{ $s->category }}</option>@endforeach</select></div>
                            <div class="col-md-2 form-group"><input type="number" step="0.01" min="0" name="fee" class="form-control" placeholder="Fee (blank = price list)"></div>
                            <div class="col-md-1 form-group"><button class="btn btn-primary">Save</button></div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
