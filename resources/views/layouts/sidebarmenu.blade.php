{{--
  ICTHospital - main navigation.

  Every entry here points at a route that exists. The menu this replaces was the
  ICTSchool one: students, teachers, classes, sections, exams, marks, GPA rules,
  promotion, timetables, question papers, academic years and family fee vouchers.
  Those controllers and routes were removed during the hospital rebuild, so every
  one of those links was a dead end.

  Hospital modules still being built (patients, appointments, wards, pharmacy,
  laboratory) will be added here as their controllers land.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
<?php
$here = request()->path();

/**
 * Hide what the signed in role cannot open.
 *
 * Without this the menu lists every clinical screen to everyone and a role that
 * is missing the permission finds out by being redirected, which reads as a
 * broken link rather than as a deliberate restriction.
 */
$may = function ($permission) {
    return \App\Support\Permissions::allows($permission);
};

/** Mark the open branch so the right group starts expanded. */
$active = function ($prefixes) use ($here) {
    foreach ((array) $prefixes as $p) {
        if ($here === $p || strpos($here, $p . '/') === 0) {
            return true;
        }
    }
    return false;
};
?>

<div class="sidebar-nav nav-canvas-thumb">
  <ul class="nav nav-pills nav-stacked main-menu">

    <li class="nav-header">Main</li>

    <li>
      <a class="{{ $active('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
        <i class="glyphicon glyphicon-home"></i><span class="hidden-tablet"> Dashboard</span>
      </a>
    </li>

    <li class="nav-header">Clinical</li>

    @if ($may('patient_view'))
    <li>
      <a class="{{ $active('patients') ? 'active' : '' }}" href="{{ url('/patients') }}">
        <i class="glyphicon glyphicon-user"></i><span class="hidden-tablet"> Patients</span>
      </a>
    </li>
    @endif
    @if ($may('doctor_view'))
    <li>
      <a class="{{ $active('doctors') ? 'active' : '' }}" href="{{ url('/doctors') }}">
        <i class="glyphicon glyphicon-briefcase"></i><span class="hidden-tablet"> Doctors</span>
      </a>
    </li>
    @endif
    @if ($may('appointment_view'))
    <li>
      <a class="{{ $active('appointments') ? 'active' : '' }}" href="{{ url('/appointments') }}">
        <i class="glyphicon glyphicon-calendar"></i><span class="hidden-tablet"> Appointments</span>
      </a>
    </li>
    @endif
    @if ($may('admission_view'))
    <li>
      <a class="{{ $active('admissions') ? 'active' : '' }}" href="{{ url('/admissions') }}">
        <i class="glyphicon glyphicon-log-in"></i><span class="hidden-tablet"> Admissions</span>
      </a>
    </li>
    @endif
    @if ($may('bed_view'))
    <li>
      <a class="{{ $active('beds') ? 'active' : '' }}" href="{{ url('/beds') }}">
        <i class="glyphicon glyphicon-th"></i><span class="hidden-tablet"> Beds</span>
      </a>
    </li>
    @endif
    @if ($may('prescription_view'))
    <li>
      <a class="{{ $active('prescriptions') ? 'active' : '' }}" href="{{ url('/prescriptions') }}">
        <i class="glyphicon glyphicon-list-alt"></i><span class="hidden-tablet"> Prescriptions</span>
      </a>
    </li>
    @endif
    @if ($may('medicine_view'))
    <li>
      <a class="{{ $active('medicines') ? 'active' : '' }}" href="{{ url('/medicines') }}">
        <i class="glyphicon glyphicon-plus-sign"></i><span class="hidden-tablet"> Medicines</span>
      </a>
    </li>
    @endif
    @if ($may('lab_test_view'))
    <li>
      <a class="{{ $active('lab') ? 'active' : '' }}" href="{{ url('/lab') }}">
        <i class="glyphicon glyphicon-tint"></i><span class="hidden-tablet"> Laboratory</span>
      </a>
    </li>
    @endif
    @if ($may('pharmacy_stock_view'))
    <li>
      <a class="{{ $active('pharmacy') ? 'active' : '' }}" href="{{ url('/pharmacy') }}">
        <i class="glyphicon glyphicon-shopping-cart"></i><span class="hidden-tablet"> Pharmacy</span>
      </a>
    </li>
    @endif
    @if ($may('invoice_view'))
    <li>
      <a class="{{ $active('invoices') ? 'active' : '' }}" href="{{ url('/invoices') }}">
        <i class="glyphicon glyphicon-usd"></i><span class="hidden-tablet"> Invoices</span>
      </a>
    </li>
    @endif
    @if ($may('service_view'))
    <li>
      <a class="{{ $active('services') ? 'active' : '' }}" href="{{ url('/services') }}">
        <i class="glyphicon glyphicon-tags"></i><span class="hidden-tablet"> Services and Prices</span>
      </a>
    </li>
    @endif
    <li>
      <a class="{{ $active('search') ? 'active' : '' }}" href="{{ url('/search') }}">
        <i class="glyphicon glyphicon-search"></i><span class="hidden-tablet"> Search Patients</span>
      </a>
    </li>

    <li>
      <a class="{{ $active('reports') ? 'active' : '' }}" href="{{ url('/reports') }}">
        <i class="glyphicon glyphicon-stats"></i><span class="hidden-tablet"> Reports</span>
      </a>
    </li>

    <li class="nav-header">People</li>

    @if ($may('department_view'))
    <li>
      <a class="{{ $active('departments') ? 'active' : '' }}" href="{{ url('/departments') }}">
        <i class="glyphicon glyphicon-tower"></i><span class="hidden-tablet"> Departments</span>
      </a>
    </li>
    @endif
    @if ($may('nurse_view'))
    <li>
      <a class="{{ $active('staff/nurse') ? 'active' : '' }}" href="{{ url('/staff/nurse') }}">
        <i class="glyphicon glyphicon-heart"></i><span class="hidden-tablet"> Nurses</span>
      </a>
    </li>
    @endif
    @if ($may('staff_view'))
    <li>
      <a class="dropmenu {{ $active('staff') ? 'active' : '' }}" href="#">
        <i class="glyphicon glyphicon-user"></i><span class="hidden-tablet"> Other Staff</span>
        <span class="pull-right"><i class="glyphicon glyphicon-chevron-down"></i></span>
      </a>
      <ul style="{{ $active('staff') ? '' : 'display:none;' }}">
        <li><a href="{{ url('/staff/pharmacist') }}"><i class="glyphicon glyphicon-shopping-cart"></i> Pharmacists</a></li>
        <li><a href="{{ url('/staff/laboratorist') }}"><i class="glyphicon glyphicon-tint"></i> Laboratory Staff</a></li>
        <li><a href="{{ url('/staff/receptionist') }}"><i class="glyphicon glyphicon-bell"></i> Receptionists</a></li>
        <li><a href="{{ url('/staff/accountant') }}"><i class="glyphicon glyphicon-usd"></i> Accountants</a></li>
      </ul>
    </li>
    @endif

    <li class="nav-header">Money</li>

    <li>
      <a class="dropmenu {{ $active('accounting') ? 'active' : '' }}" href="#">
        <i class="glyphicon glyphicon-usd"></i><span class="hidden-tablet"> Accounting</span>
        <span class="pull-right"><i class="glyphicon glyphicon-chevron-down"></i></span>
      </a>
      <ul style="{{ $active('accounting') ? '' : 'display:none;' }}">
        <li><a href="{{ url('/accounting/income') }}"><i class="glyphicon glyphicon-plus"></i> Add Income</a></li>
        <li><a href="{{ url('/accounting/incomelist') }}"><i class="glyphicon glyphicon-list"></i> Income List</a></li>
        <li><a href="{{ url('/accounting/expence') }}"><i class="glyphicon glyphicon-minus"></i> Add Expense</a></li>
        <li><a href="{{ url('/accounting/expencelist') }}"><i class="glyphicon glyphicon-list"></i> Expense List</a></li>
        <li><a href="{{ url('/accounting/sectors') }}"><i class="glyphicon glyphicon-tags"></i> Sectors</a></li>
        <li><a href="{{ url('/accounting/report') }}"><i class="glyphicon glyphicon-stats"></i> Report</a></li>
        <li><a href="{{ url('/accounting/reportsum') }}"><i class="glyphicon glyphicon-stats"></i> Summary Report</a></li>
      </ul>
    </li>

    <li class="nav-header">Communication</li>

    <li>
      <a class="dropmenu {{ $active(['message', 'template', 'smslog', 'notification_type']) ? 'active' : '' }}" href="#">
        <i class="glyphicon glyphicon-envelope"></i><span class="hidden-tablet"> Messaging</span>
        <span class="pull-right"><i class="glyphicon glyphicon-chevron-down"></i></span>
      </a>
      <ul style="{{ $active(['message', 'template', 'smslog', 'notification_type']) ? '' : 'display:none;' }}">
        <li><a href="{{ url('/message') }}"><i class="glyphicon glyphicon-send"></i> Send Message</a></li>
        <li><a href="{{ url('/template/create') }}"><i class="glyphicon glyphicon-plus"></i> Add Template</a></li>
        <li><a href="{{ url('/template/list') }}"><i class="glyphicon glyphicon-list"></i> Templates</a></li>
        <li><a href="{{ url('/smslog') }}"><i class="glyphicon glyphicon-list-alt"></i> SMS and Voice Log</a></li>
        <li><a href="{{ url('/notification_type') }}"><i class="glyphicon glyphicon-bell"></i> Notification Types</a></li>
      </ul>
    </li>

    <li>
      <a class="dropmenu {{ $active('ictcore') ? 'active' : '' }}" href="#">
        <i class="glyphicon glyphicon-phone-alt"></i><span class="hidden-tablet"> ICTCore</span>
        <span class="pull-right"><i class="glyphicon glyphicon-chevron-down"></i></span>
      </a>
      <ul style="{{ $active('ictcore') ? '' : 'display:none;' }}">
        <li><a href="{{ url('/ictcore') }}"><i class="glyphicon glyphicon-cog"></i> Integration</a></li>
        <li><a href="{{ url('/ictcore/attendance') }}"><i class="glyphicon glyphicon-calendar"></i> Appointment Reminders</a></li>
        <li><a href="{{ url('/ictcore/fees') }}"><i class="glyphicon glyphicon-credit-card"></i> Payment Reminders</a></li>
      </ul>
    </li>

    <li class="nav-header">Administration</li>

    <li>
      <a class="dropmenu {{ $active(['users', 'permission', 'useredit', 'verification_code', 'verify_code']) ? 'active' : '' }}" href="#">
        <i class="glyphicon glyphicon-user"></i><span class="hidden-tablet"> Users and Roles</span>
        <span class="pull-right"><i class="glyphicon glyphicon-chevron-down"></i></span>
      </a>
      <ul style="{{ $active(['users', 'permission']) ? '' : 'display:none;' }}">
        <li><a href="{{ url('/users') }}"><i class="glyphicon glyphicon-list"></i> Users</a></li>
        <li><a href="{{ url('/permission') }}"><i class="glyphicon glyphicon-lock"></i> Permissions</a></li>
      </ul>
    </li>

    <li>
      <a class="dropmenu {{ $active(['settings', 'institute', 'branches', 'schedule', 'barcode']) ? 'active' : '' }}" href="#">
        <i class="glyphicon glyphicon-cog"></i><span class="hidden-tablet"> Settings</span>
        <span class="pull-right"><i class="glyphicon glyphicon-chevron-down"></i></span>
      </a>
      <ul style="{{ $active(['settings', 'institute', 'branches', 'schedule', 'barcode']) ? '' : 'display:none;' }}">
        <li><a href="{{ url('/institute') }}"><i class="glyphicon glyphicon-tower"></i> Hospital Information</a></li>
        <li><a href="{{ url('/branches') }}"><i class="glyphicon glyphicon-map-marker"></i> Branches</a></li>
        <li><a href="{{ url('/settings') }}"><i class="glyphicon glyphicon-wrench"></i> General Settings</a></li>
        <li><a href="{{ url('/schedule') }}"><i class="glyphicon glyphicon-time"></i> Schedule</a></li>
        <li><a href="{{ url('/barcode') }}"><i class="glyphicon glyphicon-barcode"></i> Barcode</a></li>
      </ul>
    </li>

    <li>
      <a class="{{ $active('activity') ? 'active' : '' }}" href="{{ url('/activity') }}">
        <i class="glyphicon glyphicon-list-alt"></i><span class="hidden-tablet"> Activity Log</span>
      </a>
    </li>

    <li>
      <a href="{{ url('/users/logout') }}">
        <i class="glyphicon glyphicon-off"></i><span class="hidden-tablet"> Sign out</span>
      </a>
    </li>

  </ul>
</div>
