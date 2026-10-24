{{--
  ICTHospital - radiology orders on the consultation screen.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@php($radExams = \App\Models\RadiologyExam::where('active', true)->orderBy('modality')->orderBy('name')->get())
@if ($radExams->isNotEmpty())
    <label style="margin-top:10px">Radiology to order</label>
    <select name="radiology_exams[]" class="form-control" multiple size="5">
        @foreach ($radExams as $e)<option value="{{ $e->id }}">{{ \App\Models\RadiologyExam::MODALITIES[$e->modality] ?? $e->modality }}: {{ $e->name }}</option>@endforeach
    </select>
    <label class="checkbox-inline"><input type="checkbox" name="radiology_urgent" value="1"> Urgent</label>
    @php($radToday = \App\Models\RadiologyOrder::where('appointment_id', $visit->id)->get())
    @if ($radToday->isNotEmpty())
        <p style="margin-top:6px"><strong>Radiology ordered:</strong> @foreach ($radToday as $r)<a href="{{ url('/radiology/' . $r->id) }}">{{ $r->name }}</a> ({{ $r->status }})@if (! $loop->last); @endif @endforeach</p>
    @endif
    <div style="margin-bottom:10px"></div>
@endif
