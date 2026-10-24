<?php
/**
 * ICTHospital - a patient type: General, a panel company, charity, an online platform.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientType extends Model
{
    protected $table = 'patient_type';

    protected $fillable = ['name', 'kind', 'company', 'active'];

    protected $casts = ['active' => 'boolean'];

    /** Report groupings. Private covers general walk-ins and online bookings. */
    public const KINDS = [
        'general' => 'General (private)',
        'panel' => 'Panel company',
        'charity' => 'Charity',
        'online' => 'Online booking platform',
        'staff' => 'Staff',
    ];

    /** The type a patient falls back to when none is set: the first General one. */
    public static function defaultId()
    {
        return static::where('kind', 'general')->orderBy('id')->value('id');
    }

    /** Private, Panel or Charity, the three columns a day book is split into. */
    public static function bucket($kind)
    {
        if ($kind === 'panel') {
            return 'Panel';
        }

        return $kind === 'charity' ? 'Charity' : 'Private';
    }
}
