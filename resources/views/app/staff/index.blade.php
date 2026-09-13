{{--
  ICTHospital - one list for every staff type.

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
                    <h2><i class="glyphicon {{ $meta['icon'] }}"></i> {{ $meta['label'] }}</h2>
                    <div class="box-icon">
                        <a href="{{ url('/staff/' . $type . '/create') }}" class="btn btn-primary btn-sm">
                            <i class="glyphicon glyphicon-plus"></i> Add {{ $meta['singular'] }}
                        </a>
                    </div>
                </div>
                <div class="box-content">

                    <form role="form" action="{{ url('/staff/' . $type) }}" method="get" class="form-inline">
                        <div class="form-group">
                            <input type="text" name="q" class="form-control" style="min-width:280px"
                                   value="{{ $term }}" placeholder="Name, phone or email">
                        </div>
                        <button type="submit" class="btn btn-default">Search</button>
                        @if ($term !== '')
                            <a href="{{ url('/staff/' . $type) }}" class="btn btn-link">Clear</a>
                        @endif
                    </form>

                    <br>

                    @if ($staff->total() === 0)
                        <p class="text-muted">
                            @if ($term !== '')
                                Nothing matched "{{ $term }}".
                            @else
                                No {{ strtolower($meta['label']) }} on file yet.
                            @endif
                        </p>
                    @else
                        <table class="table table-striped table-bordered bootstrap-datatable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th style="width:12%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($staff as $person)
                                    <tr>
                                        <td>{{ $person->name }}</td>
                                        <td>{{ $person->phone }}</td>
                                        <td>{{ $person->email }}</td>
                                        <td>{{ $person->address }}</td>
                                        <td>
                                            <a class="btn btn-default btn-sm"
                                               href="{{ url('/staff/' . $type . '/' . $person->id . '/edit') }}">
                                                <i class="glyphicon glyphicon-edit"></i>
                                            </a>
                                            <form action="{{ url('/staff/' . $type . '/' . $person->id) }}" method="post"
                                                  style="display:inline"
                                                  onsubmit="return confirm('Remove {{ $person->name }}?');">
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

                        <p class="text-muted">{{ $staff->total() }} on file.</p>
                        {!! $staff->links() !!}
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
