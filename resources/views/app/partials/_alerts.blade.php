{{--
  ICTHospital - flash messages and validation errors, shared by the newer screens.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
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
@if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>Check the form.</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
