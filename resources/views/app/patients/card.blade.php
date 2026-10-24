{{--
  ICTHospital - printable health card (wallet size) or wristband.

  Stands alone, without the app layout, so it prints on a card or label printer
  with nothing else on the page.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{{ $wristband ? 'Wristband' : 'Health card' }} {{ $patient->patient_id }}</title>
<style>
  body { font-family: Arial, sans-serif; margin: 16px; color: #111; }
  .card { width: 85.6mm; height: 54mm; border: 1px solid #333; border-radius: 3mm; padding: 3mm 4mm; box-sizing: border-box; position: relative; }
  .band { width: 250mm; height: 25mm; border: 1px solid #333; border-radius: 12mm; padding: 2mm 10mm; box-sizing: border-box; display: flex; align-items: center; gap: 6mm; }
  .hosp { font-size: 9pt; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; }
  .name { font-size: 13pt; font-weight: bold; margin: 2mm 0 1mm; }
  .meta { font-size: 8.5pt; line-height: 1.45; }
  .mlc { color: #b00; font-weight: bold; }
  img.bc { height: 12mm; }
  .card img.bc { position: absolute; left: 4mm; bottom: 3mm; }
  .card .photo { position: absolute; right: 4mm; top: 9mm; width: 18mm; height: 22mm; object-fit: cover; border: 1px solid #999; }
  .noprint { margin-top: 12px; }
  @media print { .noprint { display: none; } body { margin: 0; } }
</style>
</head>
<body>
@php($institute = \Illuminate\Support\Facades\Schema::hasTable('institute') ? \Illuminate\Support\Facades\DB::table('institute')->value('name') : null)
@if ($wristband)
  <div class="band">
    <div>
      <div class="hosp">{{ $institute ?: 'Hospital' }}</div>
      <div class="name" style="margin:1mm 0">{{ $patient->name }}</div>
      <div class="meta">MR {{ $patient->patient_id }} &middot; {{ $patient->age !== '' ? $patient->age . ' y' : '' }} {{ $patient->sex ? ucfirst($patient->sex) : '' }}
        {{ $patient->bloodgroup ? '· ' . $patient->bloodgroup : '' }} @if ($patient->mlc)<span class="mlc">· MLC</span>@endif</div>
    </div>
    <img class="bc" src="{{ $barcode }}" alt="{{ $patient->patient_id }}">
  </div>
@else
  <div class="card">
    <div class="hosp">{{ $institute ?: 'Hospital' }} &middot; Health card</div>
    <div class="name">{{ $patient->name }}</div>
    <div class="meta">
      MR number: <strong>{{ $patient->patient_id }}</strong><br>
      @if ($patient->cnic)CNIC: {{ $patient->cnic }}<br>@endif
      @if ($patient->bloodgroup)Blood group: {{ $patient->bloodgroup }}<br>@endif
      {{ optional($type)->name }}
    </div>
    @if ($patient->img_url)<img class="photo" src="{{ asset('storage/' . $patient->img_url) }}" alt="">@endif
    <img class="bc" src="{{ $barcode }}" alt="{{ $patient->patient_id }}">
  </div>
@endif
<div class="noprint"><button onclick="window.print()">Print</button></div>
</body>
</html>
