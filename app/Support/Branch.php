<?php
/**
 * ICTHospital - the branch the signed in user is working at.
 *
 * A user tied to a branch always works there. A user with no branch (usually an
 * administrator) works across all branches, or picks one from the top bar for the
 * session. With no branches set up at all, everything behaves as a single site.
 *
 * Records written before branches existed have no branch and show everywhere.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Support;

use App\Models\HospitalBranch;
use Illuminate\Support\Facades\Schema;

class Branch
{
    /** The current branch id, or null for "all branches". */
    public static function current()
    {
        $user = auth()->user();
        if (! $user || ! Schema::hasTable('hospital_branch')) {
            return null;
        }
        if (! empty($user->branch_id)) {
            return (int) $user->branch_id;
        }
        $picked = session('branch_id');

        return $picked ? (int) $picked : null;
    }

    /** Can this user choose the branch, rather than being tied to one? */
    public static function canSwitch()
    {
        $user = auth()->user();

        return $user && empty($user->branch_id);
    }

    public static function name($id)
    {
        return $id ? HospitalBranch::whereKey($id)->value('name') : null;
    }

    /** Narrows a query to the current branch, keeping rows that predate branches. */
    public static function scope($query, $column = 'branch_id')
    {
        $id = self::current();
        if ($id === null) {
            return $query;
        }

        return $query->where(fn ($q) => $q->where($column, $id)->orWhereNull($column));
    }
}
