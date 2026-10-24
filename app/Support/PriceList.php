<?php
/**
 * ICTHospital - what a service costs this patient, here.
 *
 * Looks up the patient type's price for the care setting (outpatient or inpatient),
 * and falls back to the service's list price when that type has no price set. One
 * place answers it so the invoice form, the server-side check and the OPD visit fee
 * can never disagree.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Support;

use App\Models\PaymentCategory;
use App\Models\ServicePrice;

class PriceList
{
    public const SETTINGS = ['opd' => 'Outpatient (OPD)', 'ipd' => 'Inpatient (IPD)'];

    public static function priceFor(PaymentCategory $service, $patientTypeId = null, $setting = 'opd')
    {
        $setting = $setting === 'ipd' ? 'ipd' : 'opd';

        if ($patientTypeId) {
            $row = ServicePrice::where('payment_category_id', $service->id)
                ->where('patient_type_id', $patientTypeId)->first();
            $price = $row ? $row->{$setting . '_price'} : null;

            if ($price !== null && $price !== '') {
                return round((float) $price, 2);
            }
        }

        return round((float) $service->c_price, 2);
    }

    /**
     * Every set price as [type id][setting][service id] => price, for the invoice
     * form's script to fill a line the moment a service is picked.
     */
    public static function matrix()
    {
        $out = [];

        foreach (ServicePrice::all() as $row) {
            foreach (['opd', 'ipd'] as $setting) {
                $price = $row->{$setting . '_price'};
                if ($price !== null) {
                    $out[$row->patient_type_id][$setting][$row->payment_category_id] = (float) $price;
                }
            }
        }

        return $out;
    }
}
