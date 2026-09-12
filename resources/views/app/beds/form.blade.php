{{--
  ICTHospital - add or edit a bed.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    @php($editing = (bool) $bed->id)

    <div class="row">
        <div class="box col-md-8">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-bed"></i> {{ $editing ? 'Edit bed' : 'Add bed' }}</h2>
                    <div class="box-icon">
                        <a href="{{ url('/beds') }}" class="btn btn-default btn-sm">Back to ward board</a>
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
                          action="{{ $editing ? url('/beds/' . $bed->id) : url('/beds') }}">
                        {{ csrf_field() }}
                        @if ($editing)
                            <input type="hidden" name="_method" value="PUT">
                        @endif

                        <div class="form-group">
                            <label>Ward type <span class="text-danger">*</span></label>
                            <select name="category" class="form-control" required>
                                <option value="">Choose a ward type</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ (string) old('category', $bed->category) === (string) $category->id ? 'selected' : '' }}>
                                        {{ $category->category }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($categories->isEmpty())
                                <span class="help-block">
                                    No ward types yet. <a href="{{ url('/beds/categories') }}">Add one first.</a>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Bed number <span class="text-danger">*</span></label>
                            <input type="text" name="number" class="form-control" required
                                   value="{{ old('number', $bed->number) }}">
                            <span class="help-block">Unique within the ward, not across the hospital.</span>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" name="description" class="form-control"
                                   value="{{ old('description', $bed->description) }}">
                        </div>

                        <button type="submit" class="btn btn-primary">{{ $editing ? 'Save changes' : 'Add bed' }}</button>
                        <a href="{{ url('/beds') }}" class="btn btn-default">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
