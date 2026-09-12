{{--
  ICTHospital - dispense medicines.

  Arriving from a prescription pre-loads its drugs, which is the common case at a
  hospital counter: somebody turns up holding one.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    @php($prefill = $prescription ? $prescription->items->map(fn ($i) => ['medicine_id' => $i->medicine_id, 'name' => $i->name])->all() : [])
    @php($rows = old('items', count($prefill) ? $prefill : [[]]))

    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-shopping-cart"></i> Dispense</h2>
                    <div class="box-icon">
                        <a href="{{ url('/pharmacy') }}" class="btn btn-default btn-sm">Back to sales</a>
                    </div>
                </div>
                <div class="box-content">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Check the form.</strong>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($prescription)
                        <p class="text-muted">
                            Loaded from prescription
                            <a href="{{ url('/prescriptions/' . $prescription->id) }}">#{{ $prescription->id }}</a>.
                            Quantities still need entering, since a prescription says what to take, not how much to hand over.
                        </p>
                    @endif

                    <form role="form" method="post" action="{{ url('/pharmacy') }}">
                        {{ csrf_field() }}

                        <div class="row">
                            <div class="col-md-5 form-group">
                                <label>Patient</label>
                                <select name="patient" class="form-control">
                                    <option value="">Counter sale, no patient record</option>
                                    @foreach ($patients as $p)
                                        <option value="{{ $p->id }}"
                                            {{ (string) old('patient', $selectedPatient) === (string) $p->id ? 'selected' : '' }}>
                                            {{ $p->name }} ({{ $p->patient_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" required
                                       value="{{ old('date', now()->toDateString()) }}">
                            </div>
                        </div>

                        <h4>Items <span class="text-danger">*</span></h4>
                        <table class="table table-bordered" id="sale-rows">
                            <thead>
                                <tr>
                                    <th style="width:40%">Medicine</th>
                                    <th style="width:20%">or type a name</th>
                                    <th style="width:15%">Quantity</th>
                                    <th style="width:20%">Unit price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $i => $row)
                                    <tr>
                                        <td>
                                            <select name="items[{{ $i }}][medicine_id]" class="form-control">
                                                <option value="">Not from the catalogue</option>
                                                @foreach ($medicines as $m)
                                                    <option value="{{ $m->id }}"
                                                        {{ (string) ($row['medicine_id'] ?? '') === (string) $m->id ? 'selected' : '' }}>
                                                        {{ $m->name }} ({{ (int) $m->quantity }} in stock)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" name="items[{{ $i }}][name]" class="form-control"
                                                   value="{{ $row['name'] ?? '' }}"></td>
                                        <td><input type="number" min="1" name="items[{{ $i }}][quantity]"
                                                   class="form-control" value="{{ $row['quantity'] ?? 1 }}"></td>
                                        <td><input type="number" step="0.01" min="0" name="items[{{ $i }}][unit_price]"
                                                   class="form-control" placeholder="catalogue price"
                                                   value="{{ $row['unit_price'] ?? '' }}"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-default btn-sm" id="add-row">
                            <i class="glyphicon glyphicon-plus"></i> Add another item
                        </button>

                        <div class="row" style="margin-top:15px">
                            <div class="col-md-3 form-group">
                                <label>Discount</label>
                                <input type="number" step="0.01" min="0" name="discount" class="form-control"
                                       value="{{ old('discount', 0) }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Tax</label>
                                <input type="number" step="0.01" min="0" name="vat" class="form-control"
                                       value="{{ old('vat', 0) }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Amount received</label>
                                <input type="number" step="0.01" min="0" name="amount_received" class="form-control"
                                       value="{{ old('amount_received') }}">
                                <span class="help-block">Blank means the full total.</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Dispense and receipt</button>
                        <a href="{{ url('/pharmacy') }}" class="btn btn-default">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
<script>
document.getElementById('add-row').addEventListener('click', function () {
    var body = document.querySelector('#sale-rows tbody');
    var rows = body.querySelectorAll('tr');
    var copy = rows[rows.length - 1].cloneNode(true);
    var next = rows.length;

    copy.querySelectorAll('select, input').forEach(function (field) {
        field.name = field.name.replace(/items\[\d+\]/, 'items[' + next + ']');
        if (field.tagName === 'SELECT') {
            field.selectedIndex = 0;
        } else if (field.type === 'number' && field.name.indexOf('quantity') !== -1) {
            field.value = 1;
        } else {
            field.value = '';
        }
    });

    body.appendChild(copy);
});
</script>
@stop
