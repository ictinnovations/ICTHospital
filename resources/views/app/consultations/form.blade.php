{{--
  ICTHospital - OPD consultation for one visit.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
<style>
  .consult .panel-heading { cursor: pointer; font-weight: bold; }
  .consult textarea { resize: vertical; }
  .consult .exam-row td { vertical-align: middle; }
  .consult-side { position: sticky; top: 70px; }
</style>
@stop
@section('content')
    @include('app.partials._alerts')
    @php($exam = (array) ($c->examination ?? []))
    @php($past = (array) ($c->past_history ?? []))
    @php($social = (array) ($c->social_history ?? []))
    @php($spec = (array) ($c->specialty ?? []))
    @php($gy = (array) ($spec['gynae'] ?? []))
    @php($den = (array) ($spec['dental'] ?? []))
    @php($eye = (array) ($spec['eye'] ?? []))
    @php($vac = (array) ($c->immunizations ?? []))
    <div class="row consult">
        <div class="col-md-9">
            <div class="box-inner">
                <div class="box-header well">
                    <h2><i class="glyphicon glyphicon-stethoscope glyphicon-heart"></i> OPD: {{ $patient->name }} <small>MR {{ $patient->patient_id }} &middot; {{ $patient->age !== '' ? $patient->age . ' y' : '' }} {{ $patient->sex }}</small>
                        @if ($patient->mlc)<span class="label label-danger">MLC</span>@endif
                        <span class="label {{ $c->status === 'completed' ? 'label-success' : 'label-default' }}">{{ ucfirst($c->status ?: 'new') }}</span></h2>
                    <div class="box-icon">
                        @if ($visit->token_no)<span class="label label-primary" style="font-size:13px">Token {{ $visit->token_no }}</span>@endif
                        @if ($c->exists)<a href="{{ url('/consultations/' . $c->id . '/print') }}" target="_blank" class="btn btn-default btn-sm">Print</a>@endif
                        <a href="{{ url('/opd/queue?date=' . $visit->date . '&doctor=' . $visit->doctor) }}" class="btn btn-default btn-sm">Queue</a>
                    </div>
                </div>
                <div class="box-content">
                <form method="post" action="{{ url('/consultations/visit/' . $visit->id) }}" id="consult-form">
                    {{ csrf_field() }}

                    @if ($templates->isNotEmpty())
                    <div class="form-inline" style="margin-bottom:10px">
                        <label>Templates:</label>
                        <select id="tpl" class="form-control input-sm"><option value="">Choose a template</option>@foreach ($templates as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select>
                        <button type="button" class="btn btn-default btn-sm" id="tpl-apply">Apply</button>
                    </div>
                    @endif

                    <div class="panel panel-default"><div class="panel-heading">Vitals</div><div class="panel-body">
                        <div class="row">
                            <div class="col-md-3 form-group"><label>BP (mm Hg)</label><input name="b_p" class="form-control" placeholder="120/80" value="{{ old('b_p', $visit->b_p) }}"></div>
                            <div class="col-md-3 form-group"><label>Pulse (/min)</label><input name="pulse" class="form-control" value="{{ old('pulse', $visit->pulse) }}"></div>
                            <div class="col-md-3 form-group"><label>Temperature (&deg;F)</label><input name="temprature" class="form-control" value="{{ old('temprature', $visit->temprature) }}"></div>
                            <div class="col-md-3 form-group"><label>Weight (kg)</label><input name="weight" class="form-control" value="{{ old('weight', $visit->weight) }}"></div>
                        </div>
                    </div></div>

                    <div class="panel panel-default"><div class="panel-heading">Presenting complaint and history</div><div class="panel-body">
                        <div class="form-group"><label>Presenting complaint</label><textarea name="presenting_complaint" class="form-control" rows="2">{{ old('presenting_complaint', $c->presenting_complaint) }}</textarea></div>
                        <div class="form-group"><label>History of presenting complaint</label><textarea name="history" class="form-control" rows="3">{{ old('history', $c->history) }}</textarea></div>
                        <div class="form-group"><label>Allergies</label><input name="allergies" class="form-control" placeholder="Drug and food allergies, with reactions" value="{{ old('allergies', $c->allergies) }}"></div>
                    </div></div>

                    <div class="panel panel-default"><div class="panel-heading">Examination</div><div class="panel-body">
                        <table class="table table-condensed">
                            @foreach (\App\Models\Consultation::SYSTEMS as $k => $label)
                                @php($st = $exam[$k]['status'] ?? 'not_examined')
                                <tr class="exam-row">
                                    <td style="width:28%">{{ $label }}</td>
                                    <td style="width:34%" class="text-nowrap">
                                        @foreach (['not_examined' => 'Not examined', 'normal' => 'Normal', 'abnormal' => 'Abnormal'] as $v => $l)
                                            <label class="radio-inline"><input type="radio" name="exam[{{ $k }}][status]" value="{{ $v }}" {{ $st === $v ? 'checked' : '' }}> {{ $l }}</label>
                                        @endforeach
                                    </td>
                                    <td><input name="exam[{{ $k }}][note]" class="form-control input-sm" placeholder="Findings" value="{{ $exam[$k]['note'] ?? '' }}"></td>
                                </tr>
                            @endforeach
                        </table>
                    </div></div>

                    <div class="panel panel-default"><div class="panel-heading">Diagnosis, plan and follow-up</div><div class="panel-body">
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Provisional diagnosis</label><textarea name="provisional_diagnosis" class="form-control" rows="2">{{ old('provisional_diagnosis', $c->provisional_diagnosis) }}</textarea></div>
                            <div class="col-md-6 form-group"><label>Final diagnosis</label><textarea name="diagnosis" class="form-control" rows="2">{{ old('diagnosis', $c->diagnosis) }}</textarea></div>
                        </div>
                        <div class="form-group"><label>Plan</label><textarea name="plan" class="form-control" rows="2">{{ old('plan', $c->plan) }}</textarea></div>
                        <div class="form-group"><label>Treatment and advice</label><textarea name="treatment" class="form-control" rows="2">{{ old('treatment', $c->treatment) }}</textarea></div>
                        <div class="row">
                            <div class="col-md-4 form-group"><label>Follow-up on</label><input type="date" name="follow_up_date" class="form-control" value="{{ old('follow_up_date', $c->follow_up_date) }}"></div>
                            <div class="col-md-8 form-group"><label>Follow-up instructions</label><input name="follow_up_notes" class="form-control" value="{{ old('follow_up_notes', $c->follow_up_notes) }}"></div>
                        </div>
                        @if ($c->follow_up_date)
                            <a class="btn btn-default btn-sm" href="{{ url('/appointments/create?patient=' . $patient->id . '&date=' . $c->follow_up_date) }}">Book follow-up appointment</a>
                        @endif
                    </div></div>

                    <div class="panel panel-default"><div class="panel-heading">Orders</div><div class="panel-body">
                        <label>Lab tests to request</label>
                        <select name="lab_tests[]" class="form-control" multiple size="6">
                            @foreach ($labCatalogue as $t)<option value="{{ $t->id }}">{{ $t->category }}</option>@endforeach
                        </select>
                        <span class="help-block">Hold Ctrl to pick several. A lab request is raised when you save.</span>
                        @if ($orders->isNotEmpty())
                            <p><strong>Requested today:</strong>
                                @foreach ($orders as $o)<a href="{{ url('/lab/' . $o->id) }}">#{{ $o->id }} {{ $o->tests->pluck('name')->implode(', ') }}</a> ({{ $o->status }})@if (! $loop->last); @endif @endforeach</p>
                        @endif
                        @includeIf('app.consultations._radiology_orders')
                        <a class="btn btn-default btn-sm" href="{{ url('/prescriptions/create?patient=' . $patient->id) }}" target="_blank"><i class="glyphicon glyphicon-list-alt"></i> Write prescription</a>
                    </div></div>

                    <div class="panel panel-default"><div class="panel-heading" data-toggle="collapse" data-target="#sec-history">Past and social history <small class="text-muted">(carried from the last visit)</small></div><div class="panel-body collapse {{ $past || $social ? 'in' : '' }}" id="sec-history">
                        <div class="row">
                            @foreach (\App\Models\Consultation::PAST as $k => $label)
                                <div class="col-md-3 form-group"><label>{{ $label }}</label><input name="past[{{ $k }}]" class="form-control input-sm" placeholder="Since, control, notes" value="{{ $past[$k] ?? '' }}"></div>
                            @endforeach
                        </div>
                        <div class="row">
                            <div class="col-md-3 form-group"><label>Smoker</label><input name="social[smoker]" class="form-control input-sm" value="{{ $social['smoker'] ?? '' }}"></div>
                            <div class="col-md-3 form-group"><label>Alcohol / drugs</label><input name="social[substances]" class="form-control input-sm" value="{{ $social['substances'] ?? '' }}"></div>
                            <div class="col-md-3 form-group"><label>Marital status</label><input name="social[marital]" class="form-control input-sm" value="{{ $social['marital'] ?? '' }}"></div>
                            <div class="col-md-3 form-group"><label>Occupation</label><input name="social[occupation]" class="form-control input-sm" value="{{ $social['occupation'] ?? '' }}"></div>
                        </div>
                    </div></div>

                    <div class="panel panel-default"><div class="panel-heading" data-toggle="collapse" data-target="#sec-gynae">Gynae and obstetric</div><div class="panel-body collapse {{ $gy ? 'in' : '' }}" id="sec-gynae">
                        <div class="row">
                            <div class="col-md-3 form-group"><label>LMP</label><input type="date" name="gynae[lmp]" class="form-control input-sm" value="{{ $gy['lmp'] ?? '' }}"></div>
                            <div class="col-md-3 form-group"><label>EDD</label><input class="form-control input-sm" value="{{ $gy['edd'] ?? '' }}" readonly placeholder="Worked out from LMP"></div>
                            <div class="col-md-3 form-group"><label>Gestational age</label><input class="form-control input-sm" value="{{ $gy['gestational_age'] ?? '' }}" readonly></div>
                            <div class="col-md-3 form-group"><label>Menstrual cycle</label><input name="gynae[cycle]" class="form-control input-sm" placeholder="e.g. 5/28 regular" value="{{ $gy['cycle'] ?? '' }}"></div>
                        </div>
                        <div class="row">
                            @foreach (['g' => 'Gravida', 'p' => 'Para', 'a' => 'Abortions', 'lcb' => 'Last childbirth', 'mode' => 'Mode of delivery', 'contraception' => 'Contraception'] as $k => $label)
                                <div class="col-md-2 form-group"><label>{{ $label }}</label><input name="gynae[{{ $k }}]" class="form-control input-sm" value="{{ $gy[$k] ?? '' }}"></div>
                            @endforeach
                        </div>
                    </div></div>

                    <div class="panel panel-default"><div class="panel-heading" data-toggle="collapse" data-target="#sec-dental">Dental</div><div class="panel-body collapse {{ $den ? 'in' : '' }}" id="sec-dental">
                        <div class="form-group"><label>Past dental history</label><textarea name="dental[history]" class="form-control" rows="2">{{ $den['history'] ?? '' }}</textarea></div>
                        <div class="form-group"><label>Teeth involved and findings</label><textarea name="dental[findings]" class="form-control" rows="2" placeholder="FDI tooth numbers, e.g. 36 caries, 47 missing">{{ $den['findings'] ?? '' }}</textarea></div>
                    </div></div>

                    <div class="panel panel-default"><div class="panel-heading" data-toggle="collapse" data-target="#sec-eye">Eye examination and glasses</div><div class="panel-body collapse {{ $eye ? 'in' : '' }}" id="sec-eye">
                        <div class="row">
                            <div class="col-md-3 form-group"><label>VA right (OD)</label><input name="eye[va_r]" class="form-control input-sm" placeholder="6/6" value="{{ $eye['va_r'] ?? '' }}"></div>
                            <div class="col-md-3 form-group"><label>VA left (OS)</label><input name="eye[va_l]" class="form-control input-sm" value="{{ $eye['va_l'] ?? '' }}"></div>
                            <div class="col-md-3 form-group"><label>IOP right (mm Hg)</label><input name="eye[iop_r]" class="form-control input-sm" value="{{ $eye['iop_r'] ?? '' }}"></div>
                            <div class="col-md-3 form-group"><label>IOP left</label><input name="eye[iop_l]" class="form-control input-sm" value="{{ $eye['iop_l'] ?? '' }}"></div>
                        </div>
                        <table class="table table-condensed table-bordered" style="max-width:640px">
                            <thead><tr><th>Glasses</th><th>OD SPH</th><th>OD CYL</th><th>OD AXIS</th><th>OS SPH</th><th>OS CYL</th><th>OS AXIS</th></tr></thead>
                            <tbody>
                            @foreach (['dist' => 'Distance', 'near' => 'Near'] as $row => $label)
                                <tr><td>{{ $label }}</td>
                                    @foreach (['r_sph', 'r_cyl', 'r_axis', 'l_sph', 'l_cyl', 'l_axis'] as $f)<td><input name="eye[{{ $row }}_{{ $f }}]" class="form-control input-sm" value="{{ $eye[$row . '_' . $f] ?? '' }}"></td>@endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div></div>

                    <div class="panel panel-default"><div class="panel-heading" data-toggle="collapse" data-target="#sec-vac">Immunizations</div><div class="panel-body collapse {{ $vac ? 'in' : '' }}" id="sec-vac">
                        <div class="row">
                            @foreach (\App\Models\Consultation::VACCINES as $v)
                                <div class="col-md-3 form-group"><label>{{ $v }}</label><input type="date" name="vaccines[{{ $v }}]" class="form-control input-sm" value="{{ $vac[$v] ?? '' }}"></div>
                            @endforeach
                        </div>
                    </div></div>

                    <div class="form-inline" style="margin:10px 0">
                        <input name="template_name" class="form-control input-sm" placeholder="Save the text as a template (name)">
                    </div>
                    <button name="action" value="save" class="btn btn-primary">Save</button>
                    <button name="action" value="complete" class="btn btn-success" onclick="return confirm('Complete the consultation and check the patient out?')">Save and discharge</button>
                </form>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="consult-side">
                <div class="panel panel-default">
                    <div class="panel-heading">Patient</div>
                    <div class="panel-body small">
                        <strong>{{ $patient->name }}</strong><br>
                        MR {{ $patient->patient_id }}@if ($patient->cnic) &middot; CNIC {{ $patient->cnic }}@endif<br>
                        {{ $patient->phone }}<br>
                        Blood group: {{ $patient->bloodgroup ?: '-' }}<br>
                        Doctor: {{ optional($doctor)->name }}<br>
                        <a href="{{ url('/patients/' . $patient->id) }}">Full chart</a>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">Previous visits</div>
                    <div class="panel-body small">
                        @forelse ($previous as $p)
                            <p><a href="{{ url('/consultations/' . $p->id . '/print') }}" target="_blank">{{ $p->date }}</a>: {{ \Illuminate\Support\Str::limit($p->diagnosis ?: $p->provisional_diagnosis ?: $p->presenting_complaint, 70) }}</p>
                        @empty
                            <p class="text-muted">First consultation.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
<script>
(function () {
    var templates = @json($templates->mapWithKeys(fn ($t) => [$t->id => $t->fields]));
    var btn = document.getElementById('tpl-apply');
    if (!btn) { return; }
    btn.addEventListener('click', function () {
        var t = templates[document.getElementById('tpl').value];
        if (!t) { return; }
        Object.keys(t).forEach(function (k) {
            var el = document.querySelector('#consult-form [name="' + k + '"]');
            if (el && t[k] && !el.value) { el.value = t[k]; }
        });
    });
})();
</script>
@stop
