{{--
  ICTHospital - payment gateway credentials.

  Secrets are never rendered back. Each one shows as set or not set, and leaving a
  field blank on save keeps whatever is stored.

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
                    <h2><i class="glyphicon glyphicon-credit-card"></i> Payment gateways</h2>
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

                    <div class="alert alert-info">
                        Nothing in this release charges a card. These settings are stored ready for the
                        online payment work that is still to come, so you can put your credentials in now
                        and they will be there when it lands. Invoices and pharmacy sales are recorded as
                        paid by whatever method you type on the payment form.
                    </div>

                    @if ($gateways->isEmpty())
                        <p class="text-muted">No gateways configured.</p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:22%">Gateway</th>
                                    <th style="width:38%">Credentials on file</th>
                                    <th style="width:15%">Status</th>
                                    <th style="width:25%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gateways as $gateway)
                                    <tr>
                                        <td>{{ $gateway->name }}</td>
                                        <td>
                                            @foreach ($secrets as $field)
                                                <span class="label label-{{ $gateway->$field ? 'success' : 'default' }}">
                                                    {{ $field }}: {{ $gateway->$field ? 'set' : 'not set' }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if ($gateway->status === 'enabled')
                                                <span class="label label-success">Active</span>
                                            @else
                                                <span class="label label-default">Disabled</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($gateway->status === 'enabled')
                                                <form action="{{ url('/gateways/' . $gateway->id . '/disable') }}"
                                                      method="post" style="display:inline">
                                                    {{ csrf_field() }}
                                                    <button type="submit" class="btn btn-default btn-sm">Disable</button>
                                                </form>
                                            @else
                                                <form action="{{ url('/gateways/' . $gateway->id . '/enable') }}"
                                                      method="post" style="display:inline">
                                                    {{ csrf_field() }}
                                                    <button type="submit" class="btn btn-primary btn-sm">Make active</button>
                                                </form>
                                            @endif
                                            <a class="btn btn-default btn-sm"
                                               href="#edit-{{ $gateway->id }}" data-toggle="collapse">Edit</a>
                                            <form action="{{ url('/gateways/' . $gateway->id) }}" method="post"
                                                  style="display:inline"
                                                  onsubmit="return confirm('Remove {{ $gateway->name }}?');">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="glyphicon glyphicon-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" style="padding:0;border-top:0">
                                            <div class="collapse" id="edit-{{ $gateway->id }}" style="padding:15px">
                                                <form action="{{ url('/gateways/' . $gateway->id) }}" method="post">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="_method" value="PUT">
                                                    <div class="row">
                                                        <div class="col-md-4 form-group">
                                                            <label>Name</label>
                                                            <input type="text" name="name" class="form-control"
                                                                   value="{{ $gateway->name }}" required>
                                                        </div>
                                                        @foreach ($secrets as $field)
                                                            <div class="col-md-4 form-group">
                                                                <label>{{ $field }}</label>
                                                                <input type="password" name="{{ $field }}" class="form-control"
                                                                       autocomplete="new-password"
                                                                       placeholder="{{ $gateway->$field ? 'Stored. Type to replace.' : 'Not set' }}">
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Save</button>
                                                    <span class="help-block">
                                                        Leave a credential blank to keep the one already stored.
                                                    </span>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                    <h4>Add a gateway</h4>
                    <form role="form" action="{{ url('/gateways') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" placeholder="PayPal" required>
                            </div>
                            @foreach ($secrets as $field)
                                <div class="col-md-4 form-group">
                                    <label>{{ $field }}</label>
                                    <input type="password" name="{{ $field }}" class="form-control"
                                           autocomplete="new-password">
                                </div>
                            @endforeach
                        </div>
                        <button type="submit" class="btn btn-primary">Add gateway</button>
                        <span class="help-block">
                            A new gateway starts disabled. Make it active when you are ready to use it.
                        </span>
                    </form>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
