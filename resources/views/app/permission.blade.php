{{--
  ICTHospital - role permission grid.

  The catalogue and the role columns both come from config/hospital_permissions.php
  by way of PermissionController, so nothing here has to be kept in step by hand.
  The version this replaces carried its own copy of the permission array and read
  the saved rows by fixed numeric offsets, which broke whenever the two lists
  drifted apart, and it posted the accountant column under the name "accutant" so
  those grants were silently discarded.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('content')
<link type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" />
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css" />

@if (Session::get('success'))
<div class="alert alert-success">
  <button data-dismiss="alert" class="close" type="button">&times;</button>
  <strong>Process Success.</strong> {{ Session::get('success') }}<br><br>
</div>
@endif

<?php
/*
 * Saved rows indexed by "group|permission_name" so a lookup never depends on the
 * order the rows came back in.
 */
$saved = [];
foreach ($permissions as $row) {
    $saved[$row->permission_group . '|' . $row->permission_name] = $row->permission_type;
}

$shown = array_filter($groups, function ($key) use ($selected) {
    return ($selected[$key] ?? '') === 'yes';
}, ARRAY_FILTER_USE_KEY);

/*
 * Section headings. The key is the permission label the section starts at, so
 * reordering the catalogue in config moves the headings with it.
 */
$sections = [
    'Patient View' => 'Patients',
    'Appointment View' => 'Appointments',
    'Admission View' => 'Admissions, Beds and Wards',
    'Prescription View' => 'Clinical',
    'Medicine View' => 'Pharmacy',
    'Doctor View' => 'Staff and Departments',
    'Payment View' => 'Billing and Accounts',
    'Send Sms/Voice' => 'Communication and Administration',
];
?>

<div class="row">
  <div class="box col-md-12">
    <div class="box-inner">
      <div data-original-title="" class="box-header well">
        <h2><i class="glyphicon glyphicon-th"></i>Permissions Setting</h2>
      </div>

      <div class="box-content">
        <div class="container">

          <form role="form">
            <div class="row">
              <div class="col-md-12">
                @foreach($groups as $key => $label)
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="control-label" for="group_{{ $key }}">{{ $label }}</label>
                    <div class="input-group">
                      <span class="input-group-addon"><i class="glyphicon glyphicon-user blue"></i></span>
                      <input type="checkbox" id="group_{{ $key }}" name="{{ $key }}" class="form-control"
                             style="margin-top: -35px;" @if(($selected[$key] ?? '') === 'yes') checked @endif>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <button class="btn btn-primary pull-right" type="submit">
                  <i class="glyphicon glyphicon-th"></i>Get List
                </button>
              </div>
            </div>
            <br>
          </form>

          @if(count($shown) === 0)
          <p class="text-muted">Tick one or more roles above and press Get List to edit their permissions.</p>
          @else

          <div id="user-permissions">
            <form role="form" action="{{ url('/permission/create') }}" method="post" enctype="multipart/form-data">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">

              <table style="width:100%" id="permission" class="table responsive table-bordered">
                <thead>
                  <tr>
                    <th>Permissions</th>
                    @foreach($shown as $key => $label)
                    <th>{{ $label }} <input type="checkbox" class="check-all" data-group="{{ $key }}"></th>
                    @endforeach
                  </tr>
                </thead>

                <tbody>
                  @foreach($permission_fields as $permission_field)
                  <?php $field_name = str_replace(' ', '_', strtolower($permission_field)); ?>

                  @if(isset($sections[$permission_field]))
                  <tr>
                    <td colspan="{{ count($shown) + 1 }}"><h4>{{ $sections[$permission_field] }}</h4></td>
                  </tr>
                  @endif

                  <tr>
                    <td width="50"><p>{{ $permission_field }}</p></td>

                    @foreach($shown as $key => $label)
                    <td width="50">
                      <div class="btn-group btn-toggle">
                        <input class="chb group-{{ $key }}" data-toggle="toggle"
                               id="{{ $key }}_{{ $field_name }}"
                               name="{{ $key }}[{{ $field_name }}]"
                               data-on="Yes" data-off="No" data-width="100"
                               data-onstyle="success" data-offstyle="danger" type="checkbox"
                               @if(($saved[$key . '|' . $field_name] ?? 'no') === 'yes') checked @endif>
                      </div>
                    </td>
                    @endforeach
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

            <div class="row">
              <div class="col-md-12">
                <button class="btn btn-primary pull-right" id="btnsave" type="submit">
                  <i class="glyphicon glyphicon-plus"></i>Save
                </button>
              </div>
            </div>
            </form>
          </div>
          @endif

          <div id="push"></div>
        </div>
      </div>
    </div>
  </div>
</div>
@stop

@section('script')
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>
<script>
$(document).ready(function () {
  // One handler covers every role column, however many there are.
  $('.check-all').on('change', function () {
    var group = $(this).data('group');
    $('input.group-' + group).prop('checked', $(this).prop('checked')).change();
  });

  $('input.chb').on('change', function () {
    var group = ($(this).attr('class').match(/group-(\S+)/) || [])[1];
    if (!group) {
      return;
    }
    var total = $('input.group-' + group).length;
    var checked = $('input.group-' + group + ':checked').length;
    $('.check-all[data-group="' + group + '"]').prop('checked', total > 0 && total === checked);
  });
});
</script>
@stop
