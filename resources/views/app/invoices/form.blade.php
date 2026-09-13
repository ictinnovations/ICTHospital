{{--
  ICTHospital - raise an invoice.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    @php($rows = old('items', [[]]))

    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-usd"></i> Raise invoice</h2>
                    <div class="box-icon">
                        <a href="{{ url('/invoices') }}" class="btn btn-default btn-sm">Back to invoices</a>
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

                    <form role="form" method="post" action="{{ url('/invoices') }}">
                        {{ csrf_field() }}

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Patient <span class="text-danger">*</span></label>
                                <select name="patient" class="form-control" required>
                                    <option value="">Choose a patient</option>
                                    @foreach ($patients as $p)
                                        <option value="{{ $p->id }}"
                                            {{ (string) old('patient', $invoice->patient) === (string) $p->id ? 'selected' : '' }}>
                                            {{ $p->name }} ({{ $p->patient_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Doctor</label>
                                <select name="doctor" class="form-control">
                                    <option value="">Not recorded</option>
                                    @foreach ($doctors as $d)
                                        <option value="{{ $d->id }}"
                                            {{ (string) old('doctor') === (string) $d->id ? 'selected' : '' }}>
                                            {{ $d->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" required
                                       value="{{ old('date', now()->toDateString()) }}">
                            </div>
                        </div>

                        <h4>Charges <span class="text-danger">*</span></h4>
                        @if ($services->isEmpty())
                            <p class="text-muted">
                                Nothing is on the price list yet, so every line has to be typed by hand.
                                <a href="{{ url('/services') }}">Add your services and prices</a> and they
                                appear in the select with their price filled in.
                            </p>
                        @endif
                        <table class="table table-bordered" id="invoice-rows">
                            <thead>
                                <tr>
                                    <th style="width:40%">Service</th>
                                    <th style="width:20%">or type a description</th>
                                    <th style="width:15%">Quantity</th>
                                    <th style="width:20%">Unit price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $i => $row)
                                    <tr>
                                        <td>
                                            <select name="items[{{ $i }}][payment_category_id]" class="form-control">
                                                <option value="">Not a listed service</option>
                                                @foreach ($services as $s)
                                                    <option value="{{ $s->id }}"
                                                        {{ (string) ($row['payment_category_id'] ?? '') === (string) $s->id ? 'selected' : '' }}>
                                                        {{ $s->category }}{{ $s->c_price ? ' (' . $s->c_price . ')' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="text" name="items[{{ $i }}][name]" class="form-control"
                                                   value="{{ $row['name'] ?? '' }}"></td>
                                        <td><input type="number" min="1" name="items[{{ $i }}][quantity]"
                                                   class="form-control" value="{{ $row['quantity'] ?? 1 }}"></td>
                                        <td><input type="number" step="0.01" min="0" name="items[{{ $i }}][unit_price]"
                                                   class="form-control" placeholder="list price"
                                                   value="{{ $row['unit_price'] ?? '' }}"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-default btn-sm" id="add-row">
                            <i class="glyphicon glyphicon-plus"></i> Add another charge
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
                            <div class="col-md-6 form-group">
                                <label>Remarks</label>
                                <input type="text" name="remarks" class="form-control" maxlength="500"
                                       value="{{ old('remarks') }}">
                            </div>
                        </div>

                        <p class="help-block">
                            The invoice is raised unpaid. Payments are taken on the invoice itself, one at a
                            time, so a deposit now and the balance later is a normal thing to record.
                        </p>

                        <button type="submit" class="btn btn-primary">Raise invoice</button>
                        <a href="{{ url('/invoices') }}" class="btn btn-default">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
<script>
document.getElementById('add-row').addEventListener('click', function () {
    var body = document.querySelector('#invoice-rows tbody');
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
