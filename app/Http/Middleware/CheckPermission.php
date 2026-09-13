<?php
/**
 * ICTHospital - route level permission check.
 *
 * Three things were wrong with the version this replaces. It read
 * Auth::user()->group without checking anyone was signed in, so an unauthenticated
 * request fatalled instead of redirecting to the login page. It redirected to
 * 'user-have-no-permission', a route that does not exist anywhere in the
 * application, so a denied user got a 404 with no explanation. And it ran its own
 * query per call, which on a screen behind several checks meant several identical
 * queries.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Middleware;

use App\Support\Permissions;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * @param  string  $permission  the stored key, for example patient_view
     */
    public function handle($request, Closure $next, $permission)
    {
        if (! Auth::check()) {
            return redirect('/');
        }

        if (Permissions::allows($permission)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'You do not have permission to do that.'], 403);
        }

        return redirect('/no-permission')->with('denied', $permission);
    }
}
