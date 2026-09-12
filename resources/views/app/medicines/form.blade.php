{{--
  ICTHospital - add or edit a medicine.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    @php($editing = (bool) $medicine->id)

    <div class="row">
        <div class="box col-md-10">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-plus-sign"></i>
                        {{ $editing ? 'Edit medicine' : 'Add medicine' }}
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/medicines') }}" class="btn btn-default btn-sm">Back to catalogue</a>
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

                    <form role="form" method="post"
                          action="{{ $editing ? url('/medicines/' . $medicine->id) : url('/medicines') }}">
                        {{ csrf_field() }}
                        @if ($editing)
                            <input type="hidden" name="_method" value="PUT">
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Brand name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required
                                           value="{{ old('name', $medicine->name) }}">
                                </div>
                                <div class="form-group">
                                    <label>Generic name</label>
                                    <input type="text" name="generic" class="form-control"
                                           value="{{ old('generic', $medicine->generic) }}">
                                </div>
                                <div class="form-group">
                                    <label>Company</label>
                                    <input type="text" name="company" class="form-control"
                                           value="{{ old('company', $medicine->company) }}">
                                </div>
                                <div class="form-group">
                                    <label>Category</label>
                                    <select name="category" class="form-control">
                                        <option value="">Uncategorised</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ (string) old('category', $medicine->category) === (string) $category->id ? 'selected' : '' }}>
                                                {{ $category->category }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pack or strength</label>
                                    <input type="text" name="box" class="form-control"
                                           value="{{ old('box', $medicine->box) }}">
                                    <span class="help-block">
                                        Name and pack together have to be unique, so two entries for the same
                                        thing cannot make the prescribing list ambiguous.
                                    </span>
                                </div>
                                <div class="form-group">
                                    <label>Quantity in stock</label>
                                    <input type="number" name="quantity" class="form-control" min="0"
                                           value="{{ old('quantity', $medicine->quantity ?? 0) }}">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label>Cost price</label>
                                        <input type="number" step="0.01" name="price" class="form-control"
                                               value="{{ old('price', $medicine->price) }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label>Sale price</label>
                                        <input type="number" step="0.01" name="s_price" class="form-control"
                                               value="{{ old('s_price', $medicine->s_price) }}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Expiry date</label>
                                    <input type="date" name="e_date" class="form-control"
                                           value="{{ old('e_date', $medicine->e_date) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Side effects or notes</label>
                            <input type="text" name="effects" class="form-control"
                                   value="{{ old('effects', $medicine->effects) }}">
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ $editing ? 'Save changes' : 'Add medicine' }}
                        </button>
                        <a href="{{ url('/medicines') }}" class="btn btn-default">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
