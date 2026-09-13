{{--
  ICTHospital - add and edit form for every staff type.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    @php($editing = (bool) $person->id)

    <div class="row">
        <div class="box col-md-8">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon {{ $meta['icon'] }}"></i>
                        {{ $editing ? 'Edit ' . $person->name : 'Add ' . $meta['singular'] }}
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/staff/' . $type) }}" class="btn btn-default btn-sm">Back to list</a>
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

                    <form role="form" method="post" enctype="multipart/form-data"
                          action="{{ $editing
                              ? url('/staff/' . $type . '/' . $person->id)
                              : url('/staff/' . $type) }}">
                        {{ csrf_field() }}
                        @if ($editing)
                            <input type="hidden" name="_method" value="PUT">
                        @endif

                        <div class="form-group">
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required
                                   value="{{ old('name', $person->name) }}">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', $person->phone) }}">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email', $person->email) }}">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" class="form-control"
                                   value="{{ old('address', $person->address) }}">
                        </div>
                        <div class="form-group">
                            <label>Photo</label>
                            <input type="file" name="photo" accept="image/*">
                            @if ($person->img_url)
                                <span class="help-block">A photo is already on file. Uploading replaces it.</span>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ $editing ? 'Save changes' : 'Add ' . $meta['singular'] }}
                        </button>
                        <a href="{{ url('/staff/' . $type) }}" class="btn btn-default">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
