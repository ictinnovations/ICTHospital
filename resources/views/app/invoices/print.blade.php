{{--
  ICTHospital - printable invoice for the patient.

  A page of its own rather than the invoice screen printed through the app layout:
  the layout's fixed sidebar, full-height wrapper and screen-only styling produced
  blank pages in some browsers. Same approach as the consultation and radiology
  print pages.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Invoice {{ $invoice->id }} {{ optional($patient)->patient_id }}</title>
<style>
  body { font-family: Arial, sans-serif; max-width: 190mm; margin: 12px auto; color: #111; font-size: 13px; }
  h1 { font-size: 18px; margin: 0 0 2px; } h2 { font-size: 14px; margin: 16px 0 4px; }
  .meta td { padding: 2px 14px 2px 0; vertical-align: top; }
  table.lines { width: 100%; border-collapse: collapse; margin-top: 10px; }
  table.lines th, table.lines td { border: 1px solid #bbb; padding: 5px 7px; }
  table.lines th { background: #f2f2f2; text-align: left; }
  .r, table.lines .r { text-align: right; } .muted { color: #555; font-size: 12px; }
  tfoot th { background: none !important; }
  @media print { button { display: none; } body { margin: 0 auto; } }
</style>
</head>
<body>
@php($institute = \Illuminate\Support\Facades\Schema::hasTable('institute') && \Illuminate\Support\Facades\Schema::hasColumn('institute', 'name') ? \Illuminate\Support\Facades\DB::table('institute')->value('name') : null)
@php($branch = \App\Support\Branch::name($invoice->branch_id ?? null))
<h1>{{ $institute ?: 'Invoice' }}</h1>
@if ($branch)<div class="muted">{{ $branch }}</div>@endif
<table class="meta" style="margin-top:10px">
  <tr><td><strong>Invoice</strong></td><td>#{{ $invoice->id }}</td><td><strong>Date</strong></td><td>{{ $invoice->date }}</td></tr>
  <tr><td><strong>Patient</strong></td><td>{{ $patient ? $patient->name . ' (MR ' . $patient->patient_id . ')' : ($invoice->patient_name ?: '-') }}</td>
      <td><strong>Doctor</strong></td><td>{{ $doctor ? $doctor->name : ($invoice->doctor_name ?: '-') }}</td></tr>
  <tr><td><strong>Status</strong></td><td colspan="3">{{ ucfirst($invoice->settlement()) }}</td></tr>
</table>

<table class="lines">
  <thead><tr><th>Charge</th><th class="r">Quantity</th><th class="r">Unit</th><th class="r">Total</th></tr></thead>
  <tbody>
  @foreach ($invoice->items as $item)
    <tr><td>{{ $item->name }}</td><td class="r">{{ $item->quantity }}</td><td class="r">{{ number_format((float) $item->unit_price, 2) }}</td><td class="r">{{ number_format((float) $item->line_total, 2) }}</td></tr>
    @php($pkg = $item->payment_category_id ? \App\Models\ServicePackageItem::where('package_id', $item->payment_category_id)->get() : collect())
    @if ($pkg->isNotEmpty())
      @php($pkgNames = \App\Models\PaymentCategory::whereIn('id', $pkg->pluck('component_id'))->pluck('category', 'id'))
      <tr><td colspan="4" class="muted" style="padding-left:20px">Includes: {{ $pkg->map(fn ($c) => ($pkgNames[$c->component_id] ?? '') . ($c->quantity > 1 ? ' x' . $c->quantity : ''))->implode(', ') }}</td></tr>
    @endif
  @endforeach
  </tbody>
  <tfoot>
    <tr><th colspan="3" class="r">Subtotal</th><th class="r">{{ number_format((float) $invoice->amount, 2) }}</th></tr>
    <tr><th colspan="3" class="r">Discount</th><th class="r">{{ number_format((float) $invoice->discount, 2) }}</th></tr>
    <tr><th colspan="3" class="r">Tax</th><th class="r">{{ number_format((float) $invoice->vat, 2) }}</th></tr>
    <tr><th colspan="3" class="r">Total</th><th class="r">{{ number_format((float) $invoice->gross_total, 2) }}</th></tr>
    <tr><th colspan="3" class="r">Paid</th><th class="r">{{ number_format($invoice->paid(), 2) }}</th></tr>
    <tr><th colspan="3" class="r">Balance</th><th class="r">{{ number_format($invoice->balance(), 2) }}</th></tr>
  </tfoot>
</table>

@if ($invoice->payments->isNotEmpty())
  <h2>Payments received</h2>
  <table class="lines">
    <thead><tr><th>When</th><th>Method</th><th>Reference</th><th class="r">Amount</th></tr></thead>
    <tbody>
    @foreach ($invoice->payments as $payment)
      <tr><td>{{ $payment->paid_at }}</td><td>{{ $payment->method ?: '-' }}</td><td>{{ $payment->reference ?: '-' }}</td><td class="r">{{ number_format((float) $payment->amount, 2) }}</td></tr>
    @endforeach
    </tbody>
  </table>
@endif

<p style="margin-top:40px">Received by ______________________</p>
<button onclick="window.print()">Print</button>
<script>window.addEventListener('load', function () { window.print(); });</script>
</body>
</html>
