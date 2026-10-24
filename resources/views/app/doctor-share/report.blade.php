{{--
  ICTHospital - doctor share report, approval and payout.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('content')
    @include('app.partials._alerts')
    @php($canApprove = \App\Support\Permissions::allows('doctor_share_approve'))
    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well">
                    <h2><i class="glyphicon glyphicon-random"></i> Doctor share report</h2>
                    <div class="box-icon"><a href="{{ url('/doctor-share/policies') }}" class="btn btn-default btn-sm">Policies</a></div>
                </div>
                <div class="box-content">
                    <form method="get" class="row">
                        <div class="col-md-2 form-group"><label>From</label><input type="date" name="from" class="form-control" value="{{ $filters['from'] }}"></div>
                        <div class="col-md-2 form-group"><label>To</label><input type="date" name="to" class="form-control" value="{{ $filters['to'] }}"></div>
                        <div class="col-md-2 form-group"><label>Doctor</label><select name="doctor" class="form-control"><option value="">All</option>@foreach ($doctors as $d)<option value="{{ $d->id }}" {{ $filters['doctor'] === (string) $d->id ? 'selected' : '' }}>{{ $d->name }}</option>@endforeach</select></div>
                        <div class="col-md-2 form-group"><label>Doctor type</label><select name="role" class="form-control"><option value="">All</option><option value="performing" {{ $filters['role'] === 'performing' ? 'selected' : '' }}>Performing</option><option value="referring" {{ $filters['role'] === 'referring' ? 'selected' : '' }}>Referring</option></select></div>
                        <div class="col-md-1 form-group"><label>Share</label><select name="status" class="form-control"><option value="">All</option>@foreach (['pending' => 'Pending', 'approved' => 'Approved', 'paid' => 'Paid'] as $k => $l)<option value="{{ $k }}" {{ $filters['status'] === $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select></div>
                        <div class="col-md-2 form-group"><label>Invoice</label><select name="paid" class="form-control"><option value="">All</option><option value="paid" {{ $filters['paid'] === 'paid' ? 'selected' : '' }}>Paid in full</option><option value="unpaid" {{ $filters['paid'] === 'unpaid' ? 'selected' : '' }}>Not paid yet</option></select></div>
                        <div class="col-md-1 form-group"><label>&nbsp;</label><button class="btn btn-default btn-block">Search</button></div>
                    </form>

                    @if ($byDoctor->isNotEmpty())
                    <table class="table table-bordered table-condensed" style="max-width:820px">
                        <thead><tr><th>Doctor</th><th class="text-right">Pending</th><th class="text-right">Approved</th><th class="text-right">Paid</th><th class="text-right">Hospital keeps</th></tr></thead>
                        <tbody>
                        @foreach ($byDoctor as $id => $t)
                            <tr><td>{{ $doctorNames[$id] ?? $id }}</td><td class="text-right">{{ number_format($t['pending'], 2) }}</td><td class="text-right">{{ number_format($t['approved'], 2) }}</td><td class="text-right">{{ number_format($t['paid'], 2) }}</td><td class="text-right">{{ number_format($t['hospital'], 2) }}</td></tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif

                    <form method="post" id="share-form">
                        {{ csrf_field() }}
                        <div class="table-responsive">
                        <table class="table table-striped table-bordered table-condensed">
                            <thead><tr>
                                @if ($canApprove)<th><input type="checkbox" onclick="document.querySelectorAll('.share-pick').forEach(c => c.checked = this.checked)"></th>@endif
                                <th>Invoice</th><th>Date</th><th>Patient</th><th>Service</th><th>Doctor</th><th>Type</th>
                                <th class="text-right">Base</th><th class="text-right">%</th><th class="text-right">Share</th><th>Invoice</th><th>Share status</th>
                            </tr></thead>
                            <tbody>
                            @forelse ($shares as $s)
                                @php($settled = $s->invoice && $s->invoice->balance() <= 0.004)
                                <tr>
                                    @if ($canApprove)<td><input type="checkbox" class="share-pick" name="ids[]" value="{{ $s->id }}"></td>@endif
                                    <td><a href="{{ url('/invoices/' . $s->payment_id) }}">#{{ $s->payment_id }}</a></td>
                                    <td>{{ optional($s->invoice)->date }}</td>
                                    <td>{{ optional($s->invoice)->patient_name }}</td>
                                    <td>{{ optional($s->item)->name }}</td>
                                    <td>{{ $doctorNames[$s->doctor_id] ?? $s->doctor_id }}</td>
                                    <td>{{ ucfirst($s->role) }}</td>
                                    <td class="text-right">{{ number_format((float) $s->base_amount, 2) }}</td>
                                    <td class="text-right">{{ rtrim(rtrim($s->percent, '0'), '.') }}</td>
                                    <td class="text-right"><strong>{{ number_format((float) $s->amount, 2) }}</strong></td>
                                    <td>{!! $settled ? '<span class="label label-success">Paid</span>' : '<span class="label label-warning">Unpaid</span>' !!}</td>
                                    <td>{{ ucfirst($s->status) }}{{ $s->payout_ref ? ' (' . $s->payout_ref . ')' : '' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="12" class="text-muted">No shares in this range. Shares appear when an invoice is raised for a doctor who is on a share policy.</td></tr>
                            @endforelse
                            </tbody>
                            @if ($shares->isNotEmpty())
                            <tfoot><tr><th colspan="{{ $canApprove ? 9 : 8 }}" class="text-right">Total</th><th class="text-right">{{ number_format($shares->sum('amount'), 2) }}</th><th colspan="2"></th></tr></tfoot>
                            @endif
                        </table>
                        </div>
                        @if ($canApprove && $shares->isNotEmpty())
                            <button formaction="{{ url('/doctor-share/approve') }}" class="btn btn-primary">Approve share</button>
                            <input name="reference" class="form-control" style="display:inline-block;width:200px" placeholder="Payout reference (optional)">
                            <button formaction="{{ url('/doctor-share/pay') }}" class="btn btn-success" onclick="return confirm('Mark the ticked approved shares as paid?')">Pay share</button>
                            <p class="help-block">Only shares on invoices paid in full can be approved. Paying writes one expense per doctor to Accounting under "Doctor share".</p>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
