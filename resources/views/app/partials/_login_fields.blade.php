{{--
  ICTHospital - sign-in fields shared by the doctor and staff forms.
  Expects $person (the record) and $roleLabel (e.g. "Doctor").

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@php($login = \App\Support\StaffLogin::userFor($person->exists ? $person : null))
<fieldset style="margin:10px 0 20px;padding-top:10px;border-top:1px solid #eee">
    <legend style="font-size:16px;border:0;margin-bottom:6px">Login</legend>
    @if ($login)
        <p class="help-block">Signs in as <strong>{{ $login->login }}</strong> with the {{ $login->group }} permissions. Fill in either field below only to change it.</p>
    @else
        <p class="help-block">Optional. Give this {{ strtolower($roleLabel) }} a username and password so they can sign in. They get the {{ $roleLabel }} permissions set under Users and Roles, Permissions.</p>
    @endif
    <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('login_username') ? 'has-error' : '' }}">
                <label>{{ $login ? 'Change username' : 'Username' }}</label>
                <input type="text" name="login_username" class="form-control" maxlength="20" autocomplete="off"
                       value="{{ old('login_username') }}" placeholder="{{ $login ? $login->login : 'e.g. dr.sana' }}">
                @if ($errors->has('login_username'))<span class="help-block">{{ $errors->first('login_username') }}</span>@endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('login_password') ? 'has-error' : '' }}">
                <label>{{ $login ? 'New password' : 'Password' }}</label>
                <input type="password" name="login_password" class="form-control" minlength="8" maxlength="64" autocomplete="new-password">
                @if ($errors->has('login_password'))<span class="help-block">{{ $errors->first('login_password') }}</span>@else<span class="help-block">At least 8 characters.</span>@endif
            </div>
        </div>
    </div>
</fieldset>
