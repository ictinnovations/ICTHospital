{{--
  ICTHospital - shown when a role is not granted the permission a screen needs.

  The middleware used to redirect to a route that did not exist, so the user got a
  404 and no idea why. This says which right is missing and who can grant it.

  Copyright (c) ICT Innovations <https://www.ictinnovations.com>
  Part of ICTHospital <https://www.icthospital.com>
  Licensed under the GNU General Public License v3.0.
--}}
@extends('layouts.master')
@section('style')
@stop
@section('content')

    <div class="row">
        <div class="box col-md-8">
            <div class="box-inner">
                <div data-original-title="" class="box-header well">
                    <h2><i class="glyphicon glyphicon-lock"></i> You do not have access to that</h2>
                </div>
                <div class="box-content">
                    <p>
                        Your role is <strong>{{ Auth::user() ? Auth::user()->group : 'not signed in' }}</strong>,
                        and it has not been granted the right this screen needs.
                    </p>

                    @if (Session::get('denied'))
                        <p class="text-muted">
                            Missing permission: <code>{{ Session::get('denied') }}</code>
                        </p>
                    @endif

                    <p>
                        An administrator can grant it on the
                        <a href="{{ url('/permission') }}">permissions screen</a>. If the grid there looks
                        empty or still lists rights from an older version, run
                        <code>php artisan hospital:sync-permissions</code> on the server first.
                    </p>

                    <a href="{{ url('/dashboard') }}" class="btn btn-primary">Back to the dashboard</a>
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
@stop
