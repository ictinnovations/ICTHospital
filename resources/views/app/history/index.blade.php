{{--
  ICTHospital - patient medical history.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    @if (Session::get('success'))
        <div class="alert alert-success">
            <button data-dismiss="alert" class="close" type="button">&times;</button>
            {{ Session::get('success') }}
        </div>
    @endif

    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-folder-open"></i> Medical history
                        @if ($patient)
                            <small>{{ $patient->name }} ({{ $patient->patient_id }})</small>
                        @endif
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/history/create' . ($patientId ? '?patient=' . $patientId : '')) }}"
                           class="btn btn-primary btn-sm">
                            <i class="glyphicon glyphicon-plus"></i> Add entry
                        </a>
                    </div>
                </div>
                <div class="box-content">

                    <form role="form" action="{{ url('/history') }}" method="get" class="form-inline">
                        <div class="form-group">
                            <select name="patient" class="form-control">
                                <option value="">Every patient</option>
                                @foreach ($patients as $p)
                                    <option value="{{ $p->id }}" {{ (string) $patientId === (string) $p->id ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ $p->patient_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" name="q" class="form-control" style="min-width:240px"
                                   value="{{ $term }}" placeholder="Title or notes">
                        </div>
                        <button type="submit" class="btn btn-default">Search</button>
                        @if ($term !== '' || $patientId)
                            <a href="{{ url('/history') }}" class="btn btn-link">Clear</a>
                        @endif
                    </form>

                    <br>

                    @if ($entries->total() === 0)
                        <p class="text-muted">
                            @if ($term !== '' || $patientId)
                                Nothing on file for that.
                            @else
                                No history recorded yet. Allergies, past operations and chronic conditions
                                go here, and they are the first thing worth reading before a consultation.
                            @endif
                        </p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:12%">Date</th>
                                    <th style="width:18%">Patient</th>
                                    <th style="width:20%">Title</th>
                                    <th>Notes</th>
                                    <th style="width:10%">Attachment</th>
                                    <th style="width:10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($entries as $entry)
                                    <tr>
                                        <td>{{ $entry->date }}</td>
                                        <td>
                                            @if ($entry->patient_id)
                                                <a href="{{ url('/patients/' . $entry->patient_id) }}">{{ $entry->patient_name }}</a>
                                            @else
                                                {{ $entry->patient_name }}
                                            @endif
                                        </td>
                                        <td>{{ $entry->title }}</td>
                                        <td>{{ \Illuminate\Support\Str::limit($entry->description, 160) }}</td>
                                        <td>
                                            @if ($entry->img_url)
                                                <a href="{{ asset('storage/' . $entry->img_url) }}" target="_blank">View</a>
                                            @else
                                                <span class="text-muted">None</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-default btn-sm" href="{{ url('/history/' . $entry->id . '/edit') }}">
                                                <i class="glyphicon glyphicon-edit"></i>
                                            </a>
                                            <form action="{{ url('/history/' . $entry->id) }}" method="post"
                                                  style="display:inline"
                                                  onsubmit="return confirm('Remove this history entry?');">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="glyphicon glyphicon-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <p class="text-muted">{{ $entries->total() }} entry(s).</p>
                        {!! $entries->links() !!}
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
