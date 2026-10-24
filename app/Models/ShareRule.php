<?php
/**
 * ICTHospital - one rule of a share policy.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareRule extends Model
{
    protected $table = 'share_rule';

    protected $fillable = ['share_policy_id', 'payment_category_id', 'department', 'patient_type_id', 'doctor_percent', 'referral_percent'];

    protected $casts = ['doctor_percent' => 'decimal:2', 'referral_percent' => 'decimal:2'];

    /**
     * Does this rule apply to a line? Every field the rule sets has to match.
     * Returns how specific the match is (service beats department beats patient
     * type), or -1 when it does not apply.
     */
    public function score($serviceId, $department, $patientTypeId)
    {
        if ($this->payment_category_id && (int) $this->payment_category_id !== (int) $serviceId) {
            return -1;
        }
        if ($this->department && strcasecmp(trim($this->department), trim((string) $department)) !== 0) {
            return -1;
        }
        if ($this->patient_type_id && (int) $this->patient_type_id !== (int) $patientTypeId) {
            return -1;
        }

        return ($this->payment_category_id ? 4 : 0) + ($this->department ? 2 : 0) + ($this->patient_type_id ? 1 : 0);
    }
}
