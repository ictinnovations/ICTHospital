<?php
/**
 * ICTHospital - credentials for an external accounting API.
 *
 * One row. The model shipped empty, with no table name, no fillable list and no
 * migration behind it, so the accounting page could neither read it nor build a
 * blank one to render the form.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountingSetting extends Model
{
    protected $table = 'accounting_settings';

    protected $fillable = [
        'company_id',
        'api_link',
        'username',
        'password',
    ];

    /**
     * The API password is a secret, keep it out of arrays and JSON.
     */
    protected $hidden = [
        'password',
    ];
}
