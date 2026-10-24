{{--
  ICTHospital - doctor share policies, their rules and the doctors on each.

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
                    <h2><i class="glyphicon glyphicon-random"></i> Doctor share policies</h2>
                    <div class="box-icon"><a href="{{ url('/doctor-share') }}" class="btn btn-default btn-sm">Share report</a></div>
                </div>
                <div class="box-content">
                    <p class="help-block">
                        A policy is a set of rules. Each doctor is on one policy. Every invoice line is matched to the most specific rule
                        of the doctor's policy: a rule for the exact service beats one for its department, which beats a catch-all, and a rule
                        for the patient type beats one without. The share is worked out on the line total less its part of any discount.
                    </p>

                    @forelse ($policies as $policy)
                        <div class="panel panel-default" id="policy-{{ $policy->id }}">
                            <div class="panel-heading">
                                <form method="post" action="{{ url('/doctor-share/policies/' . $policy->id) }}" class="form-inline">
                                    {{ csrf_field() }}<input type="hidden" name="_method" value="PUT">
                                    <input name="name" class="form-control input-sm" value="{{ $policy->name }}" required style="font-weight:bold">
                                    <input name="notes" class="form-control input-sm" value="{{ $policy->notes }}" placeholder="Notes" style="width:320px">
                                    <button class="btn btn-default btn-sm">Save</button>
                                </form>
                            </div>
                            <div class="panel-body">
                                @foreach ($policy->rules as $rule)
                                    <form id="rule-{{ $rule->id }}" method="post" action="{{ url('/doctor-share/rules/' . $rule->id) }}">{{ csrf_field() }}<input type="hidden" name="_method" value="PUT"></form>
                                    <form id="rule-del-{{ $rule->id }}" method="post" action="{{ url('/doctor-share/rules/' . $rule->id) }}">{{ csrf_field() }}<input type="hidden" name="_method" value="DELETE"></form>
                                @endforeach
                                <form method="post" action="{{ url('/doctor-share/policies/' . $policy->id . '/rules') }}" id="rule-new-{{ $policy->id }}">{{ csrf_field() }}</form>
                                <div class="table-responsive">
                                <table class="table table-condensed table-bordered">
                                    <thead><tr><th>Service</th><th>Department</th><th>Patient type</th><th>Doctor %</th><th>Referral %</th><th></th></tr></thead>
                                    <tbody>
                                    @foreach ($policy->rules as $rule)
                                        <tr>
                                            <td><select form="rule-{{ $rule->id }}" name="payment_category_id" class="form-control input-sm"><option value="">Any service</option>@foreach ($services as $s)<option value="{{ $s->id }}" {{ (int) $rule->payment_category_id === $s->id ? 'selected' : '' }}>{{ $s->category }}</option>@endforeach</select></td>
                                            <td><select form="rule-{{ $rule->id }}" name="department" class="form-control input-sm"><option value="">Any department</option>@foreach ($departments as $d)<option value="{{ $d }}" {{ $rule->department === $d ? 'selected' : '' }}>{{ $d }}</option>@endforeach</select></td>
                                            <td><select form="rule-{{ $rule->id }}" name="patient_type_id" class="form-control input-sm"><option value="">Any type</option>@foreach ($types as $t)<option value="{{ $t->id }}" {{ (int) $rule->patient_type_id === $t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach</select></td>
                                            <td><input form="rule-{{ $rule->id }}" type="number" step="0.01" min="0" max="100" name="doctor_percent" class="form-control input-sm" value="{{ $rule->doctor_percent }}" required></td>
                                            <td><input form="rule-{{ $rule->id }}" type="number" step="0.01" min="0" max="100" name="referral_percent" class="form-control input-sm" value="{{ $rule->referral_percent }}"></td>
                                            <td class="text-nowrap"><button form="rule-{{ $rule->id }}" class="btn btn-primary btn-xs">Save</button> <button form="rule-del-{{ $rule->id }}" class="btn btn-danger btn-xs">Remove</button></td>
                                        </tr>
                                    @endforeach
                                    <tr class="active">
                                        <td><select form="rule-new-{{ $policy->id }}" name="payment_category_id" class="form-control input-sm"><option value="">Any service</option>@foreach ($services as $s)<option value="{{ $s->id }}">{{ $s->category }}</option>@endforeach</select></td>
                                        <td><select form="rule-new-{{ $policy->id }}" name="department" class="form-control input-sm"><option value="">Any department</option>@foreach ($departments as $d)<option value="{{ $d }}">{{ $d }}</option>@endforeach</select></td>
                                        <td><select form="rule-new-{{ $policy->id }}" name="patient_type_id" class="form-control input-sm"><option value="">Any type</option>@foreach ($types as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></td>
                                        <td><input form="rule-new-{{ $policy->id }}" type="number" step="0.01" min="0" max="100" name="doctor_percent" class="form-control input-sm" placeholder="e.g. 70" required></td>
                                        <td><input form="rule-new-{{ $policy->id }}" type="number" step="0.01" min="0" max="100" name="referral_percent" class="form-control input-sm" placeholder="0"></td>
                                        <td><button form="rule-new-{{ $policy->id }}" class="btn btn-default btn-xs">Add rule</button></td>
                                    </tr>
                                    </tbody>
                                </table>
                                </div>

                                <form method="post" action="{{ url('/doctor-share/policies/' . $policy->id . '/assign') }}">
                                    {{ csrf_field() }}
                                    <label>Doctors on this policy</label>
                                    <div class="row">
                                        @foreach ($doctors as $d)
                                            <div class="col-md-3 checkbox" style="margin-top:0">
                                                <label><input type="checkbox" name="doctors[]" value="{{ $d->id }}" {{ (int) $d->share_policy_id === $policy->id ? 'checked' : '' }}>
                                                    {{ $d->name }}@if ($d->share_policy_id && (int) $d->share_policy_id !== $policy->id) <small class="text-muted">(on another policy)</small>@endif</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-primary btn-sm">Assign to doctors</button>
                                    @if ($doctors->isEmpty())<span class="text-muted">No doctors yet. Add them under Doctors.</span>@endif
                                </form>
                                <form method="post" action="{{ url('/doctor-share/policies/' . $policy->id) }}" style="margin-top:8px" onsubmit="return confirm('Delete this policy?')">
                                    {{ csrf_field() }}<input type="hidden" name="_method" value="DELETE">
                                    <button class="btn btn-link btn-sm text-danger" style="padding-left:0">Delete policy</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">No policies yet. Add one below, then assign doctors to it.</p>
                    @endforelse

                    <h4>Add a policy</h4>
                    <form method="post" action="{{ url('/doctor-share/policies') }}" class="form-inline">
                        {{ csrf_field() }}
                        <input name="name" class="form-control" placeholder="e.g. OPD 70%, panel 100%" required>
                        <input type="number" step="0.01" min="0" max="100" name="doctor_percent" class="form-control" placeholder="Default doctor %" style="width:170px">
                        <input name="notes" class="form-control" placeholder="Notes (optional)">
                        <button class="btn btn-primary">Add policy</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
