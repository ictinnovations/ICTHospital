<?php
/**
 * ICTHospital - a reusable consultation note template.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultTemplate extends Model
{
    protected $table = 'consult_template';

    protected $fillable = ['name', 'doctor_id', 'fields'];

    protected $casts = ['fields' => 'array'];
}
