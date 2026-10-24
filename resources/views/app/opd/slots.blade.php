{{--
  ICTHospital - a doctor's weekly booking slots and blocked dates.

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
                <div class="box-header well">
                    <h2><i class="glyphicon glyphicon-time"></i> Booking slots: {{ $doctor->name }}</h2>
                    <div class="box-icon">
                        <form method="get" class="form-inline" style="display:inline" onsubmit="location='{{ url('/doctors') }}/'+this.d.value+'/slots';return false">
                            <select name="d" class="form-control input-sm" onchange="this.form.onsubmit()">@foreach ($doctors as $d)<option value="{{ $d->id }}" {{ $d->id === $doctor->id ? 'selected' : '' }}>{{ $d->name }}</option>@endforeach</select>
                        </form>
                    </div>
                </div>
                <div class="box-content">
                    <p class="help-block">Once a doctor has slots, bookings must fall inside them. A doctor with no slots can be booked at any time.</p>
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed" style="max-width:760px">
                        <thead><tr><th>Day</th><th>From</th><th>To</th><th>Slot length</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        @forelse ($slots as $s)
                            <tr class="{{ $s->active ? '' : 'text-muted' }}">
                                <td>{{ \App\Models\DoctorSlot::DAYS[$s->weekday] }}</td><td>{{ $s->start_time }}</td><td>{{ $s->end_time }}</td><td>{{ $s->slot_minutes }} min</td>
                                <td>{{ $s->active ? 'Active' : 'Disabled' }}</td>
                                <td class="text-nowrap">
                                    <form method="post" action="{{ url('/slots/' . $s->id . '/toggle') }}" style="display:inline">{{ csrf_field() }}<button class="btn btn-default btn-xs">{{ $s->active ? 'Disable slots' : 'Enable' }}</button></form>
                                    <form method="post" action="{{ url('/slots/' . $s->id) }}" style="display:inline">{{ csrf_field() }}<input type="hidden" name="_method" value="DELETE"><button class="btn btn-danger btn-xs">Delete slots</button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-muted">No slots yet: this doctor can be booked at any time.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                    </div>

                    <h4>Create slots</h4>
                    <form method="post" action="{{ url('/doctors/' . $doctor->id . '/slots') }}">
                        {{ csrf_field() }}
                        <div>@foreach (\App\Models\DoctorSlot::DAYS as $i => $day)<label class="checkbox-inline"><input type="checkbox" name="weekdays[]" value="{{ $i }}" {{ in_array($i, [1, 2, 3, 4, 5, 6]) ? 'checked' : '' }}> {{ $day }}</label>@endforeach</div>
                        <div class="row" style="margin-top:8px">
                            <div class="col-md-2 form-group"><label>From</label><input type="time" name="start_time" class="form-control" value="09:00" required></div>
                            <div class="col-md-2 form-group"><label>To</label><input type="time" name="end_time" class="form-control" value="13:00" required></div>
                            <div class="col-md-2 form-group"><label>Minutes each</label><input type="number" name="slot_minutes" class="form-control" value="15" min="5" max="240" required></div>
                            <div class="col-md-2 form-group"><label>&nbsp;</label><button class="btn btn-primary btn-block">Create slots</button></div>
                        </div>
                    </form>

                    <h4>Blocked dates <small>leave, a half day, a conference</small></h4>
                    <ul>
                        @forelse ($blocks as $b)
                            <li>{{ \Carbon\Carbon::parse($b->date)->format('D d M Y') }}: {{ $b->start_time ? $b->start_time . '-' . $b->end_time : 'whole day' }}{{ $b->reason ? ' (' . $b->reason . ')' : '' }}
                                <form method="post" action="{{ url('/blocks/' . $b->id) }}" style="display:inline">{{ csrf_field() }}<input type="hidden" name="_method" value="DELETE"><button class="btn btn-link btn-xs">remove</button></form></li>
                        @empty
                            <li class="text-muted">None coming up.</li>
                        @endforelse
                    </ul>
                    <form method="post" action="{{ url('/doctors/' . $doctor->id . '/blocks') }}" class="row">
                        {{ csrf_field() }}
                        <div class="col-md-2 form-group"><input type="date" name="date" class="form-control" required></div>
                        <div class="col-md-2 form-group"><input type="time" name="start_time" class="form-control" title="Leave blank for the whole day"></div>
                        <div class="col-md-2 form-group"><input type="time" name="end_time" class="form-control"></div>
                        <div class="col-md-4 form-group"><input name="reason" class="form-control" placeholder="Reason (optional)"></div>
                        <div class="col-md-2 form-group"><button class="btn btn-default btn-block">Block time</button></div>
                    </form>

                    <h4>Free today</h4>
                    <p>@forelse ($preview as $f)<span class="label label-default" style="margin-right:4px">{{ $f['start'] }}</span>@empty<span class="text-muted">Nothing free today.</span>@endforelse</p>
                </div>
            </div>
        </div>
    </div>
@stop
