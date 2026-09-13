<?php
/**
 * ICTHospital - payment gateway credentials.
 *
 * The paymentGateway table came across from the CodeIgniter system with a model and no
 * screen, so the credentials could only be put in by editing the database directly.
 *
 * Be clear about what this is and is not. Nothing in this release charges a card. The
 * gateway settings are stored so they are ready for the online payment and deposit work
 * that is still to come, and the screen says so rather than implying a card can be
 * taken today.
 *
 * The secrets are write only. They are never rendered back into the form, because a
 * settings page that prints an API password into the HTML puts it in every browser
 * cache and every screenshot. Leaving a secret field blank keeps the stored value.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Http\Controllers;

use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymentGatewayController extends BaseController
{
    /** Fields never sent back to the browser once stored. */
    private const SECRETS = ['merchant_key', 'salt', 'APIUsername', 'APIPassword', 'APISignature'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('app.gateways.index', [
            'gateways' => PaymentGateway::orderBy('name')->get(),
            'secrets' => self::SECRETS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $gateway = PaymentGateway::create($data);

        return redirect('/gateways')->with('success', $gateway->name . ' added.');
    }

    public function update(Request $request, $id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        $data = $this->validated($request, $gateway->id);

        // A blank secret means "leave the stored one alone", not "clear it". Someone
        // correcting a typo in the gateway name should not have to retype an API
        // signature they may not have to hand.
        foreach (self::SECRETS as $field) {
            if (($data[$field] ?? '') === '') {
                unset($data[$field]);
            }
        }

        $gateway->update($data);

        return redirect('/gateways')->with('success', $gateway->name . ' updated.');
    }

    /** Only one gateway is in use at a time, so enabling one disables the rest. */
    public function enable($id)
    {
        $gateway = PaymentGateway::findOrFail($id);

        PaymentGateway::query()->update(['status' => 'disabled']);
        $gateway->update(['status' => 'enabled']);

        return redirect('/gateways')->with('success', $gateway->name . ' is now the active gateway.');
    }

    public function disable($id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        $gateway->update(['status' => 'disabled']);

        return redirect('/gateways')->with('success', $gateway->name . ' disabled.');
    }

    public function destroy($id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        $name = $gateway->name;
        $gateway->delete();

        return redirect('/gateways')->with('success', 'Removed ' . $name . '.');
    }

    private function validated(Request $request, $ignoreId = null)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'merchant_key' => 'nullable|string|max:100',
            'salt' => 'nullable|string|max:100',
            'APIUsername' => 'nullable|string|max:100',
            'APIPassword' => 'nullable|string|max:100',
            'APISignature' => 'nullable|string|max:100',
        ]);

        $clash = PaymentGateway::where('name', $data['name'])
            ->when($ignoreId, fn ($q) => $q->where('id', '<>', $ignoreId))
            ->exists();

        if ($clash) {
            throw ValidationException::withMessages([
                'name' => 'A gateway with that name is already configured.',
            ]);
        }

        foreach (self::SECRETS as $field) {
            $data[$field] = $data[$field] ?? '';
        }

        // Legacy NOT NULL filler columns, unused leftovers from the old system.
        $data['x'] = '';
        $data['y'] = '';

        // status is owned by enable() and disable(), never by the edit form. Setting
        // it here would quietly switch off the active gateway every time somebody
        // corrected a typo in its name.
        if (! $ignoreId) {
            $data['status'] = 'disabled';
        }

        return $data;
    }
}
