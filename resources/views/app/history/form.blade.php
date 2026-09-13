{{--
  ICTHospital - add and edit a medical history entry.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    @php($editing = (bool) $entry->id)

    <div class="row">
        <div class="box col-md-9">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-folder-open"></i>
                        {{ $editing ? 'Edit history entry' : 'Add history entry' }}
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/history') }}" class="btn btn-default btn-sm">Back to list</a>
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
                          action="{{ $editing ? url('/history/' . $entry->id) : url('/history') }}">
                        {{ csrf_field() }}
                        @if ($editing)
                            <input type="hidden" name="_method" value="PUT">
                        @endif

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Patient <span class="text-danger">*</span></label>
                                <select name="patient_id" class="form-control" required>
                                    <option value="">Choose a patient</option>
                                    @foreach ($patients as $p)
                                        <option value="{{ $p->id }}"
                                            {{ (string) old('patient_id', $entry->patient_id) === (string) $p->id ? 'selected' : '' }}>
                                            {{ $p->name }} ({{ $p->patient_id }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label>Date</label>
                                <input type="date" name="date" class="form-control"
                                       value="{{ old('date', $entry->date) }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required
                                   placeholder="Penicillin allergy"
                                   value="{{ old('title', $entry->title) }}">
                        </div>

                        <div class="form-group">
                            <label>Notes <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="8"
                                      required>{{ old('description', $entry->description) }}</textarea>
                            <span class="help-block">
                                Write what the next clinician needs to know before they see this patient.
                            </span>
                        </div>

                        <div class="form-group">
                            <label>Attachment</label>
                            <input type="file" name="attachment" accept="image/*">
                            @if ($entry->img_url)
                                <span class="help-block">
                                    <a href="{{ asset('storage/' . $entry->img_url) }}" target="_blank">A file is already attached.</a>
                                    Uploading replaces it.
                                </span>
                            @else
                                <span class="help-block">A scan or a photo of a report, if you have one.</span>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary">
                            {{ $editing ? 'Save changes' : 'Add entry' }}
                        </button>
                        <a href="{{ url('/history') }}" class="btn btn-default">Cancel</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
