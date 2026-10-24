<?php
/**
 * ICTHospital - one service included in a package.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePackageItem extends Model
{
    protected $table = 'service_package_item';

    protected $fillable = ['package_id', 'component_id', 'quantity'];
}
