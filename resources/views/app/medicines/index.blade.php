{{--
  ICTHospital - medicine catalogue.

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
                    <h2><i class="glyphicon glyphicon-plus-sign"></i> Medicines</h2>
                    <div class="box-icon">
                        <a href="{{ url('/medicines/categories') }}" class="btn btn-default btn-sm">Categories</a>
                        <a href="{{ url('/medicines/create') }}" class="btn btn-primary btn-sm">
                            <i class="glyphicon glyphicon-plus"></i> Add medicine
                        </a>
                    </div>
                </div>
                <div class="box-content">

                    <form role="form" action="{{ url('/medicines') }}" method="get" class="form-inline">
                        <div class="form-group">
                            <input type="text" name="q" class="form-control" style="min-width:260px"
                                   value="{{ $term }}" placeholder="Brand, generic or company">
                        </div>
                        <div class="form-group">
                            <select name="category" class="form-control">
                                <option value="">All categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ (string) $categoryId === (string) $category->id ? 'selected' : '' }}>
                                        {{ $category->category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-default">Search</button>
                    </form>

                    <br>

                    @if ($medicines->total() === 0)
                        <p class="text-muted">Nothing in the catalogue yet.</p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Generic</th>
                                    <th>Company</th>
                                    <th>Pack</th>
                                    <th>In stock</th>
                                    <th>Expires</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($medicines as $medicine)
                                    <tr>
                                        <td>{{ $medicine->name }}</td>
                                        <td>{{ $medicine->generic }}</td>
                                        <td>{{ $medicine->company }}</td>
                                        <td>{{ $medicine->box }}</td>
                                        <td>
                                            @if ((int) $medicine->quantity === 0)
                                                <span class="label label-danger">Out of stock</span>
                                            @else
                                                {{ $medicine->quantity }}
                                            @endif
                                        </td>
                                        <td>{{ $medicine->e_date }}</td>
                                        <td>
                                            <a class="btn btn-default btn-sm" href="{{ url('/medicines/' . $medicine->id . '/edit') }}">
                                                <i class="glyphicon glyphicon-edit"></i>
                                            </a>
                                            <form action="{{ url('/medicines/' . $medicine->id) }}" method="post"
                                                  style="display:inline"
                                                  onsubmit="return confirm('Remove {{ $medicine->name }} from the catalogue?');">
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
                        <p class="text-muted">{{ $medicines->total() }} medicine(s).</p>
                        {!! $medicines->links() !!}
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
