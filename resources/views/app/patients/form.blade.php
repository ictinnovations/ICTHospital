{{--
  ICTHospital - patient registration and edit form.

  One form for both, because the fields are identical and two near-copies drift
  apart the moment a column is added.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    @php($editing = (bool) $patient->id)

    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-user"></i>
                        {{ $editing ? 'Edit patient ' . $patient->patient_id : 'Register patient' }}
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/patients') }}" class="btn btn-default btn-sm">Back to list</a>
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
                          action="{{ $editing ? url('/patients/' . $patient->id) : url('/patients') }}">
                        {{ csrf_field() }}
                        @if ($editing)
                            <input type="hidden" name="_method" value="PUT">
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required
                                           value="{{ old('name', $patient->name) }}">
                                </div>
                                <div class="form-group">
                                    <label>Father or guardian name</label>
                                    <input type="text" name="father_name" class="form-control"
                                           value="{{ old('father_name', $patient->father_name) }}">
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control"
                                           value="{{ old('phone', $patient->phone) }}">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control"
                                           value="{{ old('email', $patient->email) }}">
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" name="address" class="form-control"
                                           value="{{ old('address', $patient->address) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date of birth</label>
                                    <input type="date" name="birthdate" class="form-control"
                                           value="{{ old('birthdate', $patient->birthdate) }}">
                                    <span class="help-block">Age is worked out from this, so it never goes stale.</span>
                                </div>
                                <div class="form-group">
                                    <label>Sex</label>
                                    <select name="sex" class="form-control">
                                        <option value="">Not recorded</option>
                                        @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ old('sex', $patient->sex) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Blood group</label>
                                    <select name="bloodgroup" class="form-control">
                                        <option value="">Not recorded</option>
                                        @foreach ($bloodGroups as $group)
                                            <option value="{{ $group }}"
                                                {{ old('bloodgroup', $patient->bloodgroup) === $group ? 'selected' : '' }}>{{ $group }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Assigned doctor</label>
                                    <select name="doctor" class="form-control">
                                        <option value="">Not assigned</option>
                                        @foreach ($doctors as $name)
                                            <option value="{{ $name }}"
                                                {{ old('doctor', $patient->doctor) === $name ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Photo</label>
                                    <input type="file" name="photo" accept="image/*">
                                    @if ($patient->img_url)
                                        <span class="help-block">A photo is already on file. Uploading replaces it.</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ $editing ? 'Save changes' : 'Register patient' }}
                        </button>
                        <a href="{{ url('/patients') }}" class="btn btn-default">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
