<?php
/**
 * ICTHospital - bed register.
 *
 * Beds and bed categories came across in the rebuild with tables, models and
 * migrations but no way to see or edit them, so a ward could not be set up and
 * therefore nobody could be admitted to one.
 *
 * A bed's `status` is derived from the admission record rather than typed. In the
 * system this replaces it was a free text field a clerk set by hand, and it drifted
 * out of step with reality the first time somebody discharged a patient without
 * remembering to change it.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\AllotedBed;
use App\Models\Bed;
use App\Models\BedCategory;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BedController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** The ward board: every bed, which are free, and who is in the rest. */
    public function index(Request $request)
    {
        $categoryId = $request->input('category', '');

        $beds = Bed::query()
            ->when($categoryId !== '', fn ($q) => $q->where('category', $categoryId))
            ->orderBy('category')
            ->orderBy('number')
            ->get();

        $occupants = AllotedBed::occupied()->get()->keyBy('bed_id');
        $patients = Patient::whereIn('id', $occupants->pluck('patient')->filter())
            ->pluck('name', 'id');

        return view('app.beds.index', [
            'beds' => $beds,
            'categories' => BedCategory::orderBy('category')->get(),
            'categoryId' => $categoryId,
            'occupants' => $occupants,
            'patients' => $patients,
            'free' => $beds->reject(fn ($bed) => $occupants->has((string) $bed->id))->count(),
        ]);
    }

    public function create()
    {
        return view('app.beds.form', [
            'bed' => new Bed(),
            'categories' => BedCategory::orderBy('category')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $bed = Bed::create($data + ['bed_id' => '', 'last_a_time' => null, 'last_d_time' => null]);

        return redirect('/beds')->with('success', 'Bed ' . $bed->number . ' added.');
    }

    public function edit($id)
    {
        return view('app.beds.form', [
            'bed' => Bed::findOrFail($id),
            'categories' => BedCategory::orderBy('category')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $bed = Bed::findOrFail($id);
        $bed->update($this->validated($request, $bed->id));

        return redirect('/beds')->with('success', 'Bed ' . $bed->number . ' updated.');
    }

    /** A bed with someone in it cannot be removed; discharge them first. */
    public function destroy($id)
    {
        $bed = Bed::findOrFail($id);

        if (AllotedBed::occupied()->where('bed_id', $bed->id)->exists()) {
            return redirect('/beds')
                ->with('error', 'Bed ' . $bed->number . ' is occupied. Discharge the patient first.');
        }

        $number = $bed->number;
        $bed->delete();

        return redirect('/beds')->with('success', 'Bed ' . $number . ' removed.');
    }

    /* ----------------------------------------------------------- categories */

    public function categories()
    {
        return view('app.beds.categories', [
            'categories' => BedCategory::orderBy('category')->get(),
            'counts' => Bed::selectRaw('category, count(*) as n')->groupBy('category')->pluck('n', 'category'),
        ]);
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:100',
        ]);

        BedCategory::create([
            'category' => $data['category'],
            'description' => $data['description'] ?? '',
        ]);

        return redirect('/beds/categories')->with('success', 'Ward type added.');
    }

    public function destroyCategory($id)
    {
        $category = BedCategory::findOrFail($id);

        if (Bed::where('category', $category->id)->exists()) {
            return redirect('/beds/categories')
                ->with('error', 'That ward type still has beds in it.');
        }

        $category->delete();

        return redirect('/beds/categories')->with('success', 'Ward type removed.');
    }

    /**
     * Bed numbers are unique within a ward type, not across the hospital, because
     * every ward numbers its own beds from one.
     */
    private function validated(Request $request, $ignoreId = null)
    {
        $data = $request->validate([
            'category' => 'required|integer|exists:bed_category,id',
            'number' => 'required|string|max:100',
            'description' => 'nullable|string|max:100',
        ]);

        $clash = Bed::where('category', $data['category'])
            ->where('number', $data['number'])
            ->when($ignoreId, fn ($q) => $q->where('id', '<>', $ignoreId))
            ->exists();

        if ($clash) {
            throw ValidationException::withMessages([
                'number' => 'That ward already has a bed numbered ' . $data['number'] . '.',
            ]);
        }

        return [
            'category' => $data['category'],
            'number' => $data['number'],
            'description' => $data['description'] ?? '',
            'status' => 'free',
        ];
    }
}
