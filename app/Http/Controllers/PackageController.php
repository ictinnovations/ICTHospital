<?php
/**
 * ICTHospital - what a service package covers.
 *
 * A package (a normal delivery, a dialysis course, a health check) is billed as one
 * line at its own price. The components are listed so the invoice shows the
 * patient what the price includes and the desk knows what to deliver.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\PaymentCategory;
use App\Models\ServicePackageItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PackageController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($id)
    {
        $package = PaymentCategory::findOrFail($id);
        $items = ServicePackageItem::where('package_id', $package->id)->get();
        $names = PaymentCategory::whereIn('id', $items->pluck('component_id'))->get()->keyBy('id');

        return view('app.services.package', [
            'package' => $package,
            'items' => $items,
            'names' => $names,
            'services' => PaymentCategory::where('id', '!=', $package->id)->orderBy('category')->get(['id', 'category', 'c_price']),
            'componentValue' => $items->sum(fn ($i) => (float) optional($names->get($i->component_id))->c_price * $i->quantity),
        ]);
    }

    public function store(Request $request, $id)
    {
        $package = PaymentCategory::findOrFail($id);
        $data = $request->validate([
            'component_id' => 'required|integer|exists:payment_category,id',
            'quantity' => 'nullable|integer|min:1|max:999',
        ]);
        if ((int) $data['component_id'] === $package->id) {
            throw ValidationException::withMessages(['component_id' => 'A package cannot contain itself.']);
        }

        if ($package->service_kind !== 'package') {
            $package->update(['service_kind' => 'package']);
        }
        ServicePackageItem::create(['package_id' => $package->id, 'component_id' => $data['component_id'], 'quantity' => $data['quantity'] ?? 1]);

        return redirect('/services/' . $package->id . '/package')->with('success', 'Added to the package.');
    }

    public function destroy($id)
    {
        $item = ServicePackageItem::findOrFail($id);
        $item->delete();

        return redirect('/services/' . $item->package_id . '/package')->with('success', 'Removed from the package.');
    }
}
