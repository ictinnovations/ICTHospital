{{--
  ICTHospital - order radiology exams.

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
                <div class="box-header well"><h2><i class="glyphicon glyphicon-film"></i> Order radiology exam</h2></div>
                <div class="box-content">
                    @if ($exams->isEmpty())
                        <p class="text-muted">The exam catalogue is empty. <a href="{{ url('/radiology/catalogue') }}">Add exams first.</a></p>
                    @else
                    <form method="post" action="{{ url('/radiology') }}">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6 form-group"><label>Patient</label><select name="patient" class="form-control" required><option value="">Choose a patient</option>@foreach ($patients as $p)<option value="{{ $p->id }}" {{ (string) old('patient', $patientId) === (string) $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->patient_id }})</option>@endforeach</select></div>
                            <div class="col-md-6 form-group"><label>Referring doctor</label><select name="doctor" class="form-control"><option value="">Not recorded</option>@foreach ($doctors as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select></div>
                        </div>
                        <label>Exams</label>
                        <div class="row">
                            @foreach ($exams->groupBy('modality') as $mod => $group)
                                <div class="col-md-3"><strong>{{ \App\Models\RadiologyExam::MODALITIES[$mod] ?? $mod }}</strong>
                                    @foreach ($group as $e)<div class="checkbox" style="margin:2px 0"><label><input type="checkbox" name="exams[]" value="{{ $e->id }}"> {{ $e->name }}</label></div>@endforeach
                                </div>
                            @endforeach
                        </div>
                        <div class="form-group" style="margin-top:10px"><label>Clinical notes</label><input name="clinical_notes" class="form-control" maxlength="1000" placeholder="Why, and what to look for"></div>
                        <label class="checkbox-inline"><input type="checkbox" name="urgent" value="1"> Urgent</label>
                        @if (\App\Support\Permissions::allows('payment_add'))<label class="checkbox-inline"><input type="checkbox" name="bill" value="1" checked> Raise the invoice now</label>@endif
                        <div style="margin-top:12px"><button class="btn btn-primary">Order</button> <a href="{{ url('/radiology') }}" class="btn btn-default">Cancel</a></div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
