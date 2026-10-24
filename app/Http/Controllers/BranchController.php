<?php
/**
 * ICTHospital - hospital branches, which users work at each, and the session switch.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\HospitalBranch;
use App\Models\User;
use App\Support\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BranchController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('app.branches.index', [
            'branches' => HospitalBranch::orderBy('name')->get(),
            'users' => User::orderBy('firstname')->get(['id', 'firstname', 'lastname', 'login', 'group', 'branch_id']),
        ]);
    }

    public function store(Request $request)
    {
        HospitalBranch::create($this->validated($request) + ['active' => true]);

        return redirect('/hospital-branches')->with('success', 'Branch added.');
    }

    public function update(Request $request, $id)
    {
        $branch = HospitalBranch::findOrFail($id);
        $branch->update($this->validated($request, $branch->id) + ['active' => $request->boolean('active')]);

        return redirect('/hospital-branches')->with('success', 'Saved ' . $branch->name . '.');
    }

    /** Ties the ticked users to this branch. Users unticked here are freed to work across branches. */
    public function assign(Request $request, $id)
    {
        $branch = HospitalBranch::findOrFail($id);
        $ids = array_map('intval', (array) $request->input('users', []));
        User::where('branch_id', $branch->id)->whereNotIn('id', $ids ?: [0])->update(['branch_id' => null]);
        if ($ids) {
            User::whereIn('id', $ids)->update(['branch_id' => $branch->id]);
        }

        return redirect('/hospital-branches')->with('success', count($ids) . ' user(s) work at ' . $branch->name . '.');
    }

    /** Picks the branch for this session. Only for users who are not tied to a branch. */
    public function switch(Request $request)
    {
        if (! Branch::canSwitch()) {
            return back()->with('error', 'You work at one branch only.');
        }
        $id = (int) $request->input('branch_id', 0);
        if ($id && HospitalBranch::where('active', true)->whereKey($id)->exists()) {
            session(['branch_id' => $id]);
        } else {
            session()->forget('branch_id');
        }

        return back();
    }

    private function validated(Request $request, $ignore = null)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('hospital_branch', 'name')->ignore($ignore)],
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:60',
        ]);

        return ['name' => $data['name'], 'address' => $data['address'] ?? null, 'phone' => $data['phone'] ?? null];
    }
}
