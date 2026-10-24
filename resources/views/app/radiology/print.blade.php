{{--
  ICTHospital - printable radiology report.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{{ $order->name }} {{ optional($patient)->patient_id }}</title>
<style>
  body { font-family: Arial, sans-serif; max-width: 190mm; margin: 12px auto; color: #111; font-size: 13px; }
  h1 { font-size: 18px; margin: 0 0 6px; } h2 { font-size: 14px; margin: 16px 0 4px; }
  td { padding: 2px 12px 2px 0; } p { white-space: pre-line; margin: 0; }
  @media print { button { display: none; } }
</style>
</head>
<body>
@php($institute = \Illuminate\Support\Facades\Schema::hasTable('institute') && \Illuminate\Support\Facades\Schema::hasColumn('institute', 'name') ? \Illuminate\Support\Facades\DB::table('institute')->value('name') : null)
<h1>{{ $institute ? $institute . ': ' : '' }}Radiology report</h1>
<table>
  <tr><td><strong>Patient</strong></td><td>{{ optional($patient)->name }} (MR {{ optional($patient)->patient_id }})</td><td><strong>Age / sex</strong></td><td>{{ optional($patient)->age }} {{ optional($patient)->sex }}</td></tr>
  <tr><td><strong>Exam</strong></td><td>{{ $order->name }} ({{ \App\Models\RadiologyExam::MODALITIES[$order->modality] ?? $order->modality }})</td><td><strong>Date</strong></td><td>{{ $order->performed_at ?: $order->created_at }}</td></tr>
  <tr><td><strong>Referred by</strong></td><td>{{ optional($doctor)->name }}</td><td><strong>Order</strong></td><td>#{{ $order->id }}</td></tr>
</table>
@if ($order->clinical_notes)<h2>Clinical notes</h2><p>{{ $order->clinical_notes }}</p>@endif
<h2>Findings</h2><p>{{ $order->findings }}</p>
<h2>Impression</h2><p><strong>{{ $order->impression }}</strong></p>
<p style="margin-top:40px">Reported by {{ $reporter ? trim($reporter->firstname . ' ' . $reporter->lastname) : '' }}, {{ $order->reported_at }}</p>
<button onclick="window.print()">Print</button>
</body>
</html>
