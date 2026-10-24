{{--
  ICTHospital - radiology exam catalogue.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('content')
    @include('app.partials._alerts')
    @php($canEdit = \App\Support\Permissions::allows('diagnostic_report_add'))
    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well"><h2><i class="glyphicon glyphicon-film"></i> Radiology exam catalogue</h2>
                    <div class="box-icon"><a href="{{ url('/radiology') }}" class="btn btn-default btn-sm">Worklist</a></div></div>
                <div class="box-content">
                    <p class="help-block">Link each exam to a service on the price list so it can be billed, priced per patient type and shared with the doctor. The report template pre-fills the findings box.</p>
                    @foreach ($exams as $e)
                        <form id="exam-{{ $e->id }}" method="post" action="{{ url('/radiology/catalogue/' . $e->id) }}">{{ csrf_field() }}<input type="hidden" name="_method" value="PUT"></form>
                    @endforeach
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed">
                        <thead><tr><th>Exam</th><th>Modality</th><th>Billed as</th><th>Report template</th><th>Active</th><th></th></tr></thead>
                        <tbody>
                        @forelse ($exams as $e)
                            <tr>
                                <td><input form="exam-{{ $e->id }}" name="name" class="form-control input-sm" value="{{ $e->name }}" required></td>
                                <td><select form="exam-{{ $e->id }}" name="modality" class="form-control input-sm">@foreach (\App\Models\RadiologyExam::MODALITIES as $k => $l)<option value="{{ $k }}" {{ $e->modality === $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select></td>
                                <td><select form="exam-{{ $e->id }}" name="payment_category_id" class="form-control input-sm"><option value="">Not billed</option>@foreach ($services as $s)<option value="{{ $s->id }}" {{ (int) $e->payment_category_id === $s->id ? 'selected' : '' }}>{{ $s->category }}</option>@endforeach</select></td>
                                <td><textarea form="exam-{{ $e->id }}" name="report_template" class="form-control input-sm" rows="1">{{ $e->report_template }}</textarea></td>
                                <td><input form="exam-{{ $e->id }}" type="checkbox" name="active" value="1" {{ $e->active ? 'checked' : '' }}></td>
                                <td>@if ($canEdit)<button form="exam-{{ $e->id }}" class="btn btn-primary btn-xs">Save</button>@endif</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-muted">No exams yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    </div>
                    @if ($canEdit)
                        <h4>Add an exam</h4>
                        <form method="post" action="{{ url('/radiology/catalogue') }}" class="row">
                            {{ csrf_field() }}
                            <div class="col-md-3 form-group"><input name="name" class="form-control" placeholder="e.g. Ultrasound abdomen" required></div>
                            <div class="col-md-2 form-group"><select name="modality" class="form-control">@foreach (\App\Models\RadiologyExam::MODALITIES as $k => $l)<option value="{{ $k }}">{{ $l }}</option>@endforeach</select></div>
                            <div class="col-md-3 form-group"><select name="payment_category_id" class="form-control"><option value="">Billed as (service)</option>@foreach ($services as $s)<option value="{{ $s->id }}">{{ $s->category }}</option>@endforeach</select></div>
                            <div class="col-md-3 form-group"><input name="report_template" class="form-control" placeholder="Report template (optional)"></div>
                            <div class="col-md-1 form-group"><button class="btn btn-primary">Add</button></div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
