{{--
  ICTHospital - ward board.

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
    @if (Session::get('error'))
        <div class="alert alert-danger">
            <button data-dismiss="alert" class="close" type="button">&times;</button>
            {{ Session::get('error') }}
        </div>
    @endif

    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-bed"></i> Beds
                        <small>{{ $free }} free of {{ $beds->count() }}</small>
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/beds/categories') }}" class="btn btn-default btn-sm">Ward types</a>
                        <a href="{{ url('/beds/create') }}" class="btn btn-primary btn-sm">
                            <i class="glyphicon glyphicon-plus"></i> Add bed
                        </a>
                    </div>
                </div>
                <div class="box-content">

                    <form role="form" action="{{ url('/beds') }}" method="get" class="form-inline">
                        <div class="form-group">
                            <label>Ward type</label>
                            <select name="category" class="form-control">
                                <option value="">All wards</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ (string) $categoryId === (string) $category->id ? 'selected' : '' }}>
                                        {{ $category->category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-default">Show</button>
                    </form>

                    <br>

                    @if ($beds->isEmpty())
                        <p class="text-muted">
                            No beds yet.
                            @if ($categories->isEmpty())
                                Add a <a href="{{ url('/beds/categories') }}">ward type</a> first.
                            @else
                                <a href="{{ url('/beds/create') }}">Add one.</a>
                            @endif
                        </p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Ward</th>
                                    <th>Bed</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Occupant</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($beds as $bed)
                                    @php($admission = $occupants->get((string) $bed->id))
                                    <tr>
                                        <td>{{ optional($categories->firstWhere('id', $bed->category))->category ?? '-' }}</td>
                                        <td>{{ $bed->number }}</td>
                                        <td>{{ $bed->description }}</td>
                                        <td>
                                            @if ($admission)
                                                <span class="label label-warning">Occupied</span>
                                            @else
                                                <span class="label label-success">Free</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($admission)
                                                <a href="{{ url('/admissions/' . $admission->id) }}">
                                                    {{ $patients[$admission->patient] ?? 'Unknown patient' }}
                                                </a>
                                                <small class="text-muted">since {{ $admission->a_time }}</small>
                                            @else
                                                <a href="{{ url('/admissions/create') }}">Admit</a>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-default btn-sm" href="{{ url('/beds/' . $bed->id . '/edit') }}">
                                                <i class="glyphicon glyphicon-edit"></i>
                                            </a>
                                            @unless ($admission)
                                                <form action="{{ url('/beds/' . $bed->id) }}" method="post"
                                                      style="display:inline"
                                                      onsubmit="return confirm('Remove bed {{ $bed->number }}?');">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="glyphicon glyphicon-trash"></i>
                                                    </button>
                                                </form>
                                            @endunless
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
