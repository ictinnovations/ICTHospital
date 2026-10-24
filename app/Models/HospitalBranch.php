<?php
/**
 * ICTHospital - one site of a multi-branch hospital.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalBranch extends Model
{
    protected $table = 'hospital_branch';

    protected $fillable = ['name', 'address', 'phone', 'active'];

    protected $casts = ['active' => 'boolean'];
}
