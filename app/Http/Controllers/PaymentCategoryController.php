<?php
/**
 * ICTHospital - billable services and their price list.
 *
 * Invoice lines are raised against this table. Until now there was no screen for
 * it, so a clean install had an empty service list and the invoice form offered
 * an empty select. Together with the doctor register this was the reason the
 * clinical modules could not be used on a fresh deployment.
 *
 * The price here is the current one. It is copied onto an invoice line when the
 * charge is raised rather than read back later, so editing a price never rewrites
 * what a patient was billed. Same reasoning as the lab and medicine catalogues,
 * and there is a test for it.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use App\Models\PaymentCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentCategoryController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $services = PaymentCategory::orderBy('type')->orderBy('category')->get();

        return view('app.services.index', [
            'services' => $services,
            'types' => $services->pluck('type')->filter()->unique()->sort()->values(),
            // How many invoice lines reference each service, so the person
            // editing a price can see what is already out there against it.
            'usage' => InvoiceItem::selectRaw('payment_category_id, count(*) as n')
                ->whereNotNull('payment_category_id')
                ->groupBy('payment_category_id')
                ->pluck('n', 'payment_category_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        PaymentCategory::create($data);

        return redirect('/services')->with('success', $data['category'] . ' added to the price list.');
    }

    public function update(Request $request, $id)
    {
        $service = PaymentCategory::findOrFail($id);
        $service->update($this->validated($request, $service->id));

        return redirect('/services')->with('success', $service->category . ' updated.');
    }

    /**
     * A service that has been invoiced stays on the list.
     *
     * The line keeps its own copy of the name and price, so an old invoice still
     * reads correctly, but the invoice item row holds a payment_category_id and
     * removing the parent would leave it pointing at nothing.
     */
    public function destroy($id)
    {
        $service = PaymentCategory::findOrFail($id);

        if (InvoiceItem::where('payment_category_id', $service->id)->exists()) {
            return redirect('/services')
                ->with('error', $service->category . ' has been invoiced, so it stays on the price list.');
        }

        $name = $service->category;
        $service->delete();

        return redirect('/services')->with('success', $name . ' removed.');
    }

    private function validated(Request $request, $ignoreId = null)
    {
        $data = $request->validate([
            'category' => 'required|string|max:100',
            'description' => 'nullable|string|max:100',
            'c_price' => 'nullable|numeric|min:0',
            'type' => 'nullable|string|max:100',
            'd_commission' => 'nullable|integer|min:0|max:100',
            'h_commission' => 'nullable|integer|min:0|max:100',
        ]);

        $clash = PaymentCategory::where('category', $data['category'])
            ->when($ignoreId, fn ($q) => $q->where('id', '<>', $ignoreId))
            ->exists();

        if ($clash) {
            throw ValidationException::withMessages([
                'category' => 'That service is already on the price list.',
            ]);
        }

        // The price column is a varchar in the legacy schema. Normalising to two
        // decimal places here keeps the list sortable and stops "500" and
        // "500.00" appearing as two different prices for the same service.
        $price = $data['c_price'] ?? null;

        return [
            'category' => $data['category'],
            'description' => $data['description'] ?? null,
            'c_price' => ($price === null || $price === '') ? null : number_format((float) $price, 2, '.', ''),
            'type' => $data['type'] ?? null,
            'd_commission' => $data['d_commission'] ?? null,
            'h_commission' => $data['h_commission'] ?? null,
        ];
    }
}
