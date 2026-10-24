{{--
  ICTHospital - printable consultation summary for the patient.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Consultation {{ optional($patient)->patient_id }} {{ $c->date }}</title>
<style>
  body { font-family: Arial, sans-serif; max-width: 190mm; margin: 12px auto; color: #111; font-size: 13px; }
  h1 { font-size: 18px; margin: 0; } h2 { font-size: 14px; margin: 14px 0 4px; border-bottom: 1px solid #ccc; }
  .meta td { padding: 2px 10px 2px 0; } p { margin: 3px 0; white-space: pre-line; }
  @media print { button { display: none; } }
</style>
</head>
<body>
@php($institute = \Illuminate\Support\Facades\Schema::hasTable('institute') && \Illuminate\Support\Facades\Schema::hasColumn('institute', 'name') ? \Illuminate\Support\Facades\DB::table('institute')->value('name') : null)
<h1>{{ $institute ?: 'Outpatient consultation' }}</h1>
<table class="meta">
  <tr><td><strong>Patient</strong></td><td>{{ optional($patient)->name }} (MR {{ optional($patient)->patient_id }})</td><td><strong>Date</strong></td><td>{{ $c->date }}</td></tr>
  <tr><td><strong>Doctor</strong></td><td>{{ optional($doctor)->name }}</td><td><strong>Age / sex</strong></td><td>{{ optional($patient)->age }} {{ optional($patient)->sex }}</td></tr>
  @if ($visit)<tr><td><strong>Vitals</strong></td><td colspan="3">BP {{ $visit->b_p ?: '-' }}, pulse {{ $visit->pulse ?: '-' }}, temp {{ $visit->temprature ?: '-' }}, weight {{ $visit->weight ?: '-' }}</td></tr>@endif
</table>
@foreach (['presenting_complaint' => 'Presenting complaint', 'history' => 'History', 'allergies' => 'Allergies'] as $k => $label)
  @if ($c->$k)<h2>{{ $label }}</h2><p>{{ $c->$k }}</p>@endif
@endforeach
@php($exam = collect((array) $c->examination)->filter(fn ($e) => ($e['status'] ?? '') !== 'not_examined' || ! empty($e['note'])))
@if ($exam->isNotEmpty())
  <h2>Examination</h2>
  @foreach ($exam as $k => $e)<p><strong>{{ \App\Models\Consultation::SYSTEMS[$k] ?? $k }}:</strong> {{ ucfirst(str_replace('_', ' ', $e['status'] ?? '')) }}{{ ! empty($e['note']) ? ', ' . $e['note'] : '' }}</p>@endforeach
@endif
@foreach (['provisional_diagnosis' => 'Provisional diagnosis', 'diagnosis' => 'Diagnosis', 'plan' => 'Plan', 'treatment' => 'Treatment and advice'] as $k => $label)
  @if ($c->$k)<h2>{{ $label }}</h2><p>{{ $c->$k }}</p>@endif
@endforeach
@php($gy = (array) (($c->specialty ?? [])['gynae'] ?? []))
@if ($gy)<h2>Obstetric</h2><p>@foreach ($gy as $k => $v){{ strtoupper(str_replace('_', ' ', $k)) }}: {{ $v }}; @endforeach</p>@endif
@php($eye = (array) (($c->specialty ?? [])['eye'] ?? []))
@if ($eye)<h2>Eye</h2><p>@foreach ($eye as $k => $v){{ strtoupper(str_replace('_', ' ', $k)) }}: {{ $v }}; @endforeach</p>@endif
@if ($c->follow_up_date)<h2>Follow-up</h2><p>{{ \Carbon\Carbon::parse($c->follow_up_date)->format('D d M Y') }}{{ $c->follow_up_notes ? ': ' . $c->follow_up_notes : '' }}</p>@endif
<p style="margin-top:40px">Signature ______________________</p>
<button onclick="window.print()">Print</button>
</body>
</html>
