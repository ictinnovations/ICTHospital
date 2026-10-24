{{--
  ICTHospital - branch switcher in the top bar, for users not tied to one branch.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@php($branches = \Illuminate\Support\Facades\Schema::hasTable('hospital_branch') ? \App\Models\HospitalBranch::where('active', true)->orderBy('name')->get(['id', 'name']) : collect())
@if ($branches->isNotEmpty() && auth()->check())
    <div class="header-button-item" style="margin-left:10px">
        @if (\App\Support\Branch::canSwitch())
            <form method="post" action="{{ url('/branch/switch') }}" style="margin:0">
                {{ csrf_field() }}
                <select name="branch_id" class="form-control input-sm" onchange="this.form.submit()" title="Branch" style="min-width:150px">
                    <option value="">All branches</option>
                    @foreach ($branches as $b)<option value="{{ $b->id }}" {{ (int) \App\Support\Branch::current() === $b->id ? 'selected' : '' }}>{{ $b->name }}</option>@endforeach
                </select>
            </form>
        @else
            <span class="label label-info" style="font-size:12px"><i class="glyphicon glyphicon-map-marker"></i> {{ \App\Support\Branch::name(\App\Support\Branch::current()) }}</span>
        @endif
    </div>
@endif
