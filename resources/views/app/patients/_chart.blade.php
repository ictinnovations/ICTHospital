{{--
  ICTHospital - patient chart, shown under the patient's details.

  Everything recorded against one patient, newest first, each read from the module
  that owns it: history notes, OPD visits with vitals, prescriptions, lab results,
  ward stays and billing. Each section links to that module for adding or editing.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@php($onWard = $admissions->firstWhere('d_time', null))
@php($pid = $patient->id)
<style>.patient-chart-section { margin-top: 22px; } .patient-chart-section h4 { border-bottom: 1px solid #e5e5e5; padding-bottom: 6px; }</style>

<div class="row" style="margin-top:15px">
    <div class="col-sm-3"><div class="well well-sm text-center">
        <div class="text-muted">OPD visits</div><h3 style="margin:5px 0">{{ $appointments->count() }}</h3>
    </div></div>
    <div class="col-sm-3"><div class="well well-sm text-center">
        <div class="text-muted">Ward status</div>
        <h3 style="margin:5px 0">{{ $onWard ? 'Admitted' : 'Not admitted' }}</h3>
    </div></div>
    <div class="col-sm-3"><div class="well well-sm text-center">
        <div class="text-muted">Billed / paid</div>
        <h3 style="margin:5px 0">{{ number_format($billed, 2) }} / {{ number_format($paid, 2) }}</h3>
    </div></div>
    <div class="col-sm-3"><div class="well well-sm text-center">
        <div class="text-muted">Outstanding</div>
        <h3 style="margin:5px 0" class="{{ $outstanding > 0.004 ? 'text-danger' : 'text-success' }}">{{ number_format($outstanding, 2) }}</h3>
    </div></div>
</div>

{{-- Medical history --}}
<div class="patient-chart-section" id="chart-history">
    <h4><i class="glyphicon glyphicon-folder-open"></i> Medical history
        <a href="{{ url('/history/create?patient=' . $pid) }}" class="btn btn-default btn-xs pull-right">Add note</a></h4>
    @if ($history->isEmpty())
        <p class="text-muted">No history notes yet.</p>
    @else
        <table class="table table-condensed table-striped">
            <thead><tr><th style="width:110px">Date</th><th style="width:220px">Title</th><th>Notes</th><th style="width:60px"></th></tr></thead>
            <tbody>
            @foreach ($history as $note)
                <tr>
                    <td>{{ $note->date ?: '-' }}</td>
                    <td>{{ $note->title }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($note->description, 160) }}</td>
                    <td><a href="{{ url('/history/' . $note->id . '/edit') }}">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- OPD visits --}}
<div class="patient-chart-section" id="chart-visits">
    <h4><i class="glyphicon glyphicon-calendar"></i> OPD visits and vitals
        <a href="{{ url('/appointments/create?patient=' . $pid) }}" class="btn btn-default btn-xs pull-right">Book appointment</a></h4>
    @if ($appointments->isEmpty())
        <p class="text-muted">No appointments yet.</p>
    @else
        <table class="table table-condensed table-striped">
            <thead><tr><th>Date</th><th>Time</th><th>Doctor</th><th>Status</th><th>BP</th><th>Pulse</th><th>Temp</th><th>Weight</th><th>Remarks</th><th></th></tr></thead>
            <tbody>
            @foreach ($appointments as $visit)
                <tr>
                    <td>{{ $visit->date }}</td>
                    <td>{{ $visit->s_time ? substr((string) $visit->s_time, 11, 5) : '-' }}</td>
                    <td>{{ $doctorNames[$visit->doctor] ?? '-' }}</td>
                    <td>{{ ucfirst((string) $visit->status) }}</td>
                    <td>{{ $visit->b_p ?: '-' }}</td>
                    <td>{{ $visit->pulse ?: '-' }}</td>
                    <td>{{ $visit->temprature ?: '-' }}</td>
                    <td>{{ $visit->weight ?: '-' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit((string) $visit->remarks, 80) }}</td>
                    <td><a href="{{ url('/appointments/' . $visit->id) }}">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- Prescriptions --}}
<div class="patient-chart-section" id="chart-prescriptions">
    <h4><i class="glyphicon glyphicon-list-alt"></i> Prescriptions
        <a href="{{ url('/prescriptions/create?patient=' . $pid) }}" class="btn btn-default btn-xs pull-right">New prescription</a></h4>
    @if ($prescriptions->isEmpty())
        <p class="text-muted">No prescriptions yet.</p>
    @else
        <table class="table table-condensed table-striped">
            <thead><tr><th style="width:110px">Date</th><th style="width:170px">Doctor</th><th style="width:200px">Symptoms</th><th>Medicines</th><th style="width:60px"></th></tr></thead>
            <tbody>
            @foreach ($prescriptions as $rx)
                <tr>
                    <td>{{ $rx->date }}</td>
                    <td>{{ $doctorNames[$rx->doctor] ?? '-' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit((string) $rx->symptom, 80) ?: '-' }}</td>
                    <td>
                        @forelse ($rx->items as $item)
                            <div>{{ $item->name }}{{ $item->dosage ? ', ' . $item->dosage : '' }}{{ $item->duration ? ' for ' . $item->duration : '' }}</div>
                        @empty
                            {{ $rx->medicine ?: '-' }}
                        @endforelse
                    </td>
                    <td><a href="{{ url('/prescriptions/' . $rx->id) }}">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- Laboratory --}}
