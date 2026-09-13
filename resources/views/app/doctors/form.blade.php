{{--
  ICTHospital - doctor add and edit form.

  One form for both. The department box is free text with a datalist of the
  departments already in use, so spelling stays consistent without needing a
  lookup table that nobody can fill in yet.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    @php($editing = (bool) $doctor->id)

    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-briefcase"></i>
                        {{ $editing ? 'Edit ' . $doctor->name : 'Add doctor' }}
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/doctors') }}" class="btn btn-default btn-sm">Back to list</a>
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
                          action="{{ $editing ? url('/doctors/' . $doctor->id) : url('/doctors') }}">
                        {{ csrf_field() }}
                        @if ($editing)
                            <input type="hidden" name="_method" value="PUT">
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required
                                           value="{{ old('name', $doctor->name) }}">
                                </div>
                                <div class="form-group">
                                    <label>Department</label>
                                    <input type="text" name="department" class="form-control" list="departmentlist"
                                           value="{{ old('department', $doctor->department) }}">
                                    <datalist id="departmentlist">
                                        @foreach ($departments as $department)
                                            <option value="{{ $department }}"></option>
                                        @endforeach
                                    </datalist>
                                    <span class="help-block">Pick an existing one where you can, so the list stays tidy.</span>
                                </div>
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" name="phone" class="form-control"
                                           value="{{ old('phone', $doctor->phone) }}">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control"
                                           value="{{ old('email', $doctor->email) }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" name="address" class="form-control"
                                           value="{{ old('address', $doctor->address) }}">
                                </div>
                                <div class="form-group">
                                    <label>Qualification or speciality</label>
                                    <input type="text" name="profile" class="form-control"
                                           value="{{ old('profile', $doctor->profile) }}">
                                </div>
                                <div class="form-group">
                                    <label>ICTCore procedure</label>
                                    <input type="text" name="procedure_id" class="form-control"
                                           value="{{ old('procedure_id', $doctor->procedure_id) }}">
                                    <span class="help-block">Only needed if this doctor is reachable through the ICTCore integration.</span>
                                </div>
                                <div class="form-group">
                                    <label>Photo</label>
                                    <input type="file" name="photo" accept="image/*">
                                    @if ($doctor->img_url)
                                        <span class="help-block">A photo is already on file. Uploading replaces it.</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ $editing ? 'Save changes' : 'Add doctor' }}
                        </button>
                        <a href="{{ url('/doctors') }}" class="btn btn-default">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
