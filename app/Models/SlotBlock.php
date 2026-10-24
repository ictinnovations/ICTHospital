<?php
/**
 * ICTHospital - a doctor unavailable on a date: a whole day off or part of one.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlotBlock extends Model
{
    protected $table = 'slot_block';

    protected $fillable = ['doctor_id', 'date', 'start_time', 'end_time', 'reason'];
}
