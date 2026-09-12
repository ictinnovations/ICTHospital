<?php
/**
 * ICTHospital - one drug on one prescription.
 *
 * The name is stored alongside medicine_id on purpose. A prescription is a
 * clinical record of what was written on a given day, so it has to stay readable
 * even if the drug is renamed or dropped from the catalogue afterwards.
 *
 * Copyright (c) ICT Innovations <https://www.ictinnovations.com>
 * Part of ICTHospital <https://www.icthospital.com>
 * Licensed under the GNU General Public License v3.0.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionMedicine extends Model
{
    use HasFactory;

    protected $table = 'prescription_medicine';
    public $timestamps = false;

    protected $fillable = [
        'prescription_id',
        'medicine_id',
        'name',
        'dosage',
        'duration',
        'instructions',
    ];

    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class, 'prescription_id');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class, 'medicine_id');
    }

    /** One line as it reads on paper, used for the legacy summary column. */
    public function summary(): string
    {
        return trim(implode(' ', array_filter([
            $this->name,
            $this->dosage,
            $this->duration ? 'for ' . $this->duration : null,
        ])));
    }
}
