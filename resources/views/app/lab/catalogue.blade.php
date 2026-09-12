{{--
  ICTHospital - laboratory test catalogue.

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
                    <h2><i class="glyphicon glyphicon-th-list"></i> Test catalogue</h2>
                    <div class="box-icon">
                        <a href="{{ url('/lab') }}" class="btn btn-default btn-sm">Back to laboratory</a>
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

                    @if ($catalogue->isEmpty())
                        <p class="text-muted">
                            No tests yet. The reference range is worth filling in, because it is copied onto
                            each request and is what makes a result readable later.
                        </p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Test</th>
                                    <th>Description</th>
                                    <th>Reference range</th>
                                    <th>Times requested</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($catalogue as $test)
                                    <tr>
                                        <td>{{ $test->category }}</td>
                                        <td>{{ $test->description }}</td>
                                        <td>{{ $test->reference_value }}</td>
                                        <td>{{ $usage[$test->id] ?? 0 }}</td>
                                        <td>
                                            <form action="{{ url('/lab/catalogue/' . $test->id) }}" method="post"
                                                  onsubmit="return confirm('Remove this test?');">
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

                    <h4>Add a test</h4>
                    <form role="form" action="{{ url('/lab/catalogue') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <input type="text" name="category" class="form-control"
                                       placeholder="Haemoglobin" required>
                            </div>
                            <div class="col-md-3 form-group">
                                <input type="text" name="description" class="form-control" placeholder="Description">
                            </div>
                            <div class="col-md-3 form-group">
                                <input type="text" name="reference_value" class="form-control"
                                       placeholder="13.5 to 17.5 g/dL">
                            </div>
                            <div class="col-md-2 form-group">
                                <input type="text" name="procedure_id" class="form-control" placeholder="Procedure code">
                            </div>
                            <div class="col-md-1 form-group">
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
