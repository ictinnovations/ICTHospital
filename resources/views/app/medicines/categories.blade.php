{{--
  ICTHospital - medicine categories.

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
        <div class="box col-md-8">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-th-list"></i> Medicine categories</h2>
                    <div class="box-icon">
                        <a href="{{ url('/medicines') }}" class="btn btn-default btn-sm">Back to catalogue</a>
                    </div>
                </div>
                <div class="box-content">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($categories->isEmpty())
                        <p class="text-muted">No categories yet. Antibiotics, analgesics and antipyretics are a common start.</p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr><th>Category</th><th>Description</th><th>Medicines</th><th></th></tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td>{{ $category->category }}</td>
                                        <td>{{ $category->description }}</td>
                                        <td>{{ $counts[$category->id] ?? 0 }}</td>
                                        <td>
                                            <form action="{{ url('/medicines/categories/' . $category->id) }}" method="post"
                                                  onsubmit="return confirm('Remove this category?');">
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
                    @endif

                    <h4>Add a category</h4>
                    <form role="form" action="{{ url('/medicines/categories') }}" method="post" class="form-inline">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <input type="text" name="category" class="form-control" placeholder="Antibiotics" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="description" class="form-control" placeholder="Description">
                        </div>
                        <button type="submit" class="btn btn-primary">Add</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
