<?php
/**
 * ICTHospital - the test catalogue.
 *
 * Lab requests are raised against this list, so nothing could be requested until
 * there was a way to fill it. Each entry carries the reference range, which is
 * copied onto a test when it is requested rather than read live, because a result
 * has to be interpretable against the range that applied when it was measured.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\LabCategory;
use App\Models\LabTest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LabCategoryController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('app.lab.catalogue', [
            'catalogue' => LabCategory::orderBy('category')->get(),
            'usage' => LabTest::selectRaw('lab_category_id, count(*) as n')
                ->groupBy('lab_category_id')->pluck('n', 'lab_category_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        LabCategory::create($data);

        return redirect('/lab/catalogue')->with('success', $data['category'] . ' added.');
    }

    public function update(Request $request, $id)
    {
        $category = LabCategory::findOrFail($id);
        $category->update($this->validated($request, $category->id));

        return redirect('/lab/catalogue')->with('success', 'Test updated.');
    }

    /**
     * A test that has been requested stays in the catalogue.
     *
     * Requests copy the name and range in, so the record survives, but removing
     * the entry loses the description and the procedure reference that a
     * technician may need when reading an old result.
     */
    public function destroy($id)
    {
        $category = LabCategory::findOrFail($id);

        if (LabTest::where('lab_category_id', $category->id)->exists()) {
            return redirect('/lab/catalogue')
                ->with('error', $category->category . ' has been requested, so it stays in the catalogue.');
        }

        $name = $category->category;
        $category->delete();

        return redirect('/lab/catalogue')->with('success', $name . ' removed.');
    }

    private function validated(Request $request, $ignoreId = null)
    {
        $data = $request->validate([
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:100',
            'reference_value' => 'nullable|string|max:1000',
            'procedure_id' => 'nullable|string|max:100',
        ]);

        $clash = LabCategory::where('category', $data['category'])
            ->when($ignoreId, fn ($q) => $q->where('id', '<>', $ignoreId))
            ->exists();

        if ($clash) {
            throw ValidationException::withMessages([
                'category' => 'That test is already in the catalogue.',
            ]);
        }

        return [
            'category' => $data['category'],
            'description' => $data['description'] ?? null,
            'reference_value' => $data['reference_value'] ?? null,
            'procedure_id' => $data['procedure_id'] ?? null,
        ];
    }
}
