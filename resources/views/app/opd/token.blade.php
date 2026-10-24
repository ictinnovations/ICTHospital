{{--
  ICTHospital - printable OPD token slip (thermal printer width).

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Token {{ $visit->token_no }}</title>
<style>
  body { font-family: Arial, sans-serif; width: 72mm; margin: 6px auto; text-align: center; color: #000; }
  .tok { font-size: 54px; font-weight: bold; margin: 6px 0; }
  .small { font-size: 12px; line-height: 1.5; }
  hr { border: 0; border-top: 1px dashed #000; }
  @media print { button { display: none; } }
</style>
</head>
<body>
  @php($institute = \Illuminate\Support\Facades\Schema::hasTable('institute') && \Illuminate\Support\Facades\Schema::hasColumn('institute', 'name') ? \Illuminate\Support\Facades\DB::table('institute')->value('name') : null)
  <div class="small"><strong>{{ $institute ?: 'OPD' }}</strong></div>
  <hr>
  <div class="small">Token</div>
  <div class="tok">{{ $visit->token_no }}</div>
  <div class="small">
    {{ optional($doctor)->name }}<br>
    {{ optional($patient)->name }} &middot; MR {{ optional($patient)->patient_id }}<br>
    {{ \Carbon\Carbon::parse($visit->checked_in_at ?: now())->format('d M Y, H:i') }}<br>
    @if ($ahead > 0){{ $ahead }} patient(s) ahead of you @endif
  </div>
  <hr>
  <button onclick="window.print()">Print</button>
</body>
</html>
