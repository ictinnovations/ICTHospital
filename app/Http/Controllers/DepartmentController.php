<?php
/**
 * ICTHospital - hospital departments.
 *
 * The department table came across from the CodeIgniter system with no screen, so the
 * doctor form had to fall back to free text with a datalist of whatever had already
 * been typed. That keeps spelling roughly consistent and nothing more: rename a
 * department and every doctor still carries the old string.
 *
 * This fills the list properly. The doctor record still stores the department as text
 * rather than a foreign key, because the legacy column is a varchar and every report
 * and export in the wild reads it that way, so renaming here offers to carry the
 * doctors across instead of silently orphaning them.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DepartmentController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $departments = Department::orderBy('name')->get();

        return view('app.departments.index', [
            'departments' => $departments,
            // How many doctors carry each name, so the person renaming one can see
            // what it is about to affect.
            'usage' => Doctor::selectRaw('department, count(*) as n')
                ->whereNotNull('department')->where('department', '<>', '')
                ->groupBy('department')->pluck('n', 'department'),
            // Departments doctors refer to that are not on the list yet, which is
            // every one of them on an install that has been using the free text box.
            'unlisted' => Doctor::whereNotNull('department')->where('department', '<>', '')
                ->distinct()->pluck('department')
                ->diff($departments->pluck('name'))
                ->values(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Department::create($data);

        return redirect('/departments')->with('success', $data['name'] . ' added.');
    }

    /**
     * Renaming carries the doctors with it.
     *
     * The doctor column holds the department name as text, so without this a rename
     * leaves every doctor pointing at a name that no longer exists on the list. Both
     * writes go in one transaction so a failure cannot leave them half renamed.
     */
    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);
        $data = $this->validated($request, $department->id);
        $oldName = $department->name;

        $moved = DB::transaction(function () use ($department, $data, $oldName) {
            $department->update($data);

            if ($oldName !== $data['name']) {
                return Doctor::where('department', $oldName)->update(['department' => $data['name']]);
            }

            return 0;
        });

        $message = $data['name'] . ' updated.';
        if ($moved > 0) {
            $message .= ' ' . $moved . ' doctor record(s) moved with it.';
        }

        return redirect('/departments')->with('success', $message);
    }

    /**
     * A department with doctors in it is not deleted.
     *
     * Same reasoning as the lab and service catalogues: the rows that reference it
     * would keep a name that is no longer on any list, which is how the free text
     * situation this screen replaces came about.
     */
    public function destroy($id)
    {
        $department = Department::findOrFail($id);
        $count = Doctor::where('department', $department->name)->count();

        if ($count > 0) {
            return redirect('/departments')->with(
                'error',
                $department->name . ' has ' . $count . ' doctor(s) in it. Move them first, or rename it instead.'
            );
        }

        $name = $department->name;
        $department->delete();

        return redirect('/departments')->with('success', $name . ' removed.');
    }

    /** Adds every department name the doctors already use but the list does not have. */
    public function adopt()
    {
        $existing = Department::pluck('name');
        $missing = Doctor::whereNotNull('department')->where('department', '<>', '')
            ->distinct()->pluck('department')->diff($existing);

        foreach ($missing as $name) {
            Department::create(['name' => $name, 'description' => '', 'x' => '', 'y' => '']);
        }

        return redirect('/departments')->with(
            'success',
            $missing->count() === 0
                ? 'Nothing to adopt, every department in use is already on the list.'
                : 'Added ' . $missing->count() . ' department(s) already in use by doctors.'
        );
    }

    private function validated(Request $request, $ignoreId = null)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $clash = Department::where('name', $data['name'])
            ->when($ignoreId, fn ($q) => $q->where('id', '<>', $ignoreId))
            ->exists();

        if ($clash) {
            throw ValidationException::withMessages([
                'name' => 'That department is already on the list.',
            ]);
        }

        // Legacy NOT NULL filler columns, unused layout leftovers from the old system.
        return [
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'x' => '',
            'y' => '',
        ];
    }
}
