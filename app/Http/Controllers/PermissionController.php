<?php
/**
 * ICTHospital - role permissions.
 *
 * The catalogue and the role list come from config/hospital_permissions.php so the
 * controller and the checkbox grid can never drift out of order. The school
 * catalogue this replaces granted rights over students, classes, exams and fees.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PermissionController extends Controller
{
    /**
     * The roles that have a column in the grid, keyed by the value stored in
     * permission.permission_group.
     */
    protected function groups()
    {
        return config('hospital_permissions.groups', []);
    }

    /**
     * Every permission the application knows about, in a fixed order.
     */
    protected function fields()
    {
        return config('hospital_permissions.permissions', []);
    }

    /**
     * Show the grid. Which columns appear depends on the roles ticked on the
     * filter form above it.
     */
    public function index(Request $request)
    {
        $permissions = Permission::count() > 0 ? Permission::get() : [];

        // Which role columns the operator asked for.
        $selected = [];
        foreach (array_keys($this->groups()) as $group) {
            $selected[$group] = $request->input($group) ? 'yes' : '';
        }

        return view('app/permission', [
            'permissions' => $permissions,
            'permission_fields' => $this->fields(),
            'groups' => $this->groups(),
            'selected' => $selected,
        ]);
    }

    public function create()
    {
        //
    }

    /**
     * Rewrite the whole permission table from the submitted grid.
     *
     * The table is rebuilt rather than updated because the grid always posts every
     * permission for every role, and an unticked box arrives as an absent key.
     */
    public function store(Request $request)
    {
        $fields = $this->fields();

        DB::transaction(function () use ($request, $fields) {
            // delete(), not truncate(): TRUNCATE is DDL, so MariaDB commits the
            // open transaction underneath us and the wrapper then fails with
            // "There is no active transaction".
            DB::table('permission')->delete();

            $rows = [];

            foreach (array_keys($this->groups()) as $group) {
                $granted = (array) $request->input($group, []);

                foreach ($fields as $field) {
                    $name = str_replace(' ', '_', strtolower($field));

                    $rows[] = [
                        'permission_name' => $name,
                        'permission_group' => $group,
                        'permission_type' => array_key_exists($name, $granted) ? 'yes' : 'no',
                    ];
                }
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('permission')->insert($chunk);
            }
        });

        return Redirect::to('/permission')->with('success', 'Permissions saved.');
    }

    public function show(Permission $permission)
    {
        //
    }

    public function edit(Permission $permission)
    {
        //
    }

    public function update(Request $request, Permission $permission)
    {
        //
    }

    public function destroy(Permission $permission)
    {
        //
    }

    /**
     * The permissions granted to the signed in user's role.
     */
    public function get_permission_by_role()
    {
        $user = Auth::user();

        if (! $user || Permission::count() === 0) {
            return [];
        }

        return Permission::where('permission_group', strtolower($user->group))
            ->where('permission_type', 'yes')
            ->get();
    }
}
