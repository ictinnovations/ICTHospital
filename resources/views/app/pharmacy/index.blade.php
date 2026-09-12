{{--
  ICTHospital - pharmacy sales.

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
                    <h2><i class="glyphicon glyphicon-shopping-cart"></i> Pharmacy
                        <small>{{ number_format($takings, 2) }} taken in this range</small>
                    </h2>
                    <div class="box-icon">
                        <a href="{{ url('/medicines') }}" class="btn btn-default btn-sm">Stock</a>
                        <a href="{{ url('/pharmacy/create') }}" class="btn btn-primary btn-sm">
                            <i class="glyphicon glyphicon-plus"></i> Dispense
                        </a>
                    </div>
                </div>
                <div class="box-content">

                    <form role="form" action="{{ url('/pharmacy') }}" method="get" class="form-inline">
                        <div class="form-group">
                            <label>From</label>
                            <input type="date" name="from" class="form-control" value="{{ $from }}">
                        </div>
                        <div class="form-group">
                            <label>To</label>
                            <input type="date" name="to" class="form-control" value="{{ $to }}">
                        </div>
                        <button type="submit" class="btn btn-default">Show</button>
                    </form>

                    <br>

                    @if ($sales->total() === 0)
                        <p class="text-muted">No sales in this range.</p>
                    @else
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sales as $sale)
                                    <tr>
                                        <td>{{ $sale->date }}</td>
                                        <td>
                                            @if ($sale->patient)
                                                <a href="{{ url('/patients/' . $sale->patient) }}">
                                                    {{ $patients[$sale->patient] ?? 'Unknown' }}
                                                </a>
                                            @else
                                                <span class="text-muted">Counter sale</span>
                                            @endif
                                        </td>
                                        <td>
                                            @foreach ($sale->items as $item)
                                                <span class="label label-default">{{ $item->summary() }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ number_format((float) $sale->gross_total, 2) }}</td>
                                        <td>
                                            <a class="btn btn-default btn-sm" href="{{ url('/pharmacy/' . $sale->id) }}">
                                                <i class="glyphicon glyphicon-eye-open"></i> Receipt
                                            </a>
                                            <form action="{{ url('/pharmacy/' . $sale->id) }}" method="post"
                                                  style="display:inline"
                                                  onsubmit="return confirm('Reverse this sale and put the stock back?');">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-danger btn-sm">Reverse</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="text-muted">{{ $sales->total() }} sale(s).</p>
                        {!! $sales->links() !!}
                    @endif

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