<div class="patient-chart-section" id="chart-lab">
    <h4><i class="glyphicon glyphicon-tint"></i> Lab results
        <a href="{{ url('/lab/create?patient=' . $pid) }}" class="btn btn-default btn-xs pull-right">Request tests</a></h4>
    @if ($labs->isEmpty())
        <p class="text-muted">No lab requests yet.</p>
    @else
        <table class="table table-condensed table-striped">
            <thead><tr><th style="width:110px">Date</th><th style="width:170px">Doctor</th><th>Test</th><th>Result</th><th>Reference</th><th style="width:60px"></th></tr></thead>
            <tbody>
            @foreach ($labs as $lab)
                @foreach ($lab->tests as $i => $test)
                    <tr>
                        <td>{{ $i === 0 ? $lab->date : '' }}</td>
                        <td>{{ $i === 0 ? ($doctorNames[$lab->doctor] ?? '-') : '' }}</td>
                        <td>{{ $test->name }}</td>
                        <td>@if ($test->result !== null && $test->result !== ''){{ $test->result }}@else<span class="label label-warning">Pending</span>@endif</td>
                        <td>{{ $test->reference_value ?: '-' }}</td>
                        <td>@if ($i === 0)<a href="{{ url('/lab/' . $lab->id) }}">Open</a>@endif</td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- IPD --}}
<div class="patient-chart-section" id="chart-admissions">
    <h4><i class="glyphicon glyphicon-bed"></i> Admissions (IPD)
        @unless ($onWard)
            <a href="{{ url('/admissions/create?patient=' . $pid) }}" class="btn btn-default btn-xs pull-right">Admit</a>
        @endunless</h4>
    @if ($admissions->isEmpty())
        <p class="text-muted">No admissions yet.</p>
    @else
        <table class="table table-condensed table-striped">
            <thead><tr><th>Ward</th><th>Bed</th><th>Admitted</th><th>Discharged</th><th>Days</th><th style="width:60px"></th></tr></thead>
            <tbody>
            @foreach ($admissions as $stay)
                @php($from = $stay->a_time ? \Illuminate\Support\Carbon::parse($stay->a_time) : null)
                @php($to = $stay->d_time ? \Illuminate\Support\Carbon::parse($stay->d_time) : now())
                <tr>
                    <td>{{ $bedCategories[$stay->category] ?? '-' }}</td>
                    <td>{{ optional($beds->get($stay->bed_id))->number ?? $stay->number }}</td>
                    <td>{{ $stay->a_time ?: '-' }}</td>
                    <td>@if ($stay->d_time){{ $stay->d_time }}@else<span class="label label-info">On the ward</span>@endif</td>
                    <td>{{ $from ? max(1, (int) ceil($from->diffInHours($to) / 24)) : '-' }}</td>
                    <td><a href="{{ url('/admissions/' . $stay->id) }}">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>

{{-- Billing --}}
<div class="patient-chart-section" id="chart-billing">
    <h4><i class="glyphicon glyphicon-usd"></i> Billing and payments
        <a href="{{ url('/invoices/create?patient=' . $pid) }}" class="btn btn-default btn-xs pull-right">New invoice</a></h4>
    @if ($invoices->isEmpty())
        <p class="text-muted">No invoices yet.</p>
    @else
        <table class="table table-condensed table-striped">
            <thead><tr><th>Invoice</th><th>Date</th><th class="text-right">Total</th><th class="text-right">Paid</th><th class="text-right">Balance</th><th>Status</th><th style="width:60px"></th></tr></thead>
            <tbody>
            @foreach ($invoices as $invoice)
                @php($invoicePaid = $invoice->paid())
                @php($invoiceBalance = $invoice->balance())
                @php($state = $invoice->settlement())
                <tr>
                    <td>#{{ $invoice->id }}</td>
                    <td>{{ $invoice->date }}</td>
                    <td class="text-right">{{ number_format((float) $invoice->gross_total, 2) }}</td>
                    <td class="text-right">{{ number_format($invoicePaid, 2) }}</td>
                    <td class="text-right">{{ number_format($invoiceBalance, 2) }}</td>
                    <td><span class="label label-{{ ['paid' => 'success', 'part paid' => 'warning'][$state] ?? 'danger' }}">{{ ucfirst($state) }}</span></td>
                    <td><a href="{{ url('/invoices/' . $invoice->id) }}">Open</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
