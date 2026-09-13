<?php
/**
 * ICTHospital - one place that answers "is this user allowed to do that".
 *
 * The middleware and the sidebar both need the answer, and before this they could
 * not share it: the middleware ran a query per request and the sidebar simply did
 * not ask, so the menu offered links that redirected the moment they were clicked.
 *
 * The grant table is read once per request and held in memory. A page with a dozen
 * gated menu entries on it would otherwise run a dozen identical queries.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Permissions
{
    /** Grants for the current user's role, keyed by permission name. Null until loaded. */
    private static $granted = null;

    /** The role the cache was built for, so a change of user inside one process is noticed. */
    private static $loadedFor = null;

    /**
     * Is the signed in user's role granted this permission?
     *
     * Returns false when nobody is signed in. It deliberately does not fall back
     * to allowing everything when the permission table is missing rows, because
     * that would turn a half finished install into an open one. The way out of an
     * empty table is `php artisan hospital:sync-permissions`, which grants the
     * whole catalogue to Admin.
     */
    public static function allows($permission)
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        $role = strtolower((string) $user->group);

        if (self::$granted === null || self::$loadedFor !== $role) {
            self::$granted = self::load($role);
            self::$loadedFor = $role;
        }

        return isset(self::$granted[$permission]);
    }

    /** Turns a catalogue label such as "Patient View" into the stored key. */
    public static function key($label)
    {
        return str_replace(' ', '_', strtolower($label));
    }

    /** Forget the cached grants. Used by the tests and after the grid is saved. */
    public static function flush()
    {
        self::$granted = null;
        self::$loadedFor = null;
    }

    private static function load($role)
    {
        if ($role === '' || ! Schema::hasTable('permission')) {
            return [];
        }

        return DB::table('permission')
            ->where('permission_group', $role)
            ->where('permission_type', 'yes')
            ->pluck('permission_name')
            ->flip()
            ->all();
    }
}
