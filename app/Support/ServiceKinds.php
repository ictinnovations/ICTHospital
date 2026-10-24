<?php
/**
 * ICTHospital - kinds of billable service, for reporting.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Support;

class ServiceKinds
{
    public const KINDS = [
        'consultation' => 'Consultation', 'procedure' => 'Procedure', 'lab' => 'Laboratory', 'radiology' => 'Radiology',
        'dialysis' => 'Dialysis', 'ot' => 'Operation theatre', 'room' => 'Room and ward', 'package' => 'Package',
        'pharmacy' => 'Pharmacy', 'donation' => 'Donation', 'misc' => 'Miscellaneous',
    ];

    public static function label($kind)
    {
        return self::KINDS[$kind] ?? 'Other';
    }
}
