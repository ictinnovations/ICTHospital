{{--
  ICTHospital - the report menu.

  Each card is hidden unless the signed in role can open what it points at, the
  same rule the sidebar uses. A menu of links that bounce reads as a broken
  application rather than as a restriction.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

@php($may = fn ($p) => \App\Support\Permissions::allows($p))

    <div class="row">
        <div class="box col-md-12">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-stats"></i> Reports</h2>
                </div>
                <div class="box-content">
                    <p class="text-muted">
                        Every figure below is worked out when you open the report. Nothing is stored as a
                        running total, so none of it can drift out of step with the records underneath.
                    </p>

                    <div class="row">
                        @if ($may('lab_test_view'))
                            <div class="col-md-6">
                                <h4><a href="{{ url('/reports/lab') }}">Outstanding lab work</a></h4>
                                <p class="text-muted">
                                    Tests requested and not yet reported, oldest first. A request with the
                                    bloods back and the culture still growing shows only the culture.
                                </p>
                            </div>
                        @endif

                        @if ($may('view_pharmacy_reports'))
                            <div class="col-md-6">
                                <h4><a href="{{ url('/reports/drug-usage') }}">Drug usage</a></h4>
                                <p class="text-muted">
                                    What was prescribed against what was actually dispensed, over a date
                                    range. The gap between the two columns is the part worth reading.
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        @if ($may('view_payment_reports'))
                            <div class="col-md-6">
                                <h4><a href="{{ url('/reports/debtors') }}">Debtors</a></h4>
                                <p class="text-muted">
                                    Who owes money and for how long, aged into the usual buckets. Balances
                                    come from the payment rows, so a part paid invoice shows its remainder.
                                </p>
                            </div>
                        @endif

                        @if ($may('bed_view'))
                            <div class="col-md-6">
                                <h4><a href="{{ url('/reports/occupancy') }}">Bed occupancy</a></h4>
                                <p class="text-muted">
                                    Occupancy per day, average length of stay and the split by ward.
                                    Counted from admissions rather than the bed status column.
                                </p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
