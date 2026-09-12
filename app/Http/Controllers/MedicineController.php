<?php
/**
 * ICTHospital - the medicine catalogue.
 *
 * Prescriptions and the pharmacy both read this list, and neither could be built
 * without a way to put anything in it. Stock movement and dispensing are the
 * pharmacy module's job; this is only the catalogue and the quantity on hand.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineCategory;
use App\Models\PrescriptionMedicine;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MedicineController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $term = trim((string) $request->input('q', ''));
        $categoryId = $request->input('category', '');

        $medicines = Medicine::query()
            ->when($term !== '', function ($q) use ($term) {
                $like = '%' . $term . '%';
                $q->where('name', 'like', $like)
                    ->orWhere('generic', 'like', $like)
                    ->orWhere('company', 'like', $like);
            })
            ->when($categoryId !== '', fn ($q) => $q->where('category', $categoryId))
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        return view('app.medicines.index', [
            'medicines' => $medicines,
            'categories' => MedicineCategory::orderBy('category')->get(),
            'term' => $term,
            'categoryId' => $categoryId,
        ]);
    }

    public function create()
    {
        return view('app.medicines.form', [
            'medicine' => new Medicine(['quantity' => 0]),
            'categories' => MedicineCategory::orderBy('category')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $medicine = Medicine::create($this->validated($request) + ['add_date' => now()->toDateString()]);

        return redirect('/medicines')->with('success', $medicine->name . ' added to the catalogue.');
    }

    public function edit($id)
    {
        return view('app.medicines.form', [
            'medicine' => Medicine::findOrFail($id),
            'categories' => MedicineCategory::orderBy('category')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);
        $medicine->update($this->validated($request, $medicine->id));

        return redirect('/medicines')->with('success', $medicine->name . ' updated.');
    }

    /**
     * A drug that has been prescribed is never deleted.
     *
     * Prescriptions copy the name in, so the record would survive, but removing
     * the catalogue row breaks the link back and loses the generic name, company
     * and strength that a pharmacist may need to read later.
     */
    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);

        if (PrescriptionMedicine::where('medicine_id', $medicine->id)->exists()) {
            return redirect('/medicines')->with(
                'error',
                $medicine->name . ' has been prescribed, so it stays in the catalogue.'
            );
        }

        $name = $medicine->name;
        $medicine->delete();

        return redirect('/medicines')->with('success', $name . ' removed.');
    }

    /* ----------------------------------------------------------- categories */

    public function categories()
    {
        return view('app.medicines.categories', [
            'categories' => MedicineCategory::orderBy('category')->get(),
            'counts' => Medicine::selectRaw('category, count(*) as n')->groupBy('category')->pluck('n', 'category'),
        ]);
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:100',
        ]);

        MedicineCategory::create([
            'category' => $data['category'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect('/medicines/categories')->with('success', 'Category added.');
    }

    public function destroyCategory($id)
    {
        $category = MedicineCategory::findOrFail($id);

        if (Medicine::where('category', $category->id)->exists()) {
            return redirect('/medicines/categories')->with('error', 'That category still has medicines in it.');
        }

        $category->delete();

        return redirect('/medicines/categories')->with('success', 'Category removed.');
    }

    private function validated(Request $request, $ignoreId = null)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'nullable|integer|exists:medicine_category,id',
            'generic' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0',
            's_price' => 'nullable|numeric|min:0',
            'box' => 'nullable|string|max:100',
            'quantity' => 'nullable|integer|min:0',
            'effects' => 'nullable|string|max:100',
            'e_date' => 'nullable|date',
        ]);

        // Two rows with the same name and strength are a data entry slip, not a
        // second drug, and they make the prescribing list ambiguous.
        $clash = Medicine::where('name', $data['name'])
            ->where('box', $data['box'] ?? null)
            ->when($ignoreId, fn ($q) => $q->where('id', '<>', $ignoreId))
            ->exists();

        if ($clash) {
            throw ValidationException::withMessages([
                'name' => 'That medicine is already in the catalogue with the same pack size.',
            ]);
        }

        return [
            'name' => $data['name'],
            'category' => $data['category'] ?? null,
            'generic' => $data['generic'] ?? null,
            'company' => $data['company'] ?? null,
            'price' => $data['price'] ?? null,
            's_price' => $data['s_price'] ?? null,
            'box' => $data['box'] ?? null,
            'quantity' => (int) ($data['quantity'] ?? 0),
            'effects' => $data['effects'] ?? null,
            'e_date' => $data['e_date'] ?? null,
        ];
    }
}
