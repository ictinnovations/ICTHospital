<?php
/**
 * ICTHospital - what a visit type costs, for one doctor or for every doctor.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitFee extends Model
{
    protected $table = 'visit_fee';

    protected $fillable = ['doctor_id', 'visit_type', 'shift', 'payment_category_id', 'fee', 'active'];

    protected $casts = ['fee' => 'decimal:2', 'active' => 'boolean'];

    public const TYPES = ['first' => 'First visit', 'follow_up' => 'Follow-up', 'emergency' => 'Emergency'];

    public const SHIFTS = ['any' => 'Any time', 'morning' => 'Morning (before 2 pm)', 'evening' => 'Evening (2 pm on)'];

    /**
     * The fee row for a doctor, visit type and time: the doctor's own row beats the
     * all-doctors row, and a row for the shift beats an any-time row.
     */
    public static function resolve($doctorId, $visitType, \DateTimeInterface $at)
    {
        $shift = (int) $at->format('H') < 14 ? 'morning' : 'evening';

        return static::where('active', true)->where('visit_type', $visitType)
            ->where(fn ($q) => $q->where('doctor_id', $doctorId)->orWhereNull('doctor_id'))
            ->whereIn('shift', [$shift, 'any'])
            ->get()
            ->sortByDesc(fn ($r) => ($r->doctor_id ? 2 : 0) + ($r->shift !== 'any' ? 1 : 0))
            ->first();
    }
}
