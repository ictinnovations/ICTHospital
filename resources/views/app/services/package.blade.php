{{--
  ICTHospital - components of a service package.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('content')
    @include('app.partials._alerts')
    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div class="box-header well"><h2><i class="glyphicon glyphicon-gift"></i> Package: {{ $package->category }}</h2>
                    <div class="box-icon"><a href="{{ url('/services') }}" class="btn btn-default btn-sm">Services</a></div></div>
                <div class="box-content">
                    <p>Package price: <strong>{{ number_format((float) $package->c_price, 2) }}</strong> (list). Components at list price: {{ number_format($componentValue, 2) }}.
                        Patient type prices are set under <a href="{{ url('/pricing') }}">Pricing</a>.</p>
                    <table class="table table-striped table-bordered table-condensed" style="max-width:720px">
                        <thead><tr><th>Included service</th><th class="text-right">Quantity</th><th class="text-right">List price each</th><th></th></tr></thead>
                        <tbody>
                        @forelse ($items as $i)
                            <tr><td>{{ optional($names->get($i->component_id))->category }}</td><td class="text-right">{{ $i->quantity }}</td>
                                <td class="text-right">{{ number_format((float) optional($names->get($i->component_id))->c_price, 2) }}</td>
                                <td><form method="post" action="{{ url('/package-items/' . $i->id) }}">{{ csrf_field() }}<input type="hidden" name="_method" value="DELETE"><button class="btn btn-danger btn-xs">Remove</button></form></td></tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">Nothing in this package yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    <form method="post" action="{{ url('/services/' . $package->id . '/package') }}" class="form-inline">
                        {{ csrf_field() }}
                        <select name="component_id" class="form-control" required><option value="">Add a service</option>@foreach ($services as $s)<option value="{{ $s->id }}">{{ $s->category }}</option>@endforeach</select>
                        <input type="number" name="quantity" class="form-control" value="1" min="1" style="width:80px">
                        <button class="btn btn-primary">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
