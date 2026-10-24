<?php
/**
 * ICTHospital - a weekly clinic window for a doctor, cut into booking slots.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSlot extends Model
{
    protected $table = 'doctor_slot';

    protected $fillable = ['doctor_id', 'weekday', 'start_time', 'end_time', 'slot_minutes', 'active'];

    protected $casts = ['active' => 'boolean', 'weekday' => 'integer', 'slot_minutes' => 'integer'];

    public const DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
}
